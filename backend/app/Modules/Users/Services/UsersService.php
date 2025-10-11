<?php
    namespace Tripmate\Backend\Modules\Users\Services;
    
    use Tripmate\Backend\Modules\Users\Repositories\UsersReadRepository as Repository;

    class UsersService {
        public Repository $repository;

        // 생성자 
        public function __construct() {
            // db 객체
            $this->repository = new Repository();
        }

        // 내 정보 조회
        public function UserMyPageService($user_id) {
            //db 호출
            $result = $this->repository->UserMyPageRepository($user_id);
            
            return $result;
        }

        // 회원 탈퇴
        public function UserSecessionService($user_id) {
            // DB에 전달
            $result = $this->repository->UserSecessionRepository($user_id);
        
            return $result;
        }
    }
