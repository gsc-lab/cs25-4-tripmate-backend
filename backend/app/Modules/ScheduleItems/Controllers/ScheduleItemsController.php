<?php
// 1. namespace 작성
namespace Tripmate\Backend\Modules\ScheduleItems\Controllers;

// 2. use 작성
use Tripmate\Backend\Common\Middleware\AuthMiddleware;
use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Modules\ScheduleItems\Services\ScheduleItemsService;
use Tripmate\Backend\Core\Validator;

// 3. ScheduleItemsController 클래스 정의
class ScheduleItemsController extends Controller {
  // 4. 프러퍼티 정의
  public ScheduleItemsService $scheduleItemsService;
  public Validator $validator;

  // 5. 생성자에서 Request, Response, ScheduleItemsService 초기화 
  public function __construct(Request $request, Response $response) {
    // 5-1. 부모 생성자 호출
    parent::__construct($request, $response);
    // 5-2. ScheduleItemsService 인스턴스 생성
    $this->scheduleItemsService = new ScheduleItemsService();
    // 5-3. Validator 인스턴스 생성
    $this->validator = new Validator();
  }

  // 1. 일정 생성 : POST /api/v1/trips/{trip_id}/days/{day_no}/items
  public function createScheduleItem(int $tripId, int $dayNo) {

    // 1-1. trip_id가 없으면 400 반환
    if (empty($tripId) || $tripId <= 0) {
      return $this->response->error('MISSING_TRIP_ID', 'trip_id가 필요합니다.', 400);
    }
    // 1-2. day_no가 없으면 400 반환
    if (empty($dayNo) || $dayNo <= 0) {
      return $this->response->error('MISSING_DAY_NO', 'day_no가 필요합니다.', 400);
    }

    // 1-3. 토큰 검증 및 user_id 추출
    $userId = AuthMiddleware::tokenResponse($this->request); // 검증 실패시 error
    // 1-4. 유효하지 않은 토큰일 시 에러 응답
    if (!$userId) {
        return $this->response->error('UNAUTHORIZED', '유효하지 않은 토큰입니다.', 401);
    }

    // 1-5. 유효성 검증
    $body = $this->request->body ?? [];
    // $validation = $this->validator->validateRelocation($body);
    // // 1-6. 유효성 검증 실패 시 에러 응답
    // if ($validation !== true) {
    //   return $this->response->error('VALIDATION_ERROR', $validation, 400);
    // }

    // 1-7. memo, visit_time, place_id 추출
    $placeId = $body['place_id'] ?? null;
    $visitTime = $body['visit_time'] ?? null;
    $memo = $body['memo'] ?? null;

    // 1-8. 일정 생성 서비스 호출
    $itemId = $this->scheduleItemsService->createScheduleItem(
      (int)$userId, 
      (int)$tripId, 
      (int)$dayNo, 
      $placeId, 
      $visitTime, 
      $memo);

    // 1-9. 일정 생성 실패 시 에러 응답
    if ($itemId === false) {
      return $this->response->error('CREATE_SCHEDULE_ITEM_FAILED', '일정 생성에 실패했습니다.', 500);
    }

    // 1-10. 일정 생성 성공 시 응답 반환
    return $this->response->success(['item_id' => $itemId],  201);
  }

}
