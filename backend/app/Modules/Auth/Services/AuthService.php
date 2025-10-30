<?php
    namespace Tripmate\Backend\Modules\Auth\Services;

    use Tripmate\Backend\Common\Exceptions\DbException;
use Tripmate\Backend\Common\Exceptions\HttpException;
use Tripmate\Backend\Common\Utils\Password;
    use Tripmate\Backend\Core\Service;
    use Tripmate\Backend\Core\DB;
    use Tripmate\Backend\Modules\Auth\Repositories\UserRepository;
    use Tripmate\Backend\Common\Middleware\AuthMiddleware as amw;

    /**
     *  유저관리(회원가입, 로그인, 로그아웃) 서비스
     */
    class AuthService extends Service {
        private UserRepository $repository;

        /**
         * 생성자 호출
         * - 부모 생성자 PDO 주입 및 리포지토리 초기화
         */
        public function __construct() {
            parent::__construct(DB::conn());
            $this->repository = new UserRepository($this->db);
        }

        /**
         * 회원가입 서비스
         * - 비밀번호 해쉬 후 DB 저장 호출
         * @param array $data
         * @return string
         */
        public function registerUser(array $data) {

            return $this->transaction(function() use ($data) {
                try {
                    $email = $data['email'];
                    $password = $data['password'];
                    $nickname = $data['nickname'];

                    $hashedPassword = Password::hash($password);

                    $normalizedEmail = strtolower($email); // 이메일 정규화
                    
                    return $this->repository->createUser($normalizedEmail, $hashedPassword, $nickname);
                } catch (DbException $e) {
                    switch ($e->getCode()) {
                        case 'USER_DUPLICATE_EMAIL':
                            throw new HttpException(500, $e->getMessage(), $e->getCode());
                        case 'UNEXPECTED_ERROR':
                            throw new HttpException(500, $e->getMessage(), $e->getCode());
                    }
                }
            });
        }

        /**
         * 로그인 서비스
         * - DB 검증 후 토큰 값 반환
         * @param array $data
         * @return string 
         */
        public function loginUser($data) {
            return $this->transaction(function() use ($data) {
                $email = $data['email'];
                $password = $data['password'];

                $result = $this->repository->loginDB($email, $password);

                return amw::tokenRequest($result);
            });
        }
    }