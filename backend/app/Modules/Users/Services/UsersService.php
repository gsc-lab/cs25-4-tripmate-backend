<?php
    namespace Tripmate\Backend\Modules\Users\Services;
    
    use Tripmate\Backend\Common\Exceptions\DbException;
    use Tripmate\Backend\Common\Exceptions\HttpException;
    use Tripmate\Backend\Core\Service;
    use Tripmate\Backend\Modules\Users\Repositories\UsersReadRepository;
    use Tripmate\Backend\Core\DB;
    use Tripmate\Backend\Modules\Auth\Repositories\UserReadRepository;
use Tripmate\Backend\Modules\Auth\Repositories\UserRepository;

    /**
     * 유저관리 서비스
     */
    class UsersService extends Service{
        private UsersReadRepository $repository;
        private UserRepository $userRepository;

        public function __construct() {
            parent::__construct(DB::conn());
            $this->repository = new UsersReadRepository($this->db);
            $this->userRepository = new UserRepository($this->db);
        }

        /**
         * 내 정보 조회 서비스
         * @param mixed $userId
         * @return array{created_at: mixed, email: mixed, nickname: mixed|string}
         */
        public function myPage($userId) {
            try {
                return $this->transaction(function() use($userId) {
                    $result = $this->repository->find($userId);
                    if ($result == null) {
                        throw new HttpException(404,"USER_NOT_FOUNT", "해당 유저를 찾을 수 없어 조회에 실패했습니다.");
                    }

                    return $result;
                    });

            } catch (DbException $e) {
                throw new HttpException(500, "NOT_USERPAGE_DATA", "페이지의 데이터를 불러오는데에 실패했습니다.", $e);
            }
        }

        /**
         * 회원탈퇴 서비스
         * @param mixed $userId
         * @return string
         */
        public function secession($password, $email) {
            try {
                return $this->transaction(function() use($password, $email) {
                    try {
                        // 패스워드 및 이메일 검증
                        $userId = $this->userRepository->getVerifiedUserId($email, $password);
                    } catch (DbException $e) {
                        // 인증 실패는 401(Unauthorized)로 변환
                        throw new HttpException(401, "AUTH_FAILED", "이메일 또는 비밀번호가 일치하지 않습니다.");
                    }

                    // 검증 성공 시 회원 삭제
                    $result = $this->repository->delete($userId);
                    if ($result === 0) {
                        throw new HttpException(404, "USER_NOT_FOUND", "삭제할 유저를 찾을 수 없습니다.");
                    }
                });
            } catch (DbException $e) {
                throw new HttpException(500, "USER_DELETE_FAIL", "회원 삭제에 실패하였습니다.", $e);
            }
        }
    }
