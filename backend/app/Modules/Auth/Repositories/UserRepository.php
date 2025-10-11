<?php
    namespace Tripmate\Backend\Modules\Auth\Repositories;

    use Tripmate\Backend\Core\DB;

    // DB 로직 작성
    class UserRepository {
        public DB $db;

        public function __construct() {
            // DB 객체 생성
            $this->db = new DB();
        }

        // 회원가입 로직
        public function RegisterDB($email, $pwd_hash, $nickname) {
            // DB 연결
            $pdo = $this->db->getConnection();

            // 트레젝션
            $pdo->beginTransaction();

            // email 중복 검사
            $result = $pdo->prepare("SELECT email_norm FROM Users WHERE email_norm = :email");
            if (!$result->execute(['email' => $email])) {
                $pdo->rollback();
            }

            // 반환 값이 있는지 확인
            if ($result->fetch()) {
                return "DUPLICATE_EMAIL";
            } 

            // 중복 값이 없을 경우 값 넣기
            $insert = $pdo->prepare("INSERT INTO Users (email_norm, password_hash, name) VALUES (:email_norm, :password_hash, :name);");
            
            if ($insert->execute(['email_norm' => $email, 'password_hash' => $pwd_hash, 'name' => $nickname])) {
                // DB 처리 완료
                $pdo->commit();

                // 성공 반환
                return "REGISTER_SUCCESS";

            } else {
                $pdo->rollback();
                return "DB_ERROR";
            }
        }
        // 로그인 로직

        // 로그아웃 로직
    }