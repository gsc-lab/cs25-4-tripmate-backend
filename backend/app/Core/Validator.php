<?PHP
    namespace Tripmate\Backend\Core;

    // validator, NestedValidatonException, Response 라이브러리 가져와 사용
    use Respect\Validation\Validator as v;
    use Respect\Validation\Exceptions\NestedvalidationException as nve;
    use Tripmate\Backend\Common\Exceptions\ValidationException;

    // 클래스 정의
    class Validator extends Response {
        // Int타입 검사
        public function vIntRule() {
            return v::intVal()->positive()->notEmpty();
        }

        // DateTime 타입 검사
        private function vDateTimeRule() {
            return v::date()->notEmpty();
        }

        // Length타입 검사
        private function vLengthRule($min = null, $max = null) {
            return v::stringVal()->notEmpty()->length($min, $max);
        }

        // String타입 검사
        private function vStringRule() {
            return v::stringVal()->notEmpty();
        }

        // Float타입 검사
        private function vFloatRule() {
            return v::floatVal()->notEmpty();
        }

        // 메세지와 맵 매핑 함수
        private function mapErrors(array $messages, array $map) {
            $errorMes = [];
            
            // 반복 매핑 
            foreach ($map as $key => $code) {
                if (isset($messages[$key])) {
                    $errorMes[$key] = $code;
                }
            }
            return $errorMes;
        }



        // 로그인 유효성 검증
        public function validateUser(array $date) {
            try {
                // email, password 검증
                $validation = v::key('email', v::email()->->notEmpty()->length(null, 255), true)
                            -> key('password', v::alnum()->notEmpty()->length(8, 128), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'email' => "EMAIL_INVALID",
                    'password' => "PASSWORD_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        } 

        // 회원가입 유효성 검증
        public function validateUserRegister(array $date) {
            try {
                // nickname 유효성 검증
                $validation = v::key('nickname', $this->vLengthRule(1, 50), true),
                            -> key('email', v::email()->->notEmpty()->length(null, 255), true),
                            -> key('password', v::alnum()->notEmpty()->length(8, 128), true);

                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'nickname' => "NICKNAME_INVALID",
                    'email' => "EMAIL_INVALID",
                    'password' => "PASSWORD_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 여행 생성/ 여행 수정 유효성 검증
        public function validateTrip(array $date) {

            try {
                // title, region_id, start/end_date 검증
                $validation = v::key('title', $this->vLengthRule(1, 100), true)
                            -> key('region_id', $this->vIntRule(), true)
                            -> key('start_date', $this->vDateTimeRule(), true)
                            -> key('end_date', $this->vDateTimeRule(), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'title' => "TITLE_INVALID",
                    'region_id' => "REGION_ID_INVALID",
                    'start_date' => "START_DATE_INVALID",
                    'end_date' => "END_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 일차 속성(메모) 수정 유효성 검증
        public function validateMemo(array $date) {
            try {
                // memo 검증
                $validation = v::key('memo', $this->vLengthRule(null, 255), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'memo' => "MEMO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }
        
        // 일차 생성 유효성 검증
        public function validateDays(array $date) {
            try {
                // day_no, memo 검증
                $validation = v::key('day_no', $this->vIntRule(), true),
                            -> key('memo', $this->vLengthRule(null, 255), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'day_no' => "DAY_NO_INVALID",
                    'memo' => "MEMO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 일차 재배치 유효성 검증
        public function validateDayRelocation(array $date) {
            try {
                // orders 배열 검증
                $validation = v::key('orders', v::arrayType()->notEmpty()->each(
                    v::keySet(
                        v::key('day_no', $this->vIntRule(), true),
                        v::key('new_day_no', $this->vIntRule(), true)
                    )
                ), true
            );

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'orders' => "ORDERS_INVALID",
                    'day_no' => "DAY_NO_INVALID",
                    'new_day_no' => "NEW_DAY_NO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        } 

        // 외부 결과를 내부로 저장 유효성 검증
        public function validatePlaceCategory(array $date) {
            try {
                $validation = v::key('place', $this->vStringRule(), false)
                    -> key('name', $this->vStringRule(), true)
                    -> key('category', $this->vStringRule(), true)
                    -> key('address', $this->vStringRule(), true)
                    -> key('external_ref', $this->vStringRule(), true)
                    -> key('lat', $this->vFloatRule(), true)
                    -> key('lng', $this->vFloatRule(), true)
                    -> key('url', $this->vStringRule(), false);
                
                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'place' => "PLACE_INVALID",
                    'name' => "NAME_INVALID",
                    'category' => "CATEGORY_INVALID",
                    'address' => "ADDRESS_INVALID",
                    'external_ref' => "EXTERNAL_REF_INVALID",
                    'lat' => "LAT_INVALID",
                    'lng' => "LNG_INVALID",
                    'url' => "URL_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 일정 아이템 수정 유효성 검증
        public function validateEditItem(array $date) {
            try {
                $validation = v::key('visit_time', $this->vDateTimeRule(), true)
                        -> key('seq_no', $this->vIntRule(), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'visit_time' => "VISIT_TIME_INVALID",
                    'seq_no' => "SEQ_NO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 일정 아이템 추가 유형성 검증
        public function validateAddItem(array $date) {
            // 시간 및 순서 검증
            $result = $this->validateEditItem($date);

            try {
                // 일정 아이템 검증
                $validation = v::key('place_id', $this->vIntRule(), true),
                            -> key('visit_time', $this->vDateTimeRule(), true),
                            -> key('seq_no', $this->vIntRule(), true);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'place_id' => "PLACE_ID_INVALID",
                    'visit_time' => "VISIT_TIME_INVALID",
                    'seq_no' => "SEQ_NO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        // 일정 아이템 순서 재배치 유효성 검증
        public function validateRelocationItem(array $date) {
            try {
                $validation = v::key('orders', v::arrayType()->notEmpty()->each(
                v::keySet(
                    v::key('item_id', $this->vIntRule(), true),
                    v::key('new_seq_no', $this->vIntRule(), true)
                    )
                ), true
            );

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'orders' => "ORDERS_INVALID",
                    'item_id' => "ITEM_ID_INVALID",
                    'new_seq_no' => "NEW_SEQ_NO_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        /**  @param  */
        public function validateItemId($date) {
            try {
                // 일정 아이템 id 검증
                $validation = $this->vIntRule();

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $errorMap = [ 'item_id' => "ITEMID_INVALID" ];

                // 에러발생 시키기
                throw new ValidationException($errorMap, "입력값이 올바르지 않습니다.");
            }
        }

        /**  @param  */
        public function validatePlaceId($date) {
            try {
                // 장소 id 검증
                $validation = $this->vIntRule();

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $errorMap = [ 'place_id' => "PLACE_ID_INVALID" ];

                // 에러발생 시키기
                throw new ValidationException($errorMap, "입력값이 올바르지 않습니다.");
            }
        }
        
        /**  @param  */
        public function validateDayNo($date) {
            try {
                // 일차 id 검증
                $validation = $this->vIntRule();
                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $errorMap = [ 'day_no' => "DAY_NO_INVALID" ];

                // 에러발생 시키기
                throw new ValidationException($errorMap, "입력값이 올바르지 않습니다.");
            }
        }

        /**  @param  */
        public function validateTripId($date) {
            try {
                // 여행 생성 id
                $validation = $this->vIntRule();
                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $errorMap = [ 'trip_id' => "TRIP_ID_INVALID" ];

                // 에러발생 시키기
                throw new ValidationException($errorMap, "입력값이 올바르지 않습니다.");
            }
        }

        // 지역 유효성 검증
        /**  @param  */
        public function validationRegion($date) {
            try {
                $validation = $this->vIntRule();
                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $errorMap = [ 'region_id' => "REGION_ID_INVALID" ];

                // 에러발생 시키기
                throw new ValidationException($errorMap, "입력값이 올바르지 않습니다.");
            }
        }


        /**  @param 쿼리*/
        public function validateRegionSearch($date) {
            try {
                // 지역 이름 검증
                $validation = v::key('query', $this->vStringRule(), true);
                
                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'query' => "QUERY_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        /**  @param 쿼리*/
        // 외부 지도 장소 검색 유효성 검증
        public function validatePlace(array $date) {
            try {
                // 장소 검증
                $validation = v::key('place', $this->vStringRule(), true)
                    -> key('radius', $this->vStringRule(), false)
                    -> key('lat', $this->vFloatRule(), false)
                    -> key('lng', $this->vFloatRule(), false)
                    -> key('sort', $this->vStringRule(), false)
                    -> key('page', $this->vIntRule(), false);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'place' => "PLACE_INVALID",
                    'radius' => "RADIUS_INVALID",
                    'lat' => "LAT_INVALID",
                    'lng' => "LNG_INVALID",
                    'sort' => "SORT_INVALID",
                    'page' => "PAGE_INVALID"
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }

        /**  @param 쿼리*/
        // 일정 아이템 목록 유효성 검증
        public function validateItem(array $date) {
            try {
                $validation = v::key('page', $this->vIntRule(), false)
                            -> key('sort', $this->vStringRule(), false);

                // 검증 실행
                $validation->assert($date);
            }catch (nve $e) {
                // 모든 필드별 에러 메시지 배열
                $messages = $e->getMessages();

                // 에러 배열
                $map = [
                    'place' => "PLACE_INVALID",
                    'sort' => 'SORT_INVALID'
                ];

                // 매핑 함수 실행
                $errorMes = $this->mapErrors($messages, $map);

                // 에러발생 시키기
                throw new ValidationException($errorMes, "입력값이 올바르지 않습니다.");
            }
        }
    }

    
