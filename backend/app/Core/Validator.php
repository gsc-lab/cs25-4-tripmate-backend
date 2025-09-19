<?PHP
    namespace App\ValidationServices;  // 네임스페이스 정의 (APP가 찾게 하기 위해)

    // validator, NestedValidatonException 라이브러리 가져와 사용
    use Respect\Validation\Validator as v;
    use Respect\Validation\Exceptions\NestedValidationException as nve;

    // 클래스 정의
    class Validation {
        // 에러 정의
        private function ErrorCheck($validation, $date) {
            try{
                $validation->assert($date);
            } catch(nve $e) {
                return $e->getMessages();
            }

            return true;
        }

        // 사용자 로그인 및 회원가입 규칙
        public function ValidationUser(array $date) {
            // email, password 유효성 검증
            $validation = v::key('email', v::email()->notEmpty())
                        -> key('password', v::alnum()->length(9, null)->notEmpty());

            // 에러 확인
            return $this->ErrorCheck($validation, $date);
        } 

        public function ValidationUserRegister(array $date) {
            // nickname 유효성 검증
            $validation = v::key('nickname', v::notEmpty()->length(2, 50));

            return $this->ErrorCheck($validation, $date);
        }

        // 여행 관련 유효성 검증
        public function ValidationTrip(array $date) {
            // title, region_id, start/end_date 검증
            $validation = v::key('title', v::length(1, 100)->notEmpty())
                        -> key('region_id', v::intVal()->notEmpty())
                        -> key('start_date', v::dateTime()->notEmpty())
                        -> key('end_date', v::dateTime()->notEmpty());

            return $this->ErrorCheck($validation, $date);
        }

        /**  @parm  */
        public function ValidationTripId(array $date) {
            $validation = v::key('tid', v::notEmpty()->intVal());

            return $this->ErrorCheck($validation, $date);
        }

        // 속성 관련 유효성 검증
        public function ValidationMemo(array $date) {
            // memo 검증
            $validation = v::key('memo', v::length(null, 255));

            return $this->ErrorCheck($validation, $date);
        }
        
        // 일차 순서 유효성 검증
        public function ValidationDays(array $date) {
            // 날짜 검증
            $validation = v::key('day_ids', v::arrayType()->notEmpty());
            
            return $this->ErrorCheck($validation, $date);
        }

        /**  @parm  */
        public function ValidationDayId(array $date) {
            $validation = v::key('did', v::notEmpty()->intVal());

            return $this->ErrorCheck($validation, $date);
        }

        // 장소 관련 유효성 검증
        public function ValidationPlace(array $date) {
            // 장소 검증 (with 사용하여 객체 내 까지 확인)
            $validation = v::key('place', v::keySet(
                v::key('external_id', v::notEmpty())
                -> key('name', v::length(1, 255)->notEmpty())
                -> key('address', v::length(1, 255)->notEmpty())
                -> key('lat', v::floatVal()->notEmpty())
                -> key('lng', v::floatVal()->notEmpty())
                -> key('category_id', v::intVal()->notEmpty())
            ));

            return $this->ErrorCheck($validation, $date);
        }

        public function ValidationPlaceCategory(array $date) {
            $validation = v::key('place', v::keyset(
                        v::key('category_id', v::intVal()->notEmpty())));

            return $this->ErrorCheck($validation, $date);
        }

        public function ValidationAddPlace(array $date) {
            $validation = v::key('visit_time', v::date()->notEmpty());

            return $this->ErrorCheck($validation, $date);
        }

        /**  @parm  */
        public function ValidationplaceId(array $date) {
            $validation = v::key('pid', v::notEmpty()->intVal());

            return $this->ErrorCheck($validation, $date);
        }

        // 일정 아이템 유형성 검증
        public function ValidationRelocation(array $date) {
            // 일정 아이템 검증
            $validation = v::key('item_ids', v::notEmpty()->arrayType());

            return $this->ErrorCheck($validation, $date);
        }

        /**  @parm  */
        public function ValidationDay(array $date) {
            // 일정 아이템 검증
            $validation = v::key('iid', v::notEmpty()->intVal());

            return $this->ErrorCheck($validation, $date);
        }

        // 지역 유효성 검증
        /**  @parm  */
        public function ValidationRegion(array $date) {
            $validation = v::key('rid', v::notEmpty()->intVal());

            return $this->ErrorCheck($validation, $date);
        }

        /**  @parm 쿼리*/
        public function ValidationRegionSearch(array $date) {
            $validation = v::key('query', v::notEmpty()->stringType());

            return $this->ErrorCheck($validation, $date);
        }
    }

    
?>