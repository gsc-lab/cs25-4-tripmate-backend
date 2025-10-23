<?php
// namespace App\Common\Exceptions;
// 1. namespace 작성
namespace Tripmate\Backend\Common\Exceptions;

// 2. DB 관련 예외 처리를 위한 클래스 작성
class DbException extends \RuntimeException {
  // 3. 프로퍼티 정의
  protected string $codeName; // 에러 코드

  // 4. 생성자 정의
  public function __construct(string $codeName, string $message = 'DB 오류', ?\Throwable $previous = null) {
    parent::__construct($message, 0, $previous); // 부모 생성자 호출
    $this->codeName = $codeName; // 에러 코드 설정
  }
}