<?php
    namespace Tripmate\Backend\Common\Utils;

    // 해쉬
    class Password {
        // 비밀번호 해쉬
        public static function PasswordHash($pwd) {
            //비밀번호 해쉬
            $hash_pwd = password_hash($pwd, PASSWORD_BCRYPT);

            return $hash_pwd;
        }

        // 비밀번호 검증
        public static function PasswordValdataion($pwd, $pwd_hash) {
            // 비밀번호 검증
            if(password_verify($pwd, $pwd_hash)) {
                return true;
            } else {
                return false;
            }

        }
    }