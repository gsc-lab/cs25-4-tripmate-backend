<?php
namespace Tripmate\Backend\Modules\Trips\Controllers;

use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Modules\Trips\Services\TripsService;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;

// 1. TripController 클래스 정의
class TripsController extends Controller {
  // 2. 프러퍼티 정의 (services 메서드 구현 예정)
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
    // 4-2. 요청 데이터 가져오기 (값이 없는 경우 빈 배열로 초기화)
    $body = $this->request->body ?? [];

    // 4-3. 유효성 검증을 위한 validator 사용
    $varidationResult = $this->validator->ValidationTrip($body);
    
    if ($varidationResult !== true) {
      return $this->response->error('VALIDATION_ERROR', $varidationResult, 422);
    }

    // 4-4. TripsService의 createTrip 메서드 호출
    $trip = $this->tripsService->createTrip(
            $body['title'],
            (int)$body['region_id'], // (int)로 형변환
            $body['start_date'],
            $body['end_date'],
          );

    // 4-5. 실패 시 응답 반환
    if ($trip === false) {
      $this->response->error('CREATION_FAILED', '여행 생성에 실패했습니다.', 500);
      return;
    }
    
   // 4-6. 성공 시 응답 반환
    $this->response->success($trip, 201);
    
  }

  // 5. 여행 목록 조회 : GET /api/v1/trips -> 페이지네이션 적용
  // 5-1. getTrips 메서드 정의 
  public function getTrips() {
    // 5-2 페이지와 페이지당 항목 수 가져오기
    $q = $this->request->query ?? [];
   
  }

  // 6. 여행 단건 조회 : GET /api/v1/trips/{trip_id}
  // 6-1. OneTrip 메서드 정의
  public function OneTrip() {}

  // 7. 여행 수정 : PUT /api/v1/trips/{trip_id}
  // 7-1. updateTrip 메서드 정의
  public function updateTrip() {}

  // 8. 여행 삭제 : DELETE /api/v1/trips/{trip_id}
  // 8-1. deleteTrip 메서드 정의
  public function deleteTrip() {}

}