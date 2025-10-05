<?php
    namespace Tripmate\Backend\Common\Utils;

    use Firebase\JWT\JWT as JJWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\SignatureInvalidException;

    // JWT 발급 및 검증
    class Jwt {
        // JWT 발급
        public static function encode($user_id) {
        // 시크릿 키 설정
        $secret_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30';

            // 페이로드 정의
            $payload = [
                'iss' => "tripmate.com", // 발급자
                'aud' => "tripmate/client.com", // 대상자
                'iat' => time(), // 발급 시간
                'exp' => time() + 43200, // 12시간 유효
                'jti' => self::jtiCreate(), // 고유 식별
                'user_id' => $user_id
            ];

            // JWT 인코딩 생성
            $jwt = JJWT::encode($payload, $secret_key, 'HS256');
            return $jwt;
        }

        // JWT 검증
        public static function decode($jwt) {
            // 시크릿 키 설정
            $secret_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.KMUFsIDTnFmyG3nMiGM6H9FNFUROf3wh7SmqJp-QV30';

            try {
                // 디코딩
                $decode = JJWT::decode($jwt, new Key($secret_key, 'HS256'));
            } catch (SignatureInvalidException $e) {
                // 서명 검증 실패 처리
                return false;
            } catch (ExpiredException $e) {
                // 토큰 만료 처리
                return false;
            }catch (Exception $e) {
                // 에러 발생 
                return false;
            }
        
            // 유저 아이디 확인
            $user_id = $decode->user_id; 

            // id 없을 시
            if (!$user_id) {
                return false;
            }
            
            return $user_id;
            }

        // JTI 생성 함수
        private static function jtiCreate() {
            $jti = '';
            // 난수 반복 생성
            for($i = 1 ; $i <= 32 ; $i++) {
                // 난수 생성
                $ran_m = rand(0, 9);
                $jti .= (string)$ran_m;
            }
            // jti 반환
            return $jti;
        }
    }