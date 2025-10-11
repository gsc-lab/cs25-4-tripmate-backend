<?php
    namespace Tripmate\Backend\Modules\Auth\Services;

    use Tripmate\Backend\Common\Utils\Password;
    use Tripmate\Backend\Modules\Auth\Repositories\UserRepository;
    use Tripmate\Backend\Modules\Auth\Repositories\User;

    // 서비스 로직
    class AuthService {
        public UserRepository $repository;

        // 생성자에서 DB 호출
        public function __construct() {
            $this->repository = new UserRepository();
        }

        // 회원가입 로직
        public function RegisterServeices(array $data) {
            // 데이터 꺼내기
            $email = $data['email'];
            $password = $data['password'];
            $nickname = $data['nickname'];

            // 비밀번호 해쉬화
            $pwd_hash = Password::PasswordHash($password);
            
            // DB 실행
            $result = $this->repository->RegisterDB($email, $pwd_hash, $nickname);

            return $result;
        }

        // 로그인

        // 로그아웃
    }