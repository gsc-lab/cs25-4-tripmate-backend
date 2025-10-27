<?php 
    namespace Tripmate\Backend\Common\Exceptions;

    class JwtException extends \Exception {
        public string $error;

        public function __construct(string $error, string $message, $code=400) {
            // 부모 생성자로 메세지 전달
            parent::__construct($message, $code);

            // 에러 코드 전달
            $this->error = $error;
        }
    }