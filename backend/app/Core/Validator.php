<?PHP
    namespace Tripmate\Backend\Core;

    // validator, NestedValidatonException, Response 라이브러리 가져와 사용
    use Respect\Validation\Validator as v;
    use Respect\Validation\Exceptions\NestedValidationException as NVE;
    use Tripmate\Backend\Common\Exceptions\ValidationException;
    use Tripmate\Backend\Common\Utils\Date;

    // 클래스 정의
    class Validator {
        // Int타입 검사
        public function vIntRule() {
            return v::intVal()->positive();
        }

        // Length타입 검사
        private function vLengthRule($min = null, $max = null) {
            return v::stringVal()->notBlank()->length($min, $max);
        }

        // String타입 검사
        private function vStringRule() {
            return v::stringVal()->notBlank();
        }

        // Float타입 검사
        private function vRequiredFloat() {
            return v::floatVal()->notOptional();
        }

        // nullable 래퍼 
        private function nullable($rule) {
            return v::optional($rule);
        }


        // try-catch 공통 함수
        public function runValidate($validation, $data, ?string $field = null) {
            try {
                // 검증 실행
                $validation->assert($data);
            } catch (NVE $e) {
                // 모든 필드별 에러 메시지 반환
                throw new ValidationException($e->getMessages(), $field);
            }
        }

        // 로그인 유효성 검증
        public function validateUser(array $data) {
            // email, password 검증
            $validation = v::key('email', v::email()->notEmpty()->length(null, 255), true)
                        -> key('password', v::stringType()->length(8, 128)
                        -> regex('/[A-Z]/')->regex('/[a-z]/')->regex('/[0-9]/')->regex('/[!@#*]/'), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        } 

        // 회원탈퇴 유효성 검증
        public function validatePassword(array $data) {
            // email, password 검증
            $validation = v::key('password', v::stringType()->length(8, 128)
                        -> regex('/[A-Z]/')->regex('/[a-z]/')->regex('/[0-9]/')->regex('/[!@#*]/'), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 회원가입 유효성 검증
        public function validateUserRegister(array $data) {
            // nickname 유효성 검증
            $validation = v::key('nickname', $this->vLengthRule(1, 50), true)
                        -> key('email', v::email()->notEmpty()->length(null, 255), true)
                        -> key('password', v::stringType()->length(8, 128)
                        -> regex('/[A-Z]/')->regex('/[a-z]/')->regex('/[0-9]/')->regex('/[!@#*]/'), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 여행 생성/ 여행 수정 유효성 검증
        public function validateTrip(array $data) {
            // title, region_id, start/end_date 검증
            $validation = v::key('title', $this->vLengthRule(1, 100), true)
                        -> key('region_id', $this->vIntRule(), true)
                        -> key('start_date', v::callback([Date::class, 'isValidDateYmd']), true)
                        ->key('end_date', v::callback([Date::class, 'isValidDateYmd'])
                            ->callback(function($endDate) use ($data) {
                                // start_date가 있을 때 비교 수행
                                if (!isset($data['start_date'])) return true;
                                return Date::isBeforeOrEqual($data['start_date'], $endDate);
                            }), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 일차 속성(메모) 수정 유효성 검증
        public function validateMemo(array $data) {
            // memo 검증
            $validation = v::key('memo', $this->vLengthRule(null, 255), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }
        
        // 일차 생성 유효성 검증
        public function validateDays(array $data) {
            // day_no, memo 검증
            $validation = v::key('day_no', $this->vIntRule(), true)
                        -> key('memo', $this->vLengthRule(null, 255), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 일차 재배치 유효성 검증
        public function validateDayRelocation(array $data) {
            // orders 배열 검증
            $validation = v::key('orders', v::arrayType()->notEmpty()->each(
                v::keySet(
                    v::key('day_no', $this->vIntRule(), true),
                    v::key('new_day_no', $this->vIntRule(), true)
                    )
                ), true
            );

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        } 

        // 외부 결과를 내부로 저장 유효성 검증
        public function validatePlaceCategory(array $data) {
            $validation = v::key('place', $this->vStringRule(), false)
                -> key('name', $this->vStringRule(), true)
                -> key('category', $this->vStringRule(), true)
                -> key('address', $this->vStringRule(), true)
                -> key('external_ref', v::stringType()->notOptional(), true)
                -> key('lat', $this->vRequiredFloat(), true)
                -> key('lng', $this->vRequiredFloat(), true)
                -> key('url', $this->vStringRule(), false);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 일정 아이템 수정 유효성 검증
        public function validateEditItem(array $data) {
            // 검증
            $validation = v::key('visit_time', $this->nullable(v::callback([Date::class, 'isValidDateYmd'])), false)
                    -> key('seq_no', $this->vIntRule(), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 일정 아이템 추가 유형성 검증
        public function validateAddItem(array $data) {
            // 일정 아이템 검증
            $validation = v::key('place_id', $this->vIntRule(), true)
                        ->key('visit_time', $this->nullable(v::callback([Date::class, 'isValidDateYmd'])), true)
                        -> key('seq_no', $this->vIntRule(), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        // 일정 아이템 순서 재배치 유효성 검증
        public function validateRelocationItem(array $data) {
            // 검증
            $validation = v::key('orders', v::arrayType()->notEmpty()->each(
                v::keySet(
                    v::key('item_id', $this->vIntRule(), true),
                    v::key('new_seq_no', $this->vIntRule(), true)
                    )
                ), true
            );

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        /**  @param  */
        public function validateItemId($data) {
            // 일정 아이템 id 검증
            $validation = $this->vIntRule();

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data, 'item_id');
        }

        /**  @param  */
        public function validatePlaceId($data) {
            // 장소 id 검증
            $validation = $this->vIntRule();

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data, 'place_id');
        }
        
        /**  @param  */
        public function validateDayNo($data) {
            // 일차 id 검증
            $validation = $this->vIntRule();

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data, 'day_no');
        }

        /**  @param  */
        public function validateTripId($data) {
            // 여행 생성 id
            $validation = $this->vIntRule();
            
            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data, 'trip_id');
        }

        // 지역 유효성 검증
        /**  @param  */
        public function validateRegion($data) {
            $validation = $this->vIntRule();
            
            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data, 'region_id');
        }

        /**  @param 쿼리*/
        public function validateRegionSearch($data) {
            // 지역 이름 검증
            $validation = v::key('query', $this->vStringRule(), true);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        /**  @param 쿼리*/
        // 외부 지도 장소 검색 유효성 검증
        public function validatePlace(array $data) {
            // 장소 검증
            $validation = v::key('place', $this->vStringRule(), false)
                -> key('sort', $this->vStringRule(), false)
                -> key('pageToken', $this->vStringRule(), false);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        /**  @param 쿼리*/
        // 좌표 -> 주소로 변환 유효성 검증
        public function validateReverseGeocoding(array $data) {
            // 장소 검증
            $validation = v::key('lat', $this->vRequiredFloat(), false)
                ->key('lng', $this->vRequiredFloat(), false);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        /** @param 쿼리 */
        // 좌표 마커 -> 장소로 변환 유효성 검증
        public function validatePlaceGeocoding(array $data) {
            // 장소 검증
            $validation = v::key('place_id', $this->vRequiredFloat(), false);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }

        /**  @param 쿼리*/
        // 일정 아이템 목록 유효성 검증
        public function validateItem(array $data) {
            $validation = v::key('page', $this->vIntRule(), false)
                        -> key('sort', $this->vStringRule(), false);

            // try-catch 예외 처리 함수 실행
            $this->runValidate($validation, $data);
        }
    }

    
