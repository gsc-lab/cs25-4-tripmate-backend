<?php
// 1. namespace 작성
namespace Tripmate\Backend\Modules\ScheduleItems\Repositories;

// 2. DB 클래스 로드 및 pdo 사용
use Tripmate\Backend\Core\DB;
use PDO;

// 3. ScheduleItemsRepository 클래스 정의
class ScheduleItemsRepository {

    // 4. 생성자에서 DB 접속 및 pdo 초기화
    public PDO $pdo;

    public function __construct() {
      // 4-1. DB 객체 생성 
      $db = new DB();
      // 4-2. db 접속
      $this->pdo = $db->getConnection();
    }

    // 5. 트레젝션 제어 메서드
    public function beginTransaction() :  bool {
        // 5-1. 실패 시 false 반환
        return $this->pdo->beginTransaction();
    }

    // 6. 커밋 제어 메서드
    public function commit() : bool {
        // 6-1. 트레젝션이 실행중인지 확인
        if ($this->pdo->inTransaction()) {
            // 6-2. 실행중이라면 commit 실행
            return $this->pdo->commit();
        }
        // 6-3. 실행중이 아니라면 false 반환
        return false;
    }

    // 7. 롤백 제어 메서드
    public function rollBack() : bool {
        // 7-1. 트레젝션이 실행중인지 확인
        if ($this->pdo->inTransaction()) {
            // 7-2. 실행중이라면 rollBack 실행
            return $this->pdo->rollBack();
        }
        // 7-3. 실행중이 아니라면 false 반환
        return false;
    }

    // 1. tripday 존재 확인 + 잠금 (sql_no 중복 방지)
    public function lookTripDay(int $tripDayId) : bool {
      // 1-1 SQL 작성 (부모 trip_days 테이블 잠금)
      $sql = "SELECT trip_day_id 
              FROM TripDay 
              WHERE trip_day_id= :trip_day_id 
              FOR UPDATE";

      // 1-2. 쿼리 준비
      $stmt = $this->pdo->prepare($sql);
      // 1-3. 쿼리 준비 실패시 false 반환
      if ($stmt === false) {
        return false;
      }
      // 1-4. 쿼리 실행
      $ok = $stmt->execute([':trip_day_id' => $tripDayId]);
      // 1-5. 쿼리 실행 실패시 false 반환
      if ($ok === false) {
        return false;
      }
      // 1-6 . 조회된 결과가 없는 경우 false 반환
      if ($stmt->fetchColumn() === false) {
        return false;
      }
      // 1-7. 성공 시 true 반환
      return true;
    }

    // 2. 다음 seq_no 계산 
    public function getNextSeqNo(int $tripDayId) : int|false {
      // 2-1 SQL 작성
      $sql = "SELECT COALESCE(MAX(seq_no), 0) + 1 AS next_seq_no 
              FROM ScheduleItem 
              WHERE trip_day_id = :trip_day_id";

      // 2-2. 쿼리 준비
      $stmt = $this->pdo->prepare($sql);
      // 2-3. 쿼리 준비 실패시false 반환
      if ($stmt === false) {
        return false  ;
      }
      // 2-4. 쿼리 실행
      $ok = $stmt->execute([':trip_day_id' => $tripDayId]);
      // 2-5. 쿼리 실행 실패시 false 반환
      if ($ok === false) {
        return false;
      }
      // 2-6. 결과 조회 실패시 false 반환
      $next = $stmt->fetchColumn();
      if ($next  === false) {
        return false;
      }
      // 2-7. 성공 시 next_seq_no 반환
      return (int)$next;
    }

    // 3. schedule_item 생성 메서드
    public function insertScheduleItem(
        int $tripDayId,
        ?int $placeId,
        int $seqNo,
        ?string $visitTime,
        ?string $memo
    ) : int|false {
      // 3-1. 빈 문자열은 null로 변환
      if ($visitTime === '') {
        $visitTime = null;
      }
      if ($memo === '') {
        $memo = null;
      }

      // 3-2. SQL 작성
      $sql = " 
        INSERT INTO ScheduleItem
          (trip_day_id, place_id, seq_no, visit_time, memo, created_at, updated_at) 
        VALUES 
          (:trip_day_id, :place_id, :seq_no, :visit_time, :memo, NOW(), NOW())";

      // 3-3. 쿼리 준비
      $stmt = $this->pdo->prepare($sql);
      // 3-4. 쿼리 준비 실패시 false 반환
      if ($stmt === false) {
        return false;
      }

      // 3-5. 쿼리 실행
      $ok = $stmt->execute([
        ':trip_day_id' => $tripDayId,
        ':place_id' => $placeId,
        ':seq_no' => $seqNo,  
        ':visit_time' => $visitTime,
        ':memo' => $memo,
      ]);

      // 3-6. 쿼리 실행 실패시 false 반환
      if ($ok === false) {
        return false;
      }

      // 3-7. 마지막으로 삽입된 ID 반환
      $id = (int)$this->pdo->lastInsertId();
      // 3-8. ID가 없는 경우 false 반환
      if ($id === false) {
        return false;
      }
      return $id;
    }

    // 4. schedule_item 추가 메인 메서드
    public function createScheduleItem (
        int $tripDayId,
        ?int $placeId,
        ?string $visitTime,
        ?string $memo
    ) : int|false {
      // 4-1. trip_day 존재 확인 + 잠금
      $exists = $this->lookTripDay($tripDayId);
      if ($exists === false) {
        return false;
      }

      // 4-2. 다음 seq_no 계산
      $nextSeqNo = $this->getNextSeqNo($tripDayId);
      if ($nextSeqNo === false) {
        return false;
      }

      // 4-3. schedule_item 생성
      $scheduleItemId = $this->insertScheduleItem(
        $tripDayId,
        $placeId,
        $nextSeqNo,
        $visitTime,
        $memo
      );
      if ($scheduleItemId === false) {
        return false;
      }

      // 4-4. 성공 시 schedule_item ID 반환
      return $scheduleItemId;
    }

  }