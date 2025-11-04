<?php
// namespace 작성
namespace Tripmate\Backend\Modules\TripDays\Controllers;

// use 작성
use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Core\DB;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;
use Tripmate\Backend\Modules\TripDays\Services\TripDaysService;

// TripDaysController 클래스 작성
class TripDaysController extends Controller { 

  // 프로퍼티 정의
  public TripDaysService $tripDaysService;
  public Validator $validator;

  // 생성자에서 Request, Response, TripDaysService, Validator 초기화
  public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $pdo = DB::conn();
        $this->tripDaysService = new TripDaysService($pdo);
        $this->validator = new Validator();
    }

  // 1. trip day 생성 
  // POST /api/v1/trips/{trip_id}/days
  public function createTripDay() 
  {
    $this->run(function () {
      // 1-1. 경로 파라미터
      $tripId =  $this->request->getAttribute('trip_id');
      
      // 1-2. tripId 유효성 검사
      $this->validator->validateTripId($tripId);
      
      // 1-3. 요청 바디
      $body = (array)$this->request->body();
      $dayNo = isset($body['day_no']) ? (int)$body['day_no'] : 0;
      $memo  = $body['memo'] ?? null;

      // 1-4. 바디 검증
      $this->validator->validateDays(['day_no' => $dayNo, 'memo' => $memo]);

      // 1-5. 서비스 호출
      $userId = $this->getUserId();
      $tripDayId = $this->tripDaysService->addTripDay($userId, $tripId, $dayNo, $memo);

      // 1-6. 실패 시 에러 응답
      if ($tripDayId <= 0) {
      $this->response->error('TRIPDAY_CREATION_FAILED', '여행 일자 생성에 실패했습니다.', 500);
      return null;
      }
      // 1-7. 성공: 201 + Location 헤더
            $this->response->setHeader('Location', "/api/v1/trips/{$tripId}/days/{$dayNo}")
                           ->created([
                               'trip_day_id' => (int)$tripDayId,
                               'trip_id'     => (int)$tripId,
                               'day_no'      => (int)$dayNo,
                               'memo'        => $memo === null ? null : (string)$memo,
                           ]);
            return null;
        });
  }
  // 4. trip day 단건 조회 : GET /api/v1/trips/{trip_id}/days/{day_no}
  // 4-1. showTripDay 메서드 정의
  public function showTripDay() {
    
    $this->run(function () {
      // 4-1. 경로 파라미터
      $tripId =  $this->request->getAttribute('trip_id');
      $dayNo  =  $this->request->getAttribute('day_no');

      // 4-2. 유효성 검사
      $this->validator->validateTripId($tripId);
      $this->validator->validateDayNo($dayNo);

      // 4-3. 서비스 호출
      $userId = $this->getUserId();
      $tripDay = $this->tripDaysService->getTripDay($userId, $tripId, $dayNo);

      // 4-4. 실패 시 에러 응답
      if ($tripDay === null) {
        $this->response->error('TRIPDAY_NOT_FOUND', '해당하는 여행 일차를 찾을 수 없습니다.', 404);
        return null;
      }

      // 4-5. 성공 시 응답
      $this->response->success($tripDay);
      return null;
    });
  }
    // // 5. trip day 목록 조회 : GET /api/v1/trips/{trip_id}/days
    // // 5-1. getTripDays 메서드 정의
    // // 일차 목록 조회
    // public function getTripDays(int $tripId) {
    //     // 토큰 검증
    //     $userId = AuthMiddleware::tokenResponse($this->request);

    //     // 서비스 전달
    //     $result = $this->tripDaysService->daysListService($tripId, $userId);
    
    //     if ($result == "DB_SELECT_FAILD") {
    //         $this->error($result, "DB 조회에 실패하였습니다.");
    //     } else {
    //         $this->success([$result]);
    //     }
    // }

    // // 6. trip day 수정 : PUT /api/v1/trips/{trip_id}/days/{day_no}
    // // 6-1. updateTripDay 메서드 정의
    // // 일차 메모 속성 수정
    // public function updateTripDay(int $tripId, int $dayId) {
    //     // 토큰 검증
    //     $userId = AuthMiddleware::tokenResponse($this->request);
    
    //     // 데이터 가져오기
    //     $data = $this->request->body;

    //     // 유효성 검증
    //     if ($this->validator->validateMemo($data) !== true) {
    //         $this->error("VALIDATION_ERROR", "입력값이 유효하지 않습니다.");
    //     }

    //     $memo = $data['memo'];

    //     $result = $this->tripDaysService->noteService($tripId, $dayId, $memo, $userId);
    
    //     if($result == "UPDATE_FAIL") {
    //         $this->error($result, "메모 업데이트에 실패했습니다.");
    //     } else if ($result == "NOT_FOUND") {
    //         $this->error($result, "입력값 조회에 실패하였습니다.");
    //     } else {
    //         $this->success($result);
    //     }
    // }

  // 7. trip day 삭제 : DELETE /api/v1/trips/{trip_id}/days/{day_no}
  // 7-1. deleteTripDay 메서드 정의
  public function deleteTripDay() {
    $this->run(function () {
      // 7-1. 경로 파라미터
      $tripId =  $this->request->getAttribute('trip_id');
      $dayNo  =  $this->request->getAttribute('day_no');

      // 7-2. 유효성 검사
      $this->validator->validateTripId($tripId);
      $this->validator->validateDayNo($dayNo);

      // 7-3. 서비스 호출
      $userId = $this->getUserId();
      $deleted = $this->tripDaysService->deleteTripDay($userId, $tripId, $dayNo);

      // 7-4. 실패 시 에러 응답
      if (!$deleted) {
        $this->response->error('TRIPDAY_DELETION_FAILED', '여행 일차 삭제에 실패했습니다.', 500);
        return null;
      }

      // 7-5. 성공 시 응답
      $this->response->noContent();
      return null;
    });

  }

//     // 8. trip day 순서 변경 : POST /api/v1/trips/{trip_id}/days:reorder
//     // 8-1. reorderTripDays 메서드 정의
//     // 일차 재배치
//     public function reorderTripDays(int $tripId) {
//         // 토큰 검증
//         $userId = AuthMiddleware::tokenResponse($this->request);

//         // 본문 데이터 꺼내기
//         $data = $this->request->body;

//         // 유효성 검증
//         if (!$this->validator->validateDayRelocation($data)) {
//             $this->error("VALIDATION_ERROR", "입력값이 유효하지 않습니다.");
//         }
//         $orders = $data['orders'];

//         // 서비스 전달
//         $result = $this->tripDaysService->relocationDaysService($tripId, $orders, $userId);
    
//         if($result == "SELECT_FAIL") {
//             $this->error($result, "값 조회에 실패했습니다.");
//         } else if($result == "UPDATE_FAIL") {
//             $this->error($result, "일차 재배치에 실패했습니다.");
//         } else if ($result == "NOT_FOUND") {
//             $this->error($result, "값 조회에 실패했습니다.");
//         } else {
//             $this->success($result);
//         }
//     }
// }
}