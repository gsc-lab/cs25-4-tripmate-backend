<?php
    namespace Tripmate\Backend\Common\Exceptions;

    class ValidationException extends \Exception {

        // 상세 오류 정보 프로퍼티 (getter 필요)
        protected array $details;

        public function __construct(array $details, string $message = "잘못된 입력값입니다.") {
            // 부모 생성자로 메세지 전달
            // ValidationException에서는 코드가 필요 없기 때문에 0으로 전달
            parent::__construct($message, 0);

            // 세부 오류 정보 설정
            $this->details = $details;
        }

        // 세부 오류 정보 getter
        public function getDetails(): array {
            return $this->details;
        }
    }