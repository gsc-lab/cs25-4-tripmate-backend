<?php
    namespace Tripmate\Backend\Common\Utils;

    // 해쉬
    class Password {
        // 비밀번호 해쉬
        public static function hash($pwd) {
            //비밀번호 해쉬
            return password_hash($pwd, PASSWORD_BCRYPT);
        }

        // 비밀번호 검증
        public static function verify($pwd, $pwdHash) {
            // 비밀번호 검증
            return password_verify($pwd, $pwdHash);
        }
    }