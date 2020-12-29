<?php

class MyControllerErgotrack extends modRestController {
    public $classKey = 'fbuchErgotrack';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function post() {
        $properties = $this->getProperties();


        $objectArray = $properties;

        $cacheManager = $this->modx->getCacheManager();
        $cache_path = $cacheManager->getCachePath();
        $cache_file = $cache_path . 'fbuch/storage/time.json';
        $cacheManager->writeFile($cache_file, json_encode($objectArray));


        return $this->success('', $objectArray);
    }

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')) {
            return false;
        }
        return true;
    }

}
