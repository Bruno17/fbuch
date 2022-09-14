<?php

include 'BaseController.php';

class MyControllerDatetypes extends BaseController {
    public $classKey = 'fbuchDateType';
    public $defaultSortField = 'id';
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
    
    
    protected function prepareListObject(xPDOObject $object) {
        $type = $object->toArray();
        $type['label'] = $object->get('name');
        $type['value'] = $object->get('name');
        $type['colorstyle'] = $type['colorstyle'] == '' ? 'grey' : $type['colorstyle'];
        return $type;
    }    

}