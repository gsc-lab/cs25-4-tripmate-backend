<?php
    namespace Tripmate\Backend\Common\Utils;

    use Firebase\JWT\JWT as JJWT;
    use Firebase\JWT\Key;
    use Firebase\JWT\ExpiredException;
    use Firebase\JWT\SignatureInvalidException;
    use Tripmate\Backend\Utils\JwtException;

    // JWT 발급 및 검증
    class Jwt {
        // secret_key 함수
        public static function secretKey() {
            return getenv("JWT_SECRET_KEY");
        }

        // JWT 발급
        public static function encode($userId) {
            // 시크릿 키 설정
            $secretKey = self::secretKey();

            // 페이로드 정의
            $payload = [
                'iss' => getenv("JWT_ISS"), // 발급자
                'aud' => getenv("JWT_AUD"), // 대상자
                'iat' => time(), // 발급 시간
                'exp' => time() + 43200, // 12시간 유효
                'jti' => self::jtiCreate(), // 고유 식별
                'userId' => $userId
            ];

            // JWT 인코딩 생성
            $jwt = JJWT::encode($payload, $secretKey, 'HS256');  // jti, exp, hs256은 .env에 보관
            return $jwt;
        }

        // JWT 검증
        public static function decode($jwt) {
            // 시크릿 키 설정
            $secretKey = self::secretKey();

            try {
                // 디코딩
                $decode = JJWT::decode($jwt, new Key($secretKey, 'HS256'));
            } catch (SignatureInvalidException $e) {
                // 서명 검증 실패 처리
                throw new JwtException("TOKEN_SIGNATURE_INVALID", "토큰 서명이 유효하지 않습니다.", 403);
            } catch (ExpiredException $e) {
                // 토큰 만료 처리
                throw new JwtException("TOKEN_EXPIRED", "토큰이 만료되었습니다. 다시 로그인해주세요.", 401);
            }

            // id가 JWT 토큰에 없을 시
            if (empty($decode->userId)) {
                throw new JwtException("TOKEN_UNKNOWN_ERROR", "토큰에 사용자 정보가 없습니다.", 401);
            }
            
            // 성공적으로 Id 파싱 성공 시 반환
            return $decode->userId;
            }

        // JTI 생성 함수
        private static function jtiCreate() {
            // 1바이트 당 16진수 2글자로, 총 32글자의 16진수 문자열 반환
            return bin2hex(random_bytes(16));
        }
    }
