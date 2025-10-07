<?php
namespace Tripmate\Backend\Modules\Trips\Controllers;

use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Modules\Trips\Services\TripsService;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;

// 1. TripController 클래스 정의
class TripsController extends Controller {
  // 2. 프러퍼티 정의
  public TripsService $tripsService;
  public Validator $validator;

  // 3. 생성자에서 Request, Response, TripsService 초기화 
  public function __construct(Request $request, Response $response) {
    // 3-1. 부모 생성자 호출
    parent::__construct($request, $response);
    // 3-2. TripsService 인스턴스 생성
    $this->tripsService = new TripsService();
    // 3-3. Validator 인스턴스 생성
    $this->validator = new Validator();
  }

  // 4. 여행 생성 : POST /api/v1/trips
  // 4-1. createTrip 메서드 정의
  public function createTrip() {
    // 4-1. 요청 데이터 가져오기
    $body = $this->request->body ?? [];

    // 4-2. 유효성 검증
    $validationResult = $this->validator->ValidationTrip($body);
    if ($validationResult !== true) {
        return $this->response->error('VALIDATION_ERROR', $validationResult, 422);
    }

    // 4-3. 임시: 로그인 미구현 상태 -> userId를 1로 고정
    $userId = 1;

    // 4-4. TripsService의 createTrip 호출
    $tripId = $this->tripsService->createTrip(
        (int)$userId,
        (int)$body['region_id'],
        $body['title'],
        $body['start_date'],
        $body['end_date']
    );

    // 4-5. 실패 시 응답
    if ($tripId === false) {
        return $this->response->error('CREATION_FAILED', '여행 생성에 실패했습니다.', 500);
    }

    // 4-6. 성공 시 응답 (생성된 trip_id 반환)
    return $this->response->success(
        ['trip_id' => $tripId],
        201
    );


}

  // 5. 여행 목록 조회 : GET /api/v1/trips -> 페이지네이션 적용
  // 5-1. getTrips 메서드 정의 
  public function getTrips() {
    // 5-2 페이지와 페이지당 항목 수 가져오기
    $q = $this->request->query ?? [];
   
  }

  // 6. 여행 단건 조회 : GET /api/v1/trips/{trip_id}
  // 6-1. OneTrip 메서드 정의
  public function OneTrip() {
    // 6-2. 경로 매개변수에서 trip_id 가져오기
    $tripId = $this->request->params['trip_id'] ?? null;
    // 6-3. trip_id가 없으면 400 응답
    if ($tripId == 0 || !is_numeric($tripId) || $tripId <= 0) {
      return $this->response->error('INVALID_TRIP_ID', '유효하지 않은 trip_id입니다.', 400);
    }
    // 6-4. TripsService의 findTripById 호출
    $trip = $this->tripsService->findTripById((int)$tripId);

    // 6-5. 조회 실패 시 404 응답
    if ($trip === false) {
      return $this->response->error('NOT_FOUND', '해당 trip_id의 여행을 찾을 수 없습니다.', 404);
    }
    // 6-6. 성공 시 여행 데이터 반환
    return $this->response->success($trip, 200);
  }

  // 7. 여행 수정 : PUT /api/v1/trips/{trip_id}
  // 7-1. updateTrip 메서드 정의
  public function updateTrip() {}

  // 8. 여행 삭제 : DELETE /api/v1/trips/{trip_id}
  // 8-1. deleteTrip 메서드 정의
  public function deleteTrip() {}

}