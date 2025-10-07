<?php 
// namespace 작성
namespace Tripmate\Backend\Modules\Trips\Services;

// 1. TripsRepository 클래스 로드 및 Date 유틸리티 로드
use Tripmate\Backend\Common\Utils\Date;
use Tripmate\Backend\Modules\Trips\Repositories\TripsRepository;

// 2. TripsService 클래스 정의
class TripsService {
  // 3. 프러퍼티 정의
  public TripsRepository $tripsRepository;

  // 4. 생성자에서 TripsRepository 초기화
  public function __construct() {
    $this->tripsRepository = new TripsRepository();
  }

  // 5. 여행 생성 메서드
  public function createTrip(int $userId, int $regionId, string $title, string $startDate, string $endDate): int|false {
    // 5-1. 날짜 형식 검증
    if (!Date::isValidDateYmd($startDate) || !Date::isValidDateYmd($endDate)) {
      return false;
    }
    // 5-2. 시작일이 종료일보다 이후인지 검증
    if ($startDate > $endDate) {
      return false;
    }

    // 5-3. 트레젝션 시작
    if (!$this->tripsRepository->beginTransaction()) {
      return false;
    }

    // 5-4. 여행 생성
    $tripId = $this->tripsRepository->insertTrip($userId, $regionId, $title, $startDate, $endDate);
    // 5-5. 여행 생성 실패 시 롤백 후 false 반환
    if ($tripId === false) {
      $this->tripsRepository->rollBack();
      return false;
    }

    // 5-6. tripdays 자동 생성
    $dayCount = Date::calcInclusiveDays($startDate, $endDate);
    // 5-7. dayCount가 1 이하이면 롤백 후 false 반환
    if ($dayCount <= 0) {
      $this->tripsRepository->rollBack();
      return false;
    }

    // 5-7. 여행 일자 수 만큼 tripdays 생성
    for ($dayNo = 1; $dayNo <= $dayCount; $dayNo++) {
      // 실패 시 롤백 후 false 반환
      if (!$this->tripsRepository->insertTripDay($tripId, $dayNo)) {
        $this->tripsRepository->rollBack();
        return false;
      }
    }

    // 5-8. 커밋이 실패하면 롤백 후 false 반환
    if (!$this->tripsRepository->commit()) {
      $this->tripsRepository->rollBack();
      return false;
    }

    // 5-9. 여행 생성 성공 시 tripId 반환
    return $tripId;
   
  }

  // 6. trip_id로 여행 단건 조회 메서드
  public function findTripById(int $tripId): array|false {
   // 6-1. tripId가 0 이하이면 false 반환
    if ($tripId <= 0) {
      return false;
    }
   // 6-2. TripsRepository의 findTripById 메서드 호출
   $trip = $this->tripsRepository->findTripById($tripId);
   // 6-3. 조회 실패 시 false 반환
    if ($trip === null) {
      return false;
    }
   // 6-4. 조회 성공 시 여행 정보 배열 반환
    return $trip;
  }

}