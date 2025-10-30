<?php
    namespace Tripmate\Backend\Modules\Auth\Repositories;

    use Tripmate\Backend\Core\DB;
    use Tripmate\Backend\Core\Repository;
    use Tripmate\Backend\Common\Utils\Password;
    use Tripmate\Backend\Common\Exceptions\DbException;


    /**
     * 유저 관리(회원가입, 로그인) 리포지토리
     */
    class UserRepository extends Repository {
        public function __construct($db) {
            parent::__construct($db);
        }

        /**
         * 회원가입 리포지토리
         * - 
         * @param string $normalizedEmail
         * @param string $hashedPassword
         * @param string $nickname
         */
        public function createUser(string $normalizedEmail, string $hashedPassword, string $nickname) {

            return DB::transaction(function() use ($normalizedEmail, $hashedPassword, $nickname) {
                try {
                    // email 중복 확인
                    $selectSql = "SELECT email_norm FROM Users WHERE email_norm = :email";
                    $selectParm = ["email" => $normalizedEmail];
                    $result = $this->fetchOne($selectSql, $selectParm);

                    // 값이 존재할 경우
                    if ($result) {
                        throw new DbException(500,"USER_DUPLICATE_EMAIL", "이미 사용중인 이메일입니다.");
                    }

                    // 유저 생성
                    $insertSql = "INSERT INTO Users (email_norm, password_hash, name) VALUES (:email_norm, :password_hash, :name);";
                    $insertParm = ['email_norm' => $normalizedEmail, 'password_hash' => $hashedPassword, 'name' => $nickname];
                    $this->query($insertSql, $insertParm);
                
                } catch (\Throwable $e) {
                    throw new DbException("UNEXPECTED_ERROR", "회원가입 처리 중 알 수 없는 오류가 발생했습니다.", $e);
                }
            });
        }

        // 로그인 로직
        public function loginDB($email, $password) {
            return DB::transaction(function() use ( $email, $password ) {
                try {
                    $selectSql = "SELECT user_id, password_hash FROM Users WHERE email_norm = ?;";
                    $selectParm = ["email"=> $email]; 
                    $data = $this->fetchOne($selectSql, $selectParm);

                    // 이메일 조회 반환 값이 없을 경우
                    if(!$data) {
                        throw new DbException(500,"EMAIL", "있다");
                    }

                    // 조회한 데이터 꺼내기
                    $userId = $data['user_id'];
                    $pwdHash = $data['password_hash'];

                    // 비밀번호 검증
                    if(Password::verify($password, $pwdHash)) {
                        // JWT 발급
                        return $userId;
                    } else {
                        // 비밀번호가 알맞지 않을 경우
                        throw new DbException(500,'PASSWORD','');
                    }
                } catch (\Throwable $e) {
                    throw new DbException('user', '에러', $e);
                }
            });
        }
    }