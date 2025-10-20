<?php

// 1. namespace 작성
namespace Tripmate\Backend\Modules\ScheduleItems\Services;

// 2. use 작성
use Tripmate\Backend\Modules\ScheduleItems\Repositories\ScheduleItemsRepository;
use Tripmate\Backend\Modules\TripDays\Repositories\TripDaysRepository;

// 3. ScheduleItemsService 클래스 정의
class ScheduleItemsService {
  // 4. 프러퍼티 정의
  public ScheduleItemsRepository $scheduleItemsRepository;
  public TripDaysRepository $tripDaysRepository;

  // 5. 생성자에서 ScheduleItemsRepository 초기화
  public function __construct() {
    $this->scheduleItemsRepository = new ScheduleItemsRepository();
    $this->tripDaysRepository = new TripDaysRepository();
  }

  // 1. 일정 추가 메서드 
  public function createScheduleItem(int $userId, int $tripId, int $dayNo, ?int $placeId, ?string $visitTime, ?string $memo) : int|false {
    // 1-1. 트레젝션 시작
    if (!$this->scheduleItemsRepository->beginTransaction()) {
      return false;
    }

    // 1-2. trip_id + day_no로 trip_day_id 조회
    $tripDayId = $this->tripDaysRepository->getTripDayId($tripId, $dayNo);
    // 1-3. trip_day_id 없으면 롤백 후 false 반환
    if ($tripDayId === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 1-4. 소유권 확인
    $isOwner = $this->tripDaysRepository->isTripOwner($tripId, $userId);
    // 1-5. 소유권 없으면 롤백 후 false 반환
    if (!$isOwner) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 1-6. 일정 추가 쿼리 실행
    $itemId = $this->scheduleItemsRepository->createScheduleItem($tripDayId, $placeId, $visitTime, $memo);
    // 1-7. 일정 추가 실패 시 롤백 후 false 반환
    if ($itemId === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 1-8. 커밋 실행
    if (!$this->scheduleItemsRepository->commit()) {
      // 1-9. 커밋 실패 시 롤백 후 false 반환
      $this->scheduleItemsRepository->rollBack();
      return false;
    }
    // 1-10. 일정 추가 성공 시 item_id 반환
    return $itemId;
  }

  // 2. 일정 목록 조회 메서드
  public function getScheduleItems(int $userId, int $tripId, int $dayNo) : array|false {
    // 1. 트레젝션 시작
    if (!$this->scheduleItemsRepository->beginTransaction()) {
      return false;
    }

    // 2. trip_id + day_no로 trip_day_id 조회
    $tripDayId = $this->tripDaysRepository->getTripDayId($tripId, $dayNo);
    // 3. trip_day_id 없으면 롤백 후 false 반환
    if ($tripDayId === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 1-4. 소유권 확인
    $isOwner = $this->tripDaysRepository->isTripOwner($tripId, $userId);
    // 1-5. 소유권 없으면 롤백 후 false 반환
    if (!$isOwner) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 1-6. 일정 목록 조회 쿼리 실행
    $items = $this->scheduleItemsRepository->getScheduleItemsByTripDayId($tripDayId);
    // 1-7. 일정 목록 조회 실패 시 롤백 후 false 반환
    if ($items === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }
    // 1-8. 커밋 실행
    if (!$this->scheduleItemsRepository->commit()) {
      // 1-9. 커밋 실패 시 롤백 후 false 반환
      $this->scheduleItemsRepository->rollBack();
      return false;
    }
    // 1-10. 일정 목록 조회 성공 시 items 반환
    return $items;


  }

}