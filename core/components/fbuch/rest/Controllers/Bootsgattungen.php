<?php

include 'BaseController.php';

class MyControllerBootsgattungen extends BaseController {
    public $classKey = 'fbuchBootsGattung';
    public $defaultSortField = 'shortname';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePost() {
        throw new Exception('Unauthorized', 401);
    }

    public function verifyAuthentication() {
        //keine bestimmte Berechtigung benötigt
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        return $c;
    }
    
    protected function prepareListObject(xPDOObject $object) {
        $type = $object->toArray();
        $type['label'] = $object->get('longname');
        $type['value'] = $object->get('id');
        return $type;
    }    

}