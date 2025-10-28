<?php 
    namespace Tripmate\Backend\Common\Exceptions;

    class JwtException extends \Exception {
        protected string $error;

        public function __construct(string $error, string $message = "JWT 오류가 발생했습니다.") {
            // 부모 생성자로 메세지 전달
            parent::__construct($message, 0);

            // 에러 코드 전달
            $this->error = $error;
        }

        // 
        public function getError(): string {
            return $this->error;
        }
    }