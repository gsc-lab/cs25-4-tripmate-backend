<?php
    namespace Tripmate\Backend\Common\Exceptions;

    class ValidationException extends \Exception {
        public array $errors;

        public function __construct(array $errors, string $message = "잘못된 입력값입니다.", $code=400) {
            // 부모 생성자로 메세지 전달
            parent::__construct($message, $code);

            // 에러 코드 전달
            $this->errors = $errors;
        }
    }