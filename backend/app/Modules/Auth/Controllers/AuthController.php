<?php
    namespace Tripmate\Backend\Modules\Auth\Controllers;
    
    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Core\Request;
    use Tripmate\Backend\Core\Response;
    use Tripmate\Backend\Modules\Auth\Services\AuthService;
    use Tripmate\Backend\Core\Validator;
    use Tripmate\Backend\Common\Middleware\AuthMiddleware as amw;

    // 컨트롤러
    class AuthController extends Controller {
        public Validator $validator;
        public AuthService $serveices;

        // 공통 생성자
        public function __construct($request, $response) {
            // 부모 생성자 호출
            parent::__construct($request, $response);

            // 서비스 객체 생성
            $this->serveices = new AuthService();

            // 유효성 검증
            $this->validator = new Validator();

        }

        // 회원가입
        public function UserRegister() {
            // 요청 데이터
            $data = $this->request->body;

            // 유효성 검증
            $result = $this->validator->ValidationUserRegister($data);
            if($result === true) {
                // 서비스 연결
                $server_response = $this->serveices->RegisterServeices($data);
                
                // 응답 출력
                if ($server_response == "REGISTER_SUCCESS") {
                    $this->success(["회원가입 성공."]);
                } else if ($server_response == "DB_EXCEPTION") {
                    $this->error($server_response, "서버 오류입니다. 잠시 후 다시 시도해주세요.");
                } else {
                    $this->error($server_response, "중복된 이메일입니다. 다시 확인해주세요.");
                }
                
            } else {
                // 에러 메세지 출력
                $this->error($result, "입력값이 잘못되었습니다dfgdfg.");
            }
        }

        // 로그인
        public function UserLogin() {
            // 요청 데이터
            $data = $this->request->body;

            // 유효성 검증
            $result = $this->validator->ValidationUser($data);

            // 입력 유효 여부 
            if ($result) {
                // 서비스 연결
                $server_response = $this->serveices->LoginServeices($data);

                // 비밀번호 또는 jwt 발급 실패 시
                if ($server_response == "AUTH_FAILED") {
                    $this->error("AUTH_FAILED", "이메일 또는 비밀번호가 일치하지 않습니다.");
                } else if ($server_response == "DB_EXCEPTION") {
                    $this->error($server_response, "서버 오류입니다. 잠시 후 다시 시도해주세요.");
                } else {
                    $this->success(["access_token" => $server_response, "token_type" => "Bearer", "expires_in" => 43200]);
                }
            } else {
                // 에러 발생
                $this->error("AUTH_FAILED", "입력값이 유효하지 않습니다. 다시 한 번 확인해주세요.");
            }
        }

        // 로그아웃
        public function UserLogout() {
            // 토큰 검증
            $user_id = amw::tokenResponse($this->request);
            
            $this->success(["user_id" => $user_id, "message" => "로그아웃 되었습니다."]);
        }
    }