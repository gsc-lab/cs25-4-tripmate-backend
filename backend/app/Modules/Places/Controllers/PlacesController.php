<?php
    namespace Tripmate\Backend\Modules\Places\Controllers;

    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Core\Validator;
    use Tripmate\Backend\Common\Middleware\AuthMiddleware as amw;
    use Tripmate\Backend\Modules\Places\Services\PlacesService;

    class PlacesController extends Controller {
        public Validator $validator;
        public PlacesService $service;

        public function __construct($request, $response) {
            parent::__construct($request, $response);

            $this->validator = new Validator();
            $this->service = new PlacesService();
        }

        // 임시 CORS
        public function cors() {
            header("Access-Control-Allow-Origin: *"); // 모든 출처(도메인, 포트) 허용
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // 요청 방식 허용
            header("Access-Control-Allow-Headers: Content-Type, Authorization"); 

            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { // 사전 요청 처리
                http_response_code(200);
                exit();
            }
        }
        
        /**
         * 장소 검색 컨트롤러
         * API를 호출하여 장소 검색 후 장소 반환
         */
        public function search() {

            return $this->run(function() {
                $query = $this->request->query();
                $this->validator->validatePlace($query);

                $place = $query['place'] ?? null;
                $token = $query['pageToken'] ?? null;

                $result = $this->service->searchByText($place, $token);

                // 응답 형식 변형
                $data = $result['data'];
                $meta = $result['meta'];
        
                return $this->response->success($data, $meta);
            });
        }

        /**
         * Geocoding (좌표->주소) 변환 컨트롤러
         */
        public function reverseGeocoding() {

            return $this->run(function() {
                $query = $this->request->query();
                $this->validator->validatereverseGeocoding($query);

                $lat = $query['lat'];
                $lng = $query['lng'];

                $result = $this->service->getAddressFromCoordinates($lat, $lng);
                
                return $result;
            });
        }

        /**
         * 장소의 Id 받아 장소 반환 컨트롤러
         */
        public function placeGeocoding($lat, $lng) {
            return $this->run(function() {
                $query = $this->request->query();
                $this->validator->validatePlaceGeocoding($query);

                $placeId = $query['place_id'];

                $result = $this->service->getPlaceDetailsById($placeId);
                
                return $result;
            });
        }

        /**
         * 주변 지역 검색
         */
        public function searchNearby($lat, $lng) {
            return $this->run(function() {
                $query = $this->request->query();
                $this->validator->validateReverseGeocoding($query);

                $lat = $query['lat'];
                $lng = $query['lng'];
                $radius = 1000; // 고정값

                $result = $this->service->nearbyPlaces($lat, $lng, $radius);
                
                // 응답 형식 변형
                $data = $result['data'];
                $meta = $result['meta'];
        
                return $this->response->success($data, $meta);
            });
        }

        /**
         * 사용자가 선택한 외부 결과 중 하나 내부 저장
         */
        public function placeUpsert() {
            return $this->run(function() {
                $this->requireAuth();

                $data = $this->request->body();
                $this->validator->validatePlaceCategory($data);
                    
                $place = $this->service->upsert($data);

                return $place;
            });
        }

        // 단건 조회
        public function singlePlaceSearch(int $placeId) {
            return $this->run(function() use ($placeId) {
                
                $result = $this->service->singlePlace($placeId);
            
                return $result;
            });
        }
    } 