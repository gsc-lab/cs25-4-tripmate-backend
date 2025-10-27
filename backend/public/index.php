<?php 
// AltoRouter 라이브러리 로드
require_once __DIR__ . '/../vendor/autoload.php';

// use 작성
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Common\Exceptions\HttpException;


// 1. 공용 객체 생성 (request, response, AutoRouter)
$request = Request::fromGlobals(); 
$response = new Response();
$router = new AltoRouter();

// 2. 모듈 라우터 자동 등록 
// - 각 Modules/*/Routes.php 는 다음 시그니처의 콜러블을 return 해야 함
// - function (AltoRouter $router, Request $request, Response $response): void
foreach (glob(__DIR__ . '/../app/Modules/*/Routes.php') as $routeFile) {
    $register = require $routeFile;
    if (!is_callable($register)) {
        error_log("Routes.php must return a callable: {$routeFile}");
        continue; // 콜러블 아닌 파일은 스킵
    }
    $register($router, $request, $response);
}
// 3. 매칭
$match = $router->match();

// 4. 매칭 후 error 처리 (404)
if ($match === false) {
    $response->error('NOT_FOUND', 'Route not found', 404);
    exit;
}

// 5. 라우트 파라미터를 request (setAttributes)에 주입 
// - 컨트롤러에서 $request->getAttribute('paramName') 으로 접근 가능
if (!empty($match['params']) && is_array($match['params'])) {
    foreach ($match['params'] as $key => $value) {
        $request->setAttribute($key, $value);
    }
}

// 6. 콜백 실행
// - 에러 일괄 처리
$target = $match['target'];

// 7. 예외 처리
try {
  // 7-1. 잘못된 타겟 타입 처리
  if (!is_callable($target)) {
      // - 500 : 잘못된 타겟 타입 
      // - Invalid route target : 라우트 타겟이 콜러블이 아님
      $response->error('INTERNAL_ERROR', '라우트 타켓이 콜러블이 아닙니다', 500);
      exit;
  }

  // 7-2. 컨트롤러에 request, response을 기본 인자로 전달
  // - 라우트에서 개별 파라피터가 필요한 경우
  // - $request->getAttribute('paramName') 으로 접근
  $result = call_user_func($target, $request, $response);

} catch (HttpException $e) {
  // 7-3. HttpExceptions 예외 처리
  $status = $e->getStatus();
  $code = $e->getCodeName();
  $message = $e->getMessage();

  // 7-4 . 에러 응답 전송
  $response->json([
    'success' => false,
    'error' => [
      'code' => $code,
      'message' => $message
    ]
  ], $status);
  
} catch (\Throwable $e) {
  // 7-5. 알 수 없는 예외 처리 (500 에러)
  error_log(...); // 에러 로깅
  $response->error(
    'INTERNAL_SERVER_ERROR',
    '서버 내부 오류가 발생했습니다.',
    500
  );
}

