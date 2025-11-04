<?php
// namespace 작성
namespace Tripmate\Backend\Modules\ScheduleItems\Controllers;

// use 작성
use Tripmate\Backend\Common\Middleware\AuthMiddleware;
use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Core\DB;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;
use Tripmate\Backend\Modules\ScheduleItems\Services\ScheduleItemsService;

// ScheduleItemsController 작성
class ScheduleItemsController extends Controller
{
    private ScheduleItemsService $service;
    private Validator $validator;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        
        // DB 커넥션 및 서비스, 밸리데이터 초기화
        $pdo = DB::conn();
        $this->service   = new ScheduleItemsService($pdo);
        $this->validator = new Validator();
    }

    // 1. 일정 아이템 생성 : POST /api/v1/trips/{trip_id}/days/{day_no}/items
    public function createScheduleItem(): void
    {
        $this->run(function () {
            // 1-1. 경로 파라미터 추출
            $rawTrip = $this->request->getAttribute('trip_id');
            $rawDay  = $this->request->getAttribute('day_no');
            $tripId  = (int) $rawTrip;
            $dayNo   = (int) $rawDay;

            // 1-2. 경로 파라미터 검증
            if ($tripId <= 0) {
                $this->response->error('INVALID_TRIP_ID', '유효하지 않은 trip_id입니다.', 400);
                return null;
            }
            if ($dayNo <= 0) {
                $this->response->error('INVALID_DAY_NO', '유효하지 않은 day_no입니다.', 400);
                return null;
            }

            // 1-3. 인증 토큰 검사
            $userId = AuthMiddleware::tokenResponse($this->request);
            if (!$userId) {
                $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
                return null;
            }

            // 1-4. 요청 바디 파싱
            $body = (array) $this->request->body();
            $placeId   = isset($body['place_id']) ? (int)$body['place_id'] : null;
            $visitTime = $body['visit_time'] ?? null;
            $memo      = $body['memo'] ?? null;

            // 1-5. 서비스 호출
            $itemId = $this->service->createScheduleItem(
                (int)$userId,
                (int)$tripId,
                (int)$dayNo,
                $placeId,
                $visitTime,
                $memo
            );

            // 1-6. 성공 응답 (201 Created)
            $this->response->created([
                'item_id'    => (int) $itemId,
                'trip_id'    => (int) $tripId,
                'day_no'     => (int) $dayNo,
                'place_id'   => $placeId,
                'visit_time' => $visitTime,
                'memo'       => $memo,
            ]);
            return null;
        });
    }

    // 2. 일정 아이템 목록 조회 : GET /api/v1/trips/{trip_id}/days/{day_no}/items
    public function getScheduleItems(): void
    {
        $this->run(function () {
            // 2-1. 경로 파라미터 추출 및 검증
            $tripId = (int) $this->request->getAttribute('trip_id');
            $dayNo  = (int) $this->request->getAttribute('day_no');

            if ($tripId <= 0 || $dayNo <= 0) {
                $this->response->error('INVALID_PARAMETER', '유효하지 않은 경로 파라미터입니다.', 400);
                return null;
            }

            // 2-2. 인증 검사
            $userId = AuthMiddleware::tokenResponse($this->request);
            if (!$userId) {
                $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
                return null;
            }

            // 2-3. 서비스 호출
            $items = $this->service->getScheduleItems((int)$userId, (int)$tripId, (int)$dayNo);

            // 2-4. 성공 응답
            $this->response->success([
                'trip_id' => (int)$tripId,
                'day_no'  => (int)$dayNo,
                'items'   => $items,
            ]);
            return null;
        });
    }

    // 3. 일정 아이템 수정 : PATCH /api/v1/trips/{trip_id}/days/{day_no}/items/{item_id}
    public function updateScheduleItem(): void
    {
        $this->run(function () {
            // 3-1. 경로 파라미터 추출
            $tripId = (int)$this->request->getAttribute('trip_id');
            $dayNo  = (int)$this->request->getAttribute('day_no');
            $itemId = (int)$this->request->getAttribute('item_id');

            // 3-2. 검증
            if ($tripId <= 0 || $dayNo <= 0 || $itemId <= 0) {
                $this->response->error('INVALID_PARAMETER', '요청 경로 파라미터가 유효하지 않습니다.', 400);
                return null;
            }

            // 3-3. 인증
            $userId = AuthMiddleware::tokenResponse($this->request);
            if (!$userId) {
                $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
                return null;
            }

            // 3-4. 요청 바디 파싱
            $body = (array) $this->request->body();
            $visitTime = $body['visit_time'] ?? null;
            $memo      = $body['memo'] ?? null;

            // 3-5. 서비스 호출
            $updated = $this->service->updateScheduleItem(
                (int)$userId,
                (int)$tripId,
                (int)$itemId,
                (int)$dayNo,
                $visitTime,
                $memo
            );

            // 3-6. 성공 응답
            $this->response->success([
                'trip_id'    => (int)$tripId,
                'day_no'     => (int)$dayNo,
                'item_id'    => (int)$itemId,
                'visit_time' => $updated['visit_time'] ?? $visitTime,
                'memo'       => $updated['memo'] ?? $memo,
            ]);
            return null;
        });
    }

    // 4. 일정 아이템 삭제 : DELETE /api/v1/trips/{trip_id}/days/{day_no}/items/{item_id}
    public function deleteScheduleItem(): void
    {
        $this->run(function () {
            // 4-1. 경로 파라미터 추출
            $tripId = (int)$this->request->getAttribute('trip_id');
            $dayNo  = (int)$this->request->getAttribute('day_no');
            $itemId = (int)$this->request->getAttribute('item_id');

            // 4-2. 검증
            if ($tripId <= 0 || $dayNo <= 0 || $itemId <= 0) {
                $this->response->error('INVALID_PARAMETER', '요청 경로 파라미터가 유효하지 않습니다.', 400);
                return null;
            }

            // 4-3. 인증
            $userId = AuthMiddleware::tokenResponse($this->request);
            if (!$userId) {
                $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
                return null;
            }

            // 4-4. 서비스 호출
            $deleted = $this->service->deleteScheduleItem(
                (int)$userId,
                (int)$tripId,
                (int)$dayNo,
                (int)$itemId
            );

            // 4-5. 실패 처리
            if ($deleted === false) {
                $this->response->error('DELETE_FAILED', '일정 삭제에 실패했습니다.', 500);
                return null;
            }

            // 4-6. 성공 (204 No Content)
            $this->response->noContent();
            return null;
        });
    }

    // 5. 일정 아이템 순서 재배치 : POST /api/v1/trips/{trip_id}/days/{day_no}/items:reorder
    public function reorderSingleScheduleItem(): void
    {
        $this->run(function () {
            // 5-1. 경로 파라미터 추출
            $tripId = (int)$this->request->getAttribute('trip_id');
            $dayNo  = (int)$this->request->getAttribute('day_no');

            // 5-2. 검증
            if ($tripId <= 0 || $dayNo <= 0) {
                $this->response->error('INVALID_PARAMETER', '요청 경로 파라미터가 유효하지 않습니다.', 400);
                return null;
            }

            // 5-3. 인증
            $userId = AuthMiddleware::tokenResponse($this->request);
            if (!$userId) {
                $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
                return null;
            }

            // 5-4. 요청 바디 파싱
            $body     = (array) $this->request->body();
            $itemId   = isset($body['item_id']) ? (int)$body['item_id'] : 0;
            $newSeqNo = isset($body['new_seq_no']) ? (int)$body['new_seq_no'] : 0;

            if ($itemId <= 0) {
                $this->response->error('INVALID_ITEM_ID', 'item_id가 필요합니다.', 400);
                return null;
            }

            // 5-5. 서비스 호출
            $reordered = $this->service->reorderSingleScheduleItem(
                (int)$userId,
                (int)$tripId,
                (int)$dayNo,
                (int)$itemId,
                (int)$newSeqNo
            );

            // 5-6. 성공 응답
            $this->response->success([
                'trip_id' => (int)$tripId,
                'day_no'  => (int)$dayNo,
                'items'   => $reordered,
            ]);
            return null;
        });
    }
}
