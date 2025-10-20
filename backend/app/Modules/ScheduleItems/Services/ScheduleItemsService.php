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
    // 2-1. 트레젝션 시작
    if (!$this->scheduleItemsRepository->beginTransaction()) {
      return false;
    }

    // 2-2. trip_id + day_no로 trip_day_id 조회
    $tripDayId = $this->tripDaysRepository->getTripDayId($tripId, $dayNo);
    // 2-3. trip_day_id 없으면 롤백 후 false 반환
    if ($tripDayId === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 2-4. 소유권 확인
    $isOwner = $this->tripDaysRepository->isTripOwner($tripId, $userId);
    // 2-5. 소유권 없으면 롤백 후 false 반환
    if (!$isOwner) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 2-6. 일정 목록 조회 쿼리 실행
    $items = $this->scheduleItemsRepository->getScheduleItemsByTripDayId($tripDayId);
    // 2-7. 일정 목록 조회 실패 시 롤백 후 false 반환
    if ($items === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }
    // 2-8. 커밋 실행
    if (!$this->scheduleItemsRepository->commit()) {
      // 2-9. 커밋 실패 시 롤백 후 false 반환
      $this->scheduleItemsRepository->rollBack();
      return false;
    }
    // 2-10. 일정 목록 조회 성공 시 items 반환
    return $items;
  }

  // 3. 일정 아이템 부분 수정 메서드 (visit_time, memo)
  public function updateScheduleItem($userId, $tripId, $itemId, $dayNo, ?string $visitTime, ?string $memo) :array|false {
    // 3-1 트랜잭션 시작
    if (!$this->scheduleItemsRepository->beginTransaction()) {
      return false;
    }

    // visit 비어 있으면 null 처리
    if ($visitTime === '' && $visitTime !== null) {
      $visitTime = null;
    }

    // memo 비어 있으면 null 처리
    if ($memo === '' && $memo !== null) {
      $memo = null;
    }

    // 3-2 trip_id + day_no로 trip_day_id 조회
    $tripDayId = $this->tripDaysRepository->getTripDayId($tripId, $dayNo);
    // 3-3 trip_day_id 없으면 롤백 후 false 반환
    if ($tripDayId === null) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 3-4 소유권 확인
    $isOwner = $this->tripDaysRepository->isTripOwner($tripId, $userId);
    // 3-5 소유권 없으면 롤백 후 false 반환
    if (!$isOwner) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 3-6 일정 아이템 부분 수정 쿼리 실행
    $items = $this->scheduleItemsRepository->updateScheduleItem($itemId, $visitTime, $memo);
    // 3-7 일정 아이템 수정 실패 시 롤백 후 false 반환
    if ($items === false) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 3-8 커밋 실행
    if (!$this->scheduleItemsRepository->commit()) {
      // 3-9 커밋 실패 시 롤백 후 false 반환
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 3-10 일정 아이템 수정 성공 시 true 반환
    return $items;
  }

  // 4. 일정 아이템 삭제 메서드
  public function deleteScheduleItem(int $userId, int $tripId, int $dayNo, int $itemId) : bool {
    // 4-1 트랜잭션 시작
    if (!$this->scheduleItemsRepository->beginTransaction()) {
      return false;
    }

    // 4-2 trip_id + day_no로 trip_day_id 조회
    $tripDayId = $this->tripDaysRepository->getTripDayId($tripId, $dayNo);
    // 4-3 trip_day_id 없으면 롤백 후 false 반환
    if ($tripDayId === null) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 4-4 소유권 확인
    $isOwner = $this->tripDaysRepository->isTripOwner($tripId, $userId);
    // 4-5 소유권 없으면 롤백 후 false 반환
    if (!$isOwner) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 4-6 일정 아이템 삭제 쿼리 실행
    $deleted = $this->scheduleItemsRepository->deleteScheduleDayById($tripId, $dayNo, $itemId);
    // 4-7 일정 아이템 삭제 실패 시 롤백 후 false 반환
    if (!$deleted) {
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 4-8 커밋 실행
    if (!$this->scheduleItemsRepository->commit()) {
      // 4-9 커밋 실패 시 롤백 후 false 반환
      $this->scheduleItemsRepository->rollBack();
      return false;
    }

    // 4-10 일정 아이템 삭제 성공 시 true 반환
    return true;
  }

}