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


}