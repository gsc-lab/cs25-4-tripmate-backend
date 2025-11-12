<?php
    namespace Tripmate\Backend\Modules\Users\Repositories;

    use Tripmate\backend\Core\DB;
    use Tripmate\Backend\Core\Repository;
    use Tripmate\Backend\Common\Utils\Password;

    class UsersReadRepository extends Repository {
        public function __construct($db) {
            parent::__construct($db);
        }

        // 내 정보 조회
        public function find($userId) {
            $query = "SELECT user_id, email_norm AS email, name FROM Users WHERE user_id = :user_id;";
            $parm = ["user_id" => $userId];
            $data = $this->fetchOne($query, $parm);
            if (!$data) {
                return null;
            }

            return ["email" => $data["email"], "nickname" => $data["name"]];
        }

        // 회원 탈퇴
        public function delete($userId, $password) {
            // 비밀번호 검증
            $sql = "SELECT user_id, password_hash FROM Users WHERE user_id = :user_id;";
            $param = ["user_id" => $userId]; 
            $data = $this->fetchOne($sql, $param);

            if ($data && Password::verify($password, $data['password_hash'])) {
                $deleteSql = "DELETE FROM Users WHERE user_id = :user_id;";
                $result = $this->query($deleteSql, $param);   
            } else {
                $result = 0;
            }

            return $result;
        }
    }