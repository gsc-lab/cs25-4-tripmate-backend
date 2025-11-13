<?php
    namespace Tripmate\Backend\Modules\Regions\Controllers;

    use Tripmate\Backend\Core\Controller;
    use Tripmate\Backend\Modules\Regions\Services\RegionsService;
    use Tripmate\Backend\Core\Validator;

    /**
     * 지역 컨트롤러(지역 검색)
     */
    class RegionsController extends Controller{
        private RegionsService $service;
        private Validator $validator;

        public function __construct($request, $response) {
            parent::__construct($request, $response);
            $this->service = new RegionsService();
            $this->validator = new Validator();
        }

        /**
         * 지역 검색 컨트롤러
         * 
         */
        public function getRegion() {
            return $this->run(function() {
                $region = $this->request->query(); // 지역 이름 데이터
                $this->validator->validateRegionSearch($region);

                $query = $region['query'] ?? null;
                $country = $region['country'] ?? 'KR';

                // 쿼리가 있을 경우
                if (!empty($query)) {
                    return $this->service->searchRegions($query);
                }

                return $this->service->listRegions($country);
            });
        }
    }