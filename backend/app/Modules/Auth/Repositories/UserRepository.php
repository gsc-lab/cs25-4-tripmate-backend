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

        // 이메일 중복 검증 로직
        public function findEmail($normalizedEmail) {
            $selectSql = "SELECT email_norm FROM Users WHERE email_norm = :email";
            $selectParm = ["email" => $normalizedEmail];
            return $this->execute($selectSql, $selectParm);
        }

        // 회원 저장 로직 
        public function createUser(string $normalizedEmail, string $hashedPassword, string $nickname) {
            // 유저 생성
            $insertSql = "INSERT INTO Users (email_norm, password_hash, name) VALUES (:email_norm, :password_hash, :name);";
            $insertParm = ['email_norm' => $normalizedEmail, 'password_hash' => $hashedPassword, 'name' => $nickname];
            $this->query($insertSql, $insertParm);
        }

        // 이메일과 비밀번호를 검증하고 user_id를 반환.
        public function getVerifiedUserId($email, $password) {
            $selectSql = "SELECT user_id, password_hash FROM Users WHERE email_norm = :email;";
            $selectParm = ["email"=> $email]; 
            $data = $this->fetchOne($selectSql, $selectParm);

            // 이메일 조회 반환 값이 없을 경우
            if(!$data) {
                throw new DbException("NOT_EMAIL_FOUND");
            }
            $userId = $data['user_id'];
            $pwdHash = $data['password_hash'];

            if(!$data || Password::verify($password, $pwdHash) === false) {
                throw new DbException("LOGIN_FAILED");
            }

            return $userId;
        }

        public function findUser(string $email, string $password) {
            try {
                // 1. 공통 인증 함수 호출
                $userId = $this->getVerifiedUserId($email, $password);

                return $userId;
            } catch(DbException $e) {
                if($e->getMessage() === 'NOT_EMAIL_FOUND' || $e->getMessage() === 'PASSWORD_NOT') {
                    throw new DbException("LOGIN_FAILED", "이메일 또는 비밀번호가 올바르지 않습니다.", $e);
                }
            }

        }
    }