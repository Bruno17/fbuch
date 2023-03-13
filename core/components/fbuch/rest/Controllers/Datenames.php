<?php
include 'BaseController.php';

class MyControllerDateNames extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDateNames';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_edit_termin')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }
        $this->setProperty('fahrt_id',$this->object->get('fahrt_id'));
        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_termin')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        /*
        if ($this->modx->hasPermission('fbuch_edit_termin')) {
           
        } else {
            throw new Exception('Unauthorized', 401);
        }
        */

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
                if ($this->modx->hasPermission('fbuch_create_fahrten')){
                    $properties['member_id'] = $member_id;
                    $this->modx->fbuch->addPersonsToDate($properties); 
                } else {
                    throw new Exception('Unauthorized', 401);
                }     
                    
                break;
            case 'remove': 
                $member_id = 0;
                if ($this->modx->hasPermission('fbuch_create_fahrten')){
                    $date_id = $this->getProperty('date_id');
                    $persons = $this->getProperty('person');
                    $datename_id = $this->getProperty('datename_id',0);
                    if (!empty($datename_id)){
                        $this->modx->fbuch->removePersonFromDate($date_id,$datename_id);
                    } elseif (is_array($persons)){
                        foreach ($persons as $person){
                            if ($object = $this->modx->getObject($this->classKey,['member_id'=>$person,'date_id'=>$date_id])){
                                $datename_id = $object->get('id');
                                $this->modx->fbuch->removePersonFromDate($date_id,$datename_id);
                            }
                        }
                    } else {
                        $person = $persons;
                        if ($object = $this->modx->getObject($this->classKey,['member_id'=>$person,'date_id'=>$date_id])){
                            $datename_id = $object->get('id');
                            $this->modx->fbuch->removePersonFromDate($date_id,$datename_id);
                        }
                    }     
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
        if (!$this->modx->hasPermission('fbuch_create_fahrten')){
            return false;
        }        
        return true;
    }
    
}
