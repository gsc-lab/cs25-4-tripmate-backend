<?php
    namespace Tripmate\Backend\Modules\Places\Services;

    use Tripmate\Backend\Common\Exceptions\DbException;
    use Tripmate\Backend\Core\Service;
    use Tripmate\Backend\Modules\Places\Repositories\PlacesRepository;
    use Tripmate\Backend\Modules\Places\Services\GoogleApi;
    use Tripmate\Backend\Core\DB;

    /**
     * 장소 관련 서비스
     */
    class PlacesService  extends Service{
        private $repository;

        public function __construct() {
            parent::__construct(DB::conn());
            $this->repository = new PlacesRepository(DB::conn());
        }

        // 외부 API 엔드포인트
        private const API_TEXT_SEARCH = "https://places.googleapis.com/v1/places:searchText";
        private const API_REVERS_GEOCODING = "https://maps.googleapis.com/maps/api/geocode/json?latlng=";
        private const API_NEARBY = "https://places.googleapis.com/v1/places:searchNearby";
        
        
        // FieldMask 정의
        private const MASK_SEARCH = "places.id,places.displayName,places.formattedAddress,places.location,places.primaryTypeDisplayName";
        private const MASK_NEARBY = "places.id,places.displayName,places.formattedAddress,places.location,places.primaryTypeDisplayName";
        
        
        /**
         * 외부 API를 불러와 장소 검색
         * @param mixed $place
         * @param mixed $token
         * @return array{data: array, meta: array{next_page_token: mixed}}
         */
        public function searchByText($place, $token) {
            // 본문 작성
            $postData = [
                'languageCode' => 'ko',
                'pageSize' => 5
            ];

            // 페이지네이션
            if (!empty($token)) {
                $postData['pageToken'] = $token; // pagetoken만 사용(textQuery 사용 불가)
            } else {
                $postData['textQuery'] = $place; // 첫 페이지는 place로 요청
            }

            // 헤더 작성
            $headers = ['X-Goog-FieldMask:' . self::MASK_SEARCH];
            
            // 외부 API 요청
            $result = GoogleApi::post(self::API_TEXT_SEARCH, $postData, $headers);
            
            return $this->placeResponse($result);
        }

        // 좌표 기준 주소로 변경
        public function getAddressFromCoordinates($lat, $lng) {
            // API 요청
            $result = GoogleApi::get(self::API_REVERS_GEOCODING, ['latlng'=> $lat . ',' . $lng ]);

            return ['data' => $result['results'][0]['formatted_address']];
            }

        // 좌표를 장소로
        public function getPlaceFromCoordinates($placeId) {
            
        }


        // 내 주변 지역 장소 검색
        public function nearbyPlaces($lat, $lng, $radius) {
            $headers = ['X-Goog-FieldMask:' . self::MASK_NEARBY];

            // 본문 작성
            $postData = [
                'languageCode' => 'ko', 
                'pageSize' => 20,     
                'locationRestriction' => [
                'circle' => [
                    'center' => [
                        'latitude' => $lat,
                        'longitude' => $lng
                    ],
                    'radius' => $radius
                ]
            ]
            ];

            // 함수 실행
            $result = GoogleApi::post(self::API_NEARBY, $postData, $headers);
            
            return $this->placeResponse($result);
        }

        // 반환값 재사용 함수
        public function placeResponse($result) {
            // 데이터 처리
            $formattedPlaces = [];

            if (!empty($result['places'])) {
                foreach ($result['places'] as $place) {
                    $formattedPlaces[] = [
                        'place_id' => $place['id'] ?? null,
                        'name' => $place['displayName']['text'] ?? '이름 없음',
                        'address' => $place['formattedAddress'] ?? null,
                        'lat' => $place['location']['latitude'] ?? null,
                        'lng' => $place['location']['longitude'] ?? null,
                        'category' => $place['primaryTypeDisplayName'] ?? '기타'
                    ];
                }
            }

            // 페이지네이션
            $nextToken = $result['nextPageToken'] ?? null;

            return ($formattedPlaces + $nextToken);
        }
        



        // 외부 결과 중 하나를 내부로 저장
        public function upsert($data) {
            try {
                $this->transaction(function () use ($data) {
                    // data값 꺼내기
                    $name = $data['name'];
                    $category = $data['category'];
                    $address = $data['address'];
                    $externalRef = $data['external_ref'];
                    $lat = $data['lat'];
                    $lng = $data['lng'];
                    
                    // DB 전달
                    $result = $this->repository->upsertRepository($name, $category, $address, $externalRef, $lat, $lng);

                    return $result;
                });
            } catch (DbException $e) {
            }
        }

        // 장소 단건 조회
        public function singlePlace($placeId) {
            try {
                $this->transaction(function () use ($placeId) {
                    // db 전달
                    $result = $this->repository->placeRepository($placeId);

                    return $result;
                });
            } catch (DbException $e) {
            
            }
        }
    }