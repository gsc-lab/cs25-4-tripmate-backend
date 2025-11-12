<?php
    namespace Tripmate\Backend\Modules\Users\Controllers;

    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Modules\Users\Services\UsersService;
    use Tripmate\Backend\Core\Validator;

    /**
     * 유저 관리(정보 조회, 회원 탈퇴)
     */
    class UsersController extends Controller{
        private UsersService $service;
        private Validator $validator;

        public function __construct($request, $response) {
            parent::__construct($request, $response);
            $this->service = new UsersService();
            $this->validator = new Validator();
        }

        /**
         * 내 정보 조회 컨트롤러
         * - 토큰 검증 후 사용자 정보 반환
         * - 성공 시 데이터(200OK, email, nickname) 반환
         */
        public function userMyPage() {
            return $this->run(function() {
                $userId = $this->getUserId(); // 토큰 검증

                $result = $this->service->myPage($userId);

                return $result;
            });
        }

        /**
         * 회원 탈퇴 컨트롤러
         * 토큰 검증 후 회원 삭제
         * 성공 시 200 NoContent 반환
         * @return \Tripmate\Backend\Core\Response
         */
        public function userSecession() {
            return $this->run(function() {
                $userId = $this->getUserId(); // 토큰 검증

                $data = $this->request->body();
                $this->validator->validatePassword($data);
                $password = $data["password"];

                $this->service->secession($userId, $password);

                return $this->response->noContent();
            });
        }
    }