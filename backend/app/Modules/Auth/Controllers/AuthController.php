<?php
    namespace Tripmate\Backend\Modules\Auth\Controllers;
    
    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Core\Request;
    use Tripmate\Backend\Core\Response;
    use Tripmate\Backend\Modules\Auth\Services\AuthService;
    use Tripmate\Backend\Core\Validator;

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
                    $this->success(["code" => $server_response, "data" => "회원가입 성공."]);
                } else {
                    $this->error($server_response, "중복된 이메일입니다. 다시 확인해주세요.");
                }
                
            } else {
                // 에러 메세지 출력
                $this->error($result, "입력값이 잘못되었습니다.");
            }
        }

        // 로그인
        public function UserLogin() {
            // 요청 데이터
            $data = $this->request->body;
        }

        // 로그아웃
    }
    
