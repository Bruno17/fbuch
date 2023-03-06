<?php
include 'BaseController.php';

class MyControllerDateNames extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDateNames';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }
        $this->setProperty('fahrt_id',$this->object->get('fahrt_id'));
        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
           
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterDelete(array &$objectArray) {
        $fahrt_id = $this->getProperty('fahrt_id');
        $this->modx->fbuch->forceObmann($fahrt_id);
    }

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');

        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }

        switch ($action) {
            case 'add': 
                $member_id = 0;
                if ($member = $this->getCurrentFbuchUser()){
                    $member_id = $member->get('id');    
                }
                if ($this->modx->hasPermission('fbuch_edit_fahrten')){
                    $properties['member_id'] = $member_id;
                    $this->modx->fbuch->addPersonsToDate($properties); 
                } else {
                    throw new Exception('Unauthorized', 401);
                }     
                    
                break;
                                
        }

        $objectArray = [];
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }    

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')){
            return false;
        }        
        return true;
    }
    
}
