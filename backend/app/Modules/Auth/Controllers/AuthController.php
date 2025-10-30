<?php
    namespace Tripmate\Backend\Modules\Auth\Controllers;
    
    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Modules\Auth\Services\AuthService;
    use Tripmate\Backend\Core\Validator;
    
    /**
     * AuthController
     * - 회원 인증 관련 요청(회원가입, 로그인, 로그아웃) 처리 컨트롤러.
     */
    class AuthController extends Controller {
        private Validator $validator;
        private AuthService $services;

        public function __construct($request, $response) {
            parent::__construct($request, $response);
            $this->services = new AuthService();
            $this->validator = new Validator();
        }

        /**
         * 회원가입 처리
         * - 요청 데이터 유효성 검증 후 서비스 연결
         * - 성공 시 (204 No Content) 응답
         */
        public function register() {

            return $this->run(function() {
                $data = $this->request->body();
                $this->validator->validateUserRegister($data);
                $this->services->registerUser($data);

                return $this->response->noContent();
            }); 
        }

        /**
         * 로그인 컨트롤러
         * - 유효성 검증 후 서비스 호출로 JWT 발급
         * - 성공 시 토큰 응답 반환
         */
        public function login() {

            return $this->run(function() {
                $data = $this->request->body();
                $this->validator->validateUser($data);
                $serverResponse = $this->services->loginUser($data);
                
                return [
                    "access_token" => $serverResponse, 
                    "token_type" => "Bearer", 
                    "expires_in" => 43200 // 12시간
                ];
            });
        }

        /**
         * 로그아웃 컨트롤러
         * -유효한 토큰인 경우 성공 처리(204 No Connect)
         */
        public function logout() {

            return $this->run(function() {
                $this->requireAuth();
                
                return $this->response->noContent();
            });
        }
    }