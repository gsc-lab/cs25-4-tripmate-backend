<?php
// namespace 작성
namespace Tripmate\Backend\Modules\TripDays\Controllers;

// use 작성
use Tripmate\Backend\Common\Middleware\AuthMiddleware;
use Tripmate\Backend\Core\Controller;
use Tripmate\Backend\Modules\TripDays\Services\TripDaysService;
use Tripmate\Backend\Core\Request;
use Tripmate\Backend\Core\Response;
use Tripmate\Backend\Core\Validator;

// TripDaysController 클래스 작성
class TripDaysController extends Controller { 

  // 1. 프로퍼티 정의
  public TripDaysService $tripDaysService;
  public Validator $validator;

  // 2. 생성자에서 Request, Response, TripDaysService, Validator 초기화
  function __construct(Request $request, Response $response) {
    // 2-1. 부모 생성자 호출
    parent::__construct($request, $response);
    // 2-2. TripDaysService 인스턴스 생성
    $this->tripDaysService = new TripDaysService();
    // 2-3. Validator 인스턴스 생성
    $this->validator = new Validator();
  }

  // 3. trip day 생성 : POST /api/v1/trips/{trip_id}/days

}


