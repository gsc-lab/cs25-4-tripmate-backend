<?php
// namespace App\Common
// 1. namespace 작성
namespace Tripmate\Backend\Common\Exceptions;

// 2. 컨트롤러 레벨에서 발생하는 HTTP 에러 응답을 위한 예외 클래스
class HttpException extends \RuntimeException {

  // 3. 프로퍼티 정의
  protected int $status; // HTTP 상태 코드
  protected string $codeName; // 에러 코드
  protected ?array $details; // 상세 메시지 (배열 또는 null)

  // 4. 생성자 정의
  public function __construct(int $status, string $codeName, string $message, ?array $details = null, ?\Throwable $previous = null) {
    parent::__construct($message, 0, $previous); // 부모 생성자 호출
    $this->status = $status; // HTTP 상태 코드 설정
    $this->codeName = $codeName; // 에러 코드 설정
    $this->details = $details; // 상세 메시지 설정
  }

  // 5. 각 프로퍼티에 대한 getter 메서드 정의
  public function getStatus(): int {return $this->status;} 
  public function getCodeName(): string {return $this->codeName;}
  public function getDetail(): ?array {return $this->details;}

  // 6. 자주 쓰는 HTTP 에러 예외 생성 메서드 정의
  // 인증 실패 (401)
  public static function unauthorized(string $message = '인증이 필요합니다.'): self {
    return new self(401, 'UNAUTHORIZED', $message);
  }

  // 리소스 없음 (404)
  public static function notFound(string $message = '리소스를 찾을 수 없습니다.'): self {
    return new self(404, 'NOT_FOUND', $message);
  }

  // 입력값 검증 실패 (422)
  public static function validation(array $details, string $message = '입력값이 유효하지 않습니다.'): self {
    return new self(422, 'VALIDATION_ERROR', $message, $details);
  }

  // 서버 내부 오류 (500)
  public static function internal(string $message = '서버 오류가 발생했습니다.'): self {
    return new self(500, 'INTERNAL_ERROR', $message);
  }
    

}
