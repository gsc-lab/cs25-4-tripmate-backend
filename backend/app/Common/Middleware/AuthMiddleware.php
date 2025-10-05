<?php
    namespace Tripmate\Backend\Common\Middleware;

    use Tripmate\Backend\Common\Utils\Jwt;
    use Tripmate\Backend\Core\Request;
    // Bearer 토큰 검증 → req->user 주입
    class AuthMiddleware {
        // 발급 요청
        public static function tokenRequest($user_id) {
            // 발급 함수 호출
            $jwt = Jwt::encode($user_id);

            if (!$jwt) {
                return false;
            } else {
                return $jwt;
            }
        }

        // 검증 요청
        public static function tokenResponse(Request $req) {
            // 헤더가 없으면 null로 설정
            $header_token = $req->headers['authorization'] ?? null;
            if ($header_token === null) {
                return false;
            }

            // Bearer 제거
            if (strpos($header_token, 'Bearer ') === 0) {
                $jwt = substr($header_token, 7);
            } else {
                return false;
            }

            // 토큰 검증 
            $user_id = Jwt::decode($jwt);

            return $user_id;
        }
    }
