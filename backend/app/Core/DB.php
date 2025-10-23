<?php
// namespace App\Core;
// 1. namespace 작성
namespace Tripmate\Backend\Core;

// 2. use 작성 
use PDO;
use PDOException; // 예외 처리 클래스 추가

// 3. DB 연결 관리를 위한 클래스 작성
class DB {
  // 4. 정적 프로퍼티 정의 (싱글턴 형태로 커넥션 1회 생성)
  private static ?PDO $pdo = null; 

  // 5. 커넥션 반환 메서드
  public static function conn(): PDO {
    // 5-1. 이미 커넥션이 생성되어 있으면 재사용
    if (self::$pdo !== null) {
      
      // 4-2. .env 값을 사용해 DB 접속 정보 설정 
      $dsn =  sprintf(
        "mysql:host=%s;dbname=%s;charset=utf8mb4",
        $_ENV['DB_HOST'] ,
        $_ENV['DB_NAME']
      );
      $user = $_ENV['DB_USER'];
      $pass = $_ENV['DB_PASSWORD'];
    
      // 5-3. PDO 옵션 설정
      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 예외 모드로 설정
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 결과를 연관 배열 형태로 설정
        PDO::ATTR_EMULATE_PREPARES => false, // 네이티브 prepared statement 사용
      ];

      // 5-4. PDO 인스턴스 생성
      self::$pdo = new PDO($dsn, $user, $pass, $options);
    }

    // 5-5. 최종 반환
    return self::$pdo;
  } 

    // 6. 트랜잭션 헬퍼 메서드
    public static function transaction(callable $callback) {
      // 6-1. DB 커넥션 가져오기
      $pdo = self::conn();

      try {
        // 6-2. 트랜잭션 시작
        $pdo->beginTransaction();

        // 6-3. 콜백 함수 실행
        $result = $callback($pdo);

        // 6-4. 커밋
        $pdo->commit();
        return $result;


      } catch (\Throwable $e) {
        // 6-5. 실패시 롤백
        if ($pdo->inTransaction()) {
          $pdo->rollBack();
        }
        // 6-6. 상위로 예외 재던지기
        throw $e;
      }
    }
  }
  

