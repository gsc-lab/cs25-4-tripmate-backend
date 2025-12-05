<?php
namespace Tripmate\Backend\Modules\Trips\Controllers;

use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Core\DB;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;
use Tripmate\Backend\Modules\Trips\Services\TripsService;

final class TripsController extends Controller
{
    private TripsService $tripsService;
    private Validator $validator;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        
        $pdo = DB::conn();
        $this->tripsService = new TripsService($pdo);
        $this->validator = new Validator();
    }

    // 1. Trip 생성 : POST /api/v1/trips
    public function createTrip(): void
    {
        $this->run(function () {
            // 1-1. 요청 바디
            $body = (array) $this->request->body();

            // 1-2. 유효성 검사 (Validator::validateTrip)
            $this->validator->validateTrip($body);

            // 1-3. 사용자 식별
            $userId = $this->getUserId();

            // 1-4. 서비스 호출
            $tripId = $this->tripsService->createTrip(
                $userId,
                (int) $body['region_id'],
                (string) $body['title'],
                (string) $body['start_date'],
                (string) $body['end_date']
            );

            // 1-5. 실패 처리
            if ($tripId <= 0) {
                $this->response->error('CREATION_FAILED', '여행 생성에 실패했습니다.', 500);
                return false;
            }

            // 1-6. 성공: 201 + Location 헤더
            $this->response->setHeader('Location', "/api/v1/trips/{$tripId}")
                        ->created([
                            'trip_id'    => (int) $tripId,
                            'title'      => (string) $body['title'],
                            'region_id'  => (int) $body['region_id'],
                            'start_date' => (string) $body['start_date'],
                            'end_date'   => (string) $body['end_date'],
                        ]);

            return [
                'trip_id' => $tripId
            ];
        });
    }

    // 2. Trip 목록 : GET /api/v1/trips?page&size&sort
    public function getTrips(): void
    {
        $this->run(function () {
            // 2-1. 페이징 파싱
            ['page' => $page, 'size' => $size, 'sort' => $sort] = $this->parsePaging();

            // 2-2. 조회
            $result = $this->tripsService->findTrips(
                $this->getUserId(),
                (int) $page,
                (int) $size,
                (string) $sort
            );

            // 2-3. 실패 처리
            if ($result === false) {
                $this->response->error('RETRIEVAL_FAILED', '여행 목록 조회에 실패했습니다.', 500);
                return false;
            }

            // 2-4. data/meta 분리 (스펙)
            $items = $result['items'] ?? [];
            $meta  = [
                'page'  => (int) ($result['page']  ?? $page),
                'size'  => (int) ($result['size']  ?? $size),
                'total' => (int) ($result['total'] ?? 0),
            ];

            // 2-5. 성공 응답
            $this->response->success($items, $meta);

            return [
                'items' => $items,
                'meta'  => $meta
            ];
        });
    }

    // 3. Trip 단건 : GET /api/v1/trips/{trip_id}
    public function showTrip(): void
    {
        $this->run(function () {

            $raw = $this->request->getAttribute('trip_id');
            $tripId = (is_string($raw) && ctype_digit($raw)) ? (int)$raw : (int)$raw;

            // 3-1. 경로 파라미터 검증
            if ($tripId <= 0) {
                $this->response->error('INVALID_TRIP_ID', '유효하지 않은 trip_id입니다.', 400);
                return false;
            }

            // 3-2. 조회
            $trip = $this->tripsService->findTripById((int) $tripId, $this->getUserId());

            // 3-3. 없으면 404
            if (empty($trip)) {
                $this->response->error('NOT_FOUND', '해당 여행을 찾을 수 없습니다.', 404);
                return false;
            }

            // 3-4. 성공
            $this->response->success($trip);

            return $trip;
        });
    }

    // 4. Trip 수정 : PUT /api/v1/trips/{trip_id}
    public function updateTrip(): void
    {
        $this->run(function ()  {

            $raw = $this->request->getAttribute('trip_id');
            $tripId = (is_string($raw) && ctype_digit($raw)) ? (int)$raw : (int)$raw;

            // 4-1. 경로 파라미터 검증
            if ($tripId <= 0) {
                $this->response->error('INVALID_TRIP_ID', '유효하지 않은 trip_id입니다.', 400);
                return false;
            }

            // 4-2. 요청 바디
            $body = (array) $this->request->body();

            // 4-3. 유효성 검사 (전체 업데이트 기준)
            $this->validator->validateTrip($body);

            // 4-4. 서비스 호출
            $updated = $this->tripsService->updateTrip(
                $this->getUserId(),
                (int) $tripId,
                (int) $body['region_id'],
                (string) $body['title'],
                (string) $body['start_date'],
                (string) $body['end_date']
            );

            // 4-5. 실패
            if ($updated === false) {
                $this->response->error('UPDATE_FAILED', '여행 수정에 실패했습니다.', 500);
                return false;
            }

            // 4-6. 성공
            $responseData = [
                'trip_id'    => (int) $tripId,
                'title'      => (string) $body['title'],
                'region_id'  => (int) $body['region_id'],
                'start_date' => (string) $body['start_date'],
                'end_date'   => (string) $body['end_date'],
            ];

            $this->response->success($responseData);

            return $responseData;
        });
    }

    // 5. Trip 삭제 : DELETE /api/v1/trips/{trip_id}
    public function deleteTrip(): void
    {
        $this->run(function ()  {
            $raw = $this->request->getAttribute('trip_id');
            $tripId = (is_string($raw) && ctype_digit($raw)) ? (int)$raw : (int)$raw;
            
            // 5-1. 경로 파라미터 검증
            if ($tripId <= 0) {
                $this->response->error('INVALID_TRIP_ID', '유효하지 않은 trip_id입니다.', 400);
                return false;
            }

            // 5-2. 삭제 실행
            $deleted = $this->tripsService->deleteTrip($this->getUserId(), (int) $tripId);

            // 5-3. 실패 처리
            if ($deleted === false) {
                $this->response->error('DELETION_FAILED', '여행 삭제에 실패했습니다.', 500);
                return false;
            }

            // 5-4. 성공: 204 No Content
            $this->response->noContent();

            return true;
        });
    }
}