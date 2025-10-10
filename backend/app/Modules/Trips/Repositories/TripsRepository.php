<?php 
// namespace 작성
namespace Tripmate\Backend\Modules\Trips\Repositories;

// 1. DB 클래스 로드 및 pdo 사용
use Tripmate\Backend\Core\DB;
use PDO;

// 2. TripsRepository 클래스 정의
class TripsRepository {


    // 3. 생성자에서 DB 접속 및 pdo 초기화
    public PDO $pdo;

    public function __construct() {
      // 3-1. DB 객체 생성 
      $db = new DB();
      // 3-2. db 접속
      $this->pdo = $db->getConnection();
    }
    
    // 4. 트레젝션 제어 메서드
    public function beginTransaction() :  bool {
        // 4-1. 실패 시 false 반환
        return $this->pdo->beginTransaction();
    }

    // 5. 커밋 제어 메서드
    public function commit() : bool {
        // 5-1. 트레젝션이 실행중인지 확인
        if ($this->pdo->inTransaction()) {
            // 5-2. 실행중이라면 commit 실행
            return $this->pdo->commit();
        }
        // 5-3. 실행중이 아니라면 false 반환
        return false;
    }

    // 6. 롤백 제어 메서드
    public function rollBack() : bool {
        // 6-1. 트레젝션이 실행중인지 확인
        if ($this->pdo->inTransaction()) {
            // 6-2. 실행중이라면 rollBack 실행
            return $this->pdo->rollBack();
        }
        // 6-3. 실행중이 아니라면 false 반환
        return false;
    }

    // 1. 여행 생성 메서드
    public function insertTrip(int $userId, int $regionId, string $title, string $startDate, string $endDate): int|false {
      // 1-1. SQL 작성
      $sql = "INSERT INTO Trip (user_id, region_id, title, start_date, end_date, created_at, updated_at)
              VALUES (:user_id, :region_id, :title, :start_date, :end_date, NOW(), NOW())";
      // 1-2. 쿼리 준비
      $stmt = $this->pdo->prepare($sql);
      // 쿼리 준비 실패 시 false 반환
      if ($stmt === false) {
        return false;
      }
      // 1-3. 쿼리 실행
      $success = $stmt->execute([
        ':user_id' => $userId,
        ':region_id' => $regionId,
        ':title' => $title,
        ':start_date' => $startDate,
        ':end_date' => $endDate,
      ]);
     
      // 1-4. 실패 시 false 반환
      if ($success === false) {
        return false;
      }

      // 1-5. 성공 시 마지막으로 삽입된 ID 반환
      $id = (int)$this->pdo->lastInsertId(); // (int)로 형변환 후 마지막으로 삽입된 ID 반환\
      // 1-6. ID가 0 이하인 경우 false 반환
      if ($id <= 0) {
        return false;
      }// 1-7. 성공시 ID 반환
        return $id;
    }

    // 2. trip id로 여행 조회 
    // 조회 성공시 배열 반환, 실패시(존재하지 않을 경우) null 반환
    public function findTripById(int $tripId): array|null {
        // 2-1. SQL 작성
        $sql = "SELECT trip_id, user_id, region_id, title, start_date, end_date, created_at, updated_at
                FROM Trip
                WHERE trip_id = :trip_id
                LIMIT 1";
        // 2-2. 쿼리 준비
        $stmt = $this->pdo->prepare($sql);
        // 쿼리 준비 실패 시 null 반환
        if ($stmt === false) {
          return null;
        }
        // 2-3. 쿼리 실행
        $success = $stmt->execute([':trip_id' => $tripId]);
        // 2-4. 실패 시 null 반환
        if ($success === false) {
          return null;
        }

        // 2-5. 결과 가져오기
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // 2-6. 결과가 없으면 null 반환
        if ($row === false) {
          return null;
        }
        return $row;
    } 

    // 3. tripday 생성 메서드 (성공시 true, 실패시 false 반환)
    public function insertTripDay(
        int $tripId,
        int $dayNo,
        string $memo = ''
    ): bool {
        // 3-1. SQL 작성
        $sql = "
            INSERT INTO TripDay (trip_id, day_no, memo, created_at, updated_at)
            VALUES (:trip_id, :day_no, :memo, NOW(), NOW())
        ";
        // 3-2. 쿼리 준비
        $stmt = $this->pdo->prepare($sql);
        // 쿼리 준비 실패 시 false 반환
        if ($stmt === false) {
            return false;
        }
        // 3-3. 쿼리 실행
        return $stmt->execute([
            ':trip_id' => $tripId,
            ':day_no'  => $dayNo,
            ':memo'    => $memo,
        ]) !== false; // 3-4. 성공시 true, 실패시 false 반환
    }

    // 4. tripId로 trip 목록 조회 (성공시 배열, 실패시 null 반환)
    public function findTripsByUserId(int $userId, int $limit, int $offset): array|null {
      // 4-1. limit와 offset을 정수로 변환
      $limit = (int)$limit;
      $offset = (int)$offset;
      
      // 4-2. limit와 offset이 음수일 경우 0으로 설정
      if ($limit < 0) $limit = 0;
      if ($offset < 0) $offset = 0;

      // 4-3. SQL 작성
      $sql = "
        SELECT t.trip_id, t.user_id, t.region_id, t.title, t.start_date, t.end_date, t.created_at, t.updated_at,
        r.name AS region_name
        FROM Trip AS t
        LEFT JOIN Region AS r ON t.region_id = r.region_id
        WHERE t.user_id = :user_id
        ORDER BY t.created_at DESC
        LIMIT $limit OFFSET $offset
      ";
      // 4-4. 쿼리 준비
      $stmt = $this->pdo->prepare($sql);
      // 4-5. 쿼리 준비 실패 시 null 반환
      if ($stmt === false) {
        return null;
      }
      // 4-6. 쿼리 실행
      $success = $stmt->execute([':user_id' => $userId]);
      // 4-7. 실패 시 null 반환
      if ($success === false) {
        return null;
      }

      // 4-8. 결과 가져오기
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      // 4-9. 결과가 없으면 null 반환
      return $rows ? $rows : null;


    }

  }