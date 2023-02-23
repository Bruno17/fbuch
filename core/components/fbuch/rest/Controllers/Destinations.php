<?php

include 'BaseController.php';

class MyControllerDestinations extends BaseController {
    public $classKey = 'fbuchDestination';
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
        
        $output = $object->toArray();
        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('destination'). ' (' . $object->get('km') . 'km)';
                $output['value'] = $object->get('destination');
                break;
        }

        return $output;
    }    

}