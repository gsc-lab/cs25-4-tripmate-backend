<?php 
    namespace Tripmate\Backend\Modules\Places\Services;

    use Tripmate\Backend\Common\Exceptions\HttpException;

    class GoogleApi {
        // google 공통 API 연결 함수
        public static function searchService($endpoint, $headers, $postData) {

            // 서버 전달
            $curl = curl_init($endpoint); // API 주소 연결
            curl_setopt($curl, CURLOPT_POST, true); // 주소 옵션 : POST, 기본값 : GET
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // 헤더 전달
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData)); // 본문 전달
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // SSL 인증서 확인(true 보안적)
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // 실행 결과를 echo하지 말고 변수로 반환
            $response = curl_exec($curl); // 실행

            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // HTTP 상태 코드
            $error = curl_error($curl); // cURL 에러

            curl_close($curl);

            // 응답 처리
            if ($httpCode !== 200 || $response === false) {
                throw new HttpException("500", "API_ERROR", "외부 API 연결에 실패했습니다.");
            }

            // 데이터 본문 처리
            $result = json_decode($response, true);

            return $result;
        }
    }