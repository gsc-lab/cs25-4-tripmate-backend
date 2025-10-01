<?PHP
    namespace Tripmate\Backend\Core;  // 네임스페이스 정의 (APP가 찾게 하기 위해)

    // validator, NestedValidatonException 라이브러리 가져와 사용
    use Respect\Validation\Validator as v;
    use Respect\Validation\Exceptions\NestedValidationException as nve;

    // 클래스 정의
    class Validator {
        // 에러 정의
        private function ErrorCheck($validation, $date) {
            try{
                $validation->assert($date);
            } catch(nve $e) {
                // 모든 에러 메세지 배열을 가져와
                $allErrormessage = $e->getMessages();

                // 연관배열이기에 reset()로 첫 값을 가져온다. 
                return reset($allErrormessage);
            }

            return true;
        }

        // 로그인 유효성 검증
        public function ValidationUser(array $date) {
            // email, password 검증
            $validation = v::key('email', v::email()->notEmpty()->length(null, 255), true)
                        -> key('password', v::alnum()->length(8, 128)->notEmpty(), true);

            // 에러 확인
            return $this->ErrorCheck($validation, $date);
        } 

        // 회원가입 유효성 검증
        public function ValidationUserRegister(array $date) {
            // email, password 검증
            $result = $this->ValidationUser($date);
            // 에러 발생
            if ($result !== true) {
                return $result;
            }

            // nickname 유효성 검증
            $validation = v::key('nickname', v::notEmpty()->length(1, 50), true);

            return $this->ErrorCheck($validation, $date);
        }

        // 여행 생성/ 여행 수정 유효성 검증
        public function ValidationTrip(array $date) {
            // title, region_id, start/end_date 검증
            $validation = v::key('title', v::length(1, 100)->notEmpty(), true)
                        -> key('region_id', v::intVal()->notEmpty(), true)
                        -> key('start_date', v::date()->notEmpty(), true)
                        -> key('end_date', v::date()->notEmpty(), true);

            return $this->ErrorCheck($validation, $date);
        }

        // 일차 속성(메모) 수정 유효성 검증
        public function ValidationMemo(array $date) {
            // memo 검증
            $validation = v::key('memo', v::length(null, 255));

            return $this->ErrorCheck($validation, $date);
        }
        
        // 일차 생성 유효성 검증
        public function ValidationDays(array $date) {
            // memo 검증
            $result = $this->ValidationMemo($date);
            if ($result !== true) {
                return $result;
            }

            // day_no 검증
            $validation = v::key('day_no', v::intVal()->notEmpty(), true);
            
            return $this->ErrorCheck($validation, $date);
        }

        // 일차 재배치 유효성 검증
        public function ValidationDayRelocation(array $date) {
            // orders 배열 검증
            $validation = v::key('orders', v::arrayType()->each(
                v::keySet(
                    v::key('day_no', v::notEmpty()->intVal(), true),
                    v::key('new_day_no', v::notEmpty()->intVal(), true)
                )
            ));

            return $this->ErrorCheck($validation, $date);
        } 

        // 외부 결과를 내부로 저장 유효성 검증
        public function ValidationPlaceCategory(array $date) {
            $validation = v::key('place', v::stringType()->notEmpty(), true)
                -> key('category_id', v::intVal()->notEmpty())
                -> key('address', v::stringType()->notEmpty())
                -> key('external_ref', v::stringType()->notEmpty());

            return $this->ErrorCheck($validation, $date);
        }

        // 일정 아이템 수정 유효성 검증
        public function ValidationAddPlace(array $date) {
            $validation = v::key('visit_time', v::dateTime()->notEmpty(), true)
                    -> key('seq_no', v::intVal()->notEmpty(), true);

            return $this->ErrorCheck($validation, $date);
        }

        // 일정 아이템 추가 유형성 검증
        public function ValidationRelocation(array $date) {
            // 시간 및 순서 검증
            $result = $this->ValidationAddPlace($date);
            if ($result !== true) {
                return $result;
            }

            // 일정 아이템 검증
            $validation = v::key('place_id', v::notEmpty()->intVal(), true);

            return $this->ErrorCheck($validation, $date);
        }

        // 일정 아이템 순서 재배치 유효성 검증
        public function ValidationRelocationItem(array $date) {
            $validation = v::key('orders', v::arrayType()->each(
            v::keySet(
                v::key('item_id', v::intVal()->notEmpty(), true),
                v::key('new_seq_no', v::intVal()->notEmpty(), true)
            )
        ));

            return $this->ErrorCheck($validation, $date);
        }

        /**  @param  */
        public function ValidationDay(array $date) {
            // 일정 아이템 검증
            $validation = v::key('item_id', v::notEmpty()->intVal(), true);

            return $this->ErrorCheck($validation, $date);
        }

        /**  @param  */
        public function ValidationplaceId(array $date) {
            $validation = v::key('place_id', v::notEmpty()->intVal(), true);

            return $this->ErrorCheck($validation, $date);
        }
        
        /**  @param  */
        public function ValidationDayId(array $date) {
            $validation = v::key('day_no', v::notEmpty()->intVal(), true);

            return $this->ErrorCheck($validation, $date);
        }

        /**  @param  */
        public function ValidationTripId(array $date) {
            $validation = v::key('trip_id', v::notEmpty()->intVal(), true);

            return $this->ErrorCheck($validation, $date);
        }

        // // 지역 유효성 검증
        // /**  @param  */
        // public function ValidationRegion(array $date) {
        //     $validation = v::key('rid', v::notEmpty()->intVal());

        //     return $this->ErrorCheck($validation, $date);
        // }


        /**  @param 쿼리*/
        public function ValidationRegionSearch(array $date) {
            $validation = v::key('query', v::notEmpty()->stringType(), true);

            return $this->ErrorCheck($validation, $date);
        }

        /**  @param 쿼리*/
        // 외부 지도 장소 검색 유효성 검증
        public function ValidationPlace(array $date) {
            // 장소 검증
            $validation = v::key('place', v::notEmpty(), true)
                -> key('radius', v::stringType())
                -> key('lat', v::floatVal()->notEmpty())
                -> key('lng', v::floatVal()->notEmpty())
                -> key('sort', v::stringType())
                -> key('page', v::intVal());

            return $this->ErrorCheck($validation, $date);
        }

        /**  @param 쿼리*/
        // 일정 아이템 목록 유효성 검증
        public function ValidationItem(array $date) {
            $validation = v::key('page', v::intVal())
                        -> key('sort', v::stringType());
            
            return $this->ErrorCheck($validation, $date);
        }
    }

    
