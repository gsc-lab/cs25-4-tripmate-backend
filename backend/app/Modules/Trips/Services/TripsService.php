<?php 
// namespace 작성
namespace Tripmate\Backend\Modules\Trips\Services;

// 1. TripsRepository 클래스 로드
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
    // 5-1. 트랜잭션 시작
   
  }
}