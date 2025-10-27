<?php
// namespace App\Core;
namespace Tripmate\Backend\Core;

// 2. use 작성
use Tripmate\Backend\Common\Exceptions\HttpException;
use Tripmate\Backend\Common\Middleware\AuthMiddleware;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;

// 3. 공통 컨트롤러 클래스
class Controller { 

  // 4. 프로퍼티 정의 (요청/응답) 
  protected Request $request;
  protected Response $response;
  // protected ?Validator $validator = null; // 유효성 검증 도구
  
  
  // 4. 생성자 (request, response 초기화) 
  public function __construct(Request $request, Response $response) {
    $this->request = $request;
    $this->response = $response;
  }

  // 5. 실행 메서드
  // - HttpExceptions -> 표준 에러 JSON 응답 처리
  // - 알 수 없는 예외 -> 500 에러 응답 처리
  protected function run(\Closure $action) : void {
    try {
      // 5-1. 액션 실행
      $result = $action();
      // 5-2. 액션 내에서 집접 응답을 완료한 경우(null 반환)
      if ($result === null) {
        return; 
      }

      // 5-3. 액션 결과가 Response 인스턴스인 경우 그대로 반환
      $this->response->success($result);
    
    } catch (HttpException $e) {
      // 5-4. HttpExceptions 예외 처리
      $this->response->error(
        $e->getCodeName(),
        $e->getMessage(),
        $e->getStatus()
      );

    } catch (\Throwable $e) {
      // 5-5. 알 수 없는 예외 처리 (500 에러)
      $this->response->error(
        'INTERNAL_SERVER_ERROR',
        '서버 내부 오류가 발생했습니다.',
        500
      );
    }  
  }

  // 6. userId 반환 및 토큰 검증 메서드
  function getUserId() : int {
    return AuthMiddleware::tokenResponse($this->request);
  }

  // 7. userId 토큰 검증 메서드 
  // - 인증만 진행 
  function riquireAuth() : void {
    // 반환 없이 토큰 검증
    $this->getUserId();
  }

  // 8. pagenation 파라미터 파싱 메서드
  // - page, size, sort 파라미터를 정리하여 반환
  protected function parsePaging(
    int $defaultPage = 1,         // 기본 페이지
    int $defaultSize = 20,        // 기본 페이지 크기
    int $maxSize = 100            // 최대 페이지 크기
  ): array {
    // 8-1. 기본값 설정
    $page = (int)($this->request->query['page'] ?? $defaultPage);
    $size = (int)($this->request->query['size'] ?? $defaultSize);

    // 8-2. 유효성 검증
    if ($page < 1) $page = $defaultPage;
    if ($size < 1) $size = $defaultSize;
    if ($size > $maxSize) $size = $maxSize;

    // 8-3. 정렬 파라미터
    $sort = $this->request->query('sort');

    // 8-4. 결과 반환
    return [
      'page' => $page,
      'size' => $size,
      'sort' => $sort
    ];
  }

}

