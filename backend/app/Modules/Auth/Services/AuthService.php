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

            try {   
                return $this->transaction(function() use ($data) {
                    $email = $data['email'];
                    $password = $data['password'];
                    $nickname = $data['nickname'];

                    // 이메일 중복 확인
                    $normalizedEmail = strtolower($email); // 이메일 정규화
                    $result = $this->repository->findEmail($normalizedEmail);
                    if ($result) {
                        throw new HttpException(409, 'USER_DUPLICATE_EMAIL', '이미 사용중인 이메일입니다.');
                    }

                    // 회원 데이터 저장
                    $hashedPassword = Password::hash($password);
                    return $this->repository->createUser($normalizedEmail, $hashedPassword, $nickname);
                    });

                } catch (DbException $e) {
                    error_log('여기서 에러가나서요');
                    throw new HttpException(500, 'UNEXPECTED_ERROR', "회원가입 중 알 수 없는 에러가 발생하였습니다.", $e);
                }
            }

        /**
         * 로그인 서비스
         * - DB 검증 후 토큰 값 반환
         * @param array $data
         * @return string 
         */
        public function loginUser($data) {
            try {
                return $this->transaction(function() use ($data) {
                    $email = $data['email'];
                    $password = $data['password'];

                    // 유저 검증
                    $userId = $this->repository->findUser($email, $password);

                    return amw::tokenRequest($userId);
                });
            } catch (DbException $e) {
                throw new HttpException(500, 'LOGIN_ERROR', '로그인 도중 알 수 없는 에러가 발생하였습니다.', $e);
            }
        }
    }