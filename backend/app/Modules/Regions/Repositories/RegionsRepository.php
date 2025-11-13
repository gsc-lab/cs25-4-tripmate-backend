<?php
    namespace Tripmate\Backend\Modules\Regions\Repositories;

    use Tripmate\Backend\Core\DB;
    use Tripmate\Backend\Core\Repository;

    /**
     * 지역 리포지토리
     */
    class RegionsRepository extends Repository {
        public function __construct($db) {
            parent::__construct($db);
        }

        /**
         * 지역 매칭 리포지토리
         * @param mixed $region
         */
        public function selectRegion($region) {
            $sql = "SELECT region_id, name, country_code
                    FROM Region WHERE name LIKE CONCAT('%', :region, '%');";
            $param = ["region" => $region];
            $data = $this->fetchAll($sql, $param);
            if(empty($data)) {
                return null;
            }

            return $data;
        }

        /**
         * 지역 조회 리포지토리
         */
        public function getSelectRegion($country) {
            $sql = "SELECT *
                    FROM Region WHERE country_code = :country;";
            $param = ["country"=> $country];
            $data = $this->fetchAll($sql, $param);

            return $data;
    }
}