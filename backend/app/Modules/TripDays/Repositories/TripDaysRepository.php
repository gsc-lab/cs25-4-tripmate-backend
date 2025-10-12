<?php
// 1. 네임스페이스 선언
namespace Tripmate\Backend\Modules\TripDays\Repositories;

// 2. use 작성
use Tripmate\Backend\Core\DB;
use PDO;

// 3. TripDaysRepository 클래스 정의
class TripDaysRepository {
  // 1. pdo 프러퍼티 정의
  public PDO $pdo;

  // 2. 생성자에서 DB 생성 및 접속 
  public function __construct() {
    // 2-1. DB 객체 생성
    $db = new DB();
    // 2-2. DB 접속
    $this->pdo = $db->getConnection();
  }

  // 3. 트랜잭션 제어 메서드
  public function beginTransaction() : bool {
    // 3-1. 실패 시 false 반환
    return $this->pdo->beginTransaction();
  }

  // 4. 커밋 제어 메서드
  public function commit() : bool {
    // 4-1. 트레젝션이 실행중인지 확인
    if ($this->pdo->inTransaction()){
      // 4-2. 실행중이라면 commit 실행
      return $this->pdo->commit();
    }
    // 4-3. 실행중이 아니라면 false 반환
    return false;
  }

  // 5. 롤백 제어 메서드
  public function rollBack() : bool {
    // 5-1. 트레젝션이 실행중인지 확인
    if ($this->pdo->inTransaction()){
      // 5-2. 실행중이라면 rollBack 실행
      return $this->pdo->rollBack();
    }
    // 5-3. 실행중이 아니라면 false 반환
    return false;
  }

  // 1. trip 존재 여부 및 day_count 조회 메서드
  public function getTripMeta(int $tripId) : array|false {
    // 1-1. sql 작성
    $sql = "SELECT trip_id, start_date, end_date, day_count
            FROM Trip
            WHERE trip_id = :trip_id";

    // 1-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 1-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 1-4. 쿼리 실행
    $success = $stmt->execute([':trip_id' => $tripId]);
    
    // 1-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }

    // 1-6. 결과 반환
    $trip = $stmt->fetch(PDO::FETCH_ASSOC);
    // 1-7. trip이 존재하지 않을 시 false 반환
    if ($trip === false) {
      return false;
    }

    // 1-7. day_count 숫자형으로 변환
    $trip['day_count'] = (int)$trip['day_count'];

    // 1-8. 성공 시 trip 배열 반환
    return $trip;
    }

  // 2. tripday의 day_no 존재 확인 메서드
  public function existsDayNo(int $tripId, int $dayNo) : bool {
    // 2-1. sql 작성 (trip_id, day_no에 해당하는 행이 존재하는지 확인)
    $sql = "SELECT 1
            FROM TripDay
            WHERE trip_id = :trip_id AND day_no = :day_no
            LIMIT 1";
    // 2-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 2-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 2-4. 쿼리 실행
    $success = $stmt->execute([
      ':trip_id' => $tripId,
      ':day_no'  => $dayNo
    ]);

    // 2-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }

    // 2-6. 첫번째 컬럼 값 가져오기 
    $exists = $stmt->fetchColumn();

    // 2-7. 존재하면 true, 없으면 false 반환
    if ($exists === false) {
      return false;
    }
    return true;
  }

  // 3. 현재 tripday의 최대 day_no 조회 메서드
  public function getMaxDayNo(int $tripId) : int|false {
    // 3-1. sql 작성 (해당 trip_id의 최대 day_no 조회)
    $sql = " SELECT MAX(day_no) as max_day_no
            FROM TripDay
            WHERE trip_id = :trip_id";
    
    // 3-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 3-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 3-4. 쿼리 실행
    $success = $stmt->execute([':trip_id' => $tripId]);
    
    // 3-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }
    
    // 3-6. 결과 가져오기
    $maxCount = $stmt->fetch(PDO::FETCH_ASSOC);
    // 3-7. 결과가 없으면 false 반환
    if ($maxCount === false) {
      return false;
    }
    // 3-8. 최대 day_no 반환 (null이면 false 반환)
    if ($maxCount['max_day_no'] === null) {
      return false;
    }
    return (int)$maxCount['max_day_no'];
  }

  // 4. (중간 삽입용) addDay 다음으로 day_no +1씩 밀어내는 메서드
  public function shiftDayNos(int $tripId, int $addDay) : bool {
    // 4-1. sql 작성 (해당 trip_id의 day_no가 addDay 이상인 행들의 day_no를 +1씩 증가)
    $sql = "UPDATE TripDay
            SET day_no = day_no + 1, 
                updated_at = NOW()
            WHERE trip_id = :trip_id 
            AND day_no >= :add_day
            ORDER BY day_no DESC "; // 내림차순 정렬로 밀어내기 충돌 방지

    // 4-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 4-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 4-4. 쿼리 실행
    $success = $stmt->execute([
      ':trip_id' => $tripId,
      ':add_day' => $addDay
    ]);
    // 4-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }
    // 4-6. 성공 시 true 반환
    return true;
  }

  // 5. tripday 삽입 메서드
  public function insertTripDay(int $tripId, int $dayNo, ?string $memo) : int|false {
    // 5-1. sql 작성
    $sql = "INSERT INTO TripDay (trip_id, day_no, memo, created_at, updated_at)
            VALUES (:trip_id, :day_no, :memo, NOW(), NOW())";
    // 5-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 5-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 5-4. 쿼리 실행
    $success = $stmt->execute([
      ':trip_id' => $tripId,
      ':day_no'  => $dayNo,
      ':memo'    => $memo,
    ]);
    // 5-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }
    // 5-6. 삽입된 행의 ID 가져오기
    $id = (int)$this->pdo->lastInsertId();
    // 5-7. ID가 0이면 false 반환
    if ($id === 0) {
      return false;
    }
    // 5-8. 성공시 ID 반환
    return $id;

  }

  // 6. trip의 day_count 업데이트 메서드
  public function updateTripDayCount(int $tripId, int $newDayCount) : bool {
    // 6-1. sql 작성
    $sql = "UPDATE Trip
            SET day_count = :new_day_count, updated_at = NOW()
            WHERE trip_id = :trip_id";
    // 6-2. 쿼리 준비
    $stmt = $this->pdo->prepare($sql);
    // 6-3. 쿼리 준비 실패 시 false 반환
    if ($stmt === false) {
      return false;
    }
    // 6-4. 쿼리 실행
    $success = $stmt->execute([
      ':new_day_count' => $newDayCount,
      ':trip_id'       => $tripId
    ]);
    // 6-5. 쿼리 실행 실패 시 false 반환
    if ($success === false) {
      return false;
    }
    // 6-6. 성공 시 true 반환
    return true;
  }

  





}