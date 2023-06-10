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

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');
        $persons = $this->getProperty('person');
        
        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }
        $current_member_id = 0;
        if ($member = $this->getCurrentFbuchMember()){
            $current_member_id = $member->get('id');    
        }
        $properties['current_member_id'] = $current_member_id;
        switch ($action) {
            case 'add':
                $person_id = 0;
                if (is_array($persons) && isset($persons[0])){
                    $person_id = $persons[0]; 
                }
                if (!empty($person_id) && $person_id == $current_member_id && $this->modx->hasPermission('fbuch_accept_invite')){
                    $this->modx->fbuch->addPersonsToDate($properties);
                } elseif ($this->modx->hasPermission('fbuch_add_persons_to_dates')){
                    
                    $this->modx->fbuch->addPersonsToDate($properties); 
                } else {
                    throw new Exception('Unauthorized', 401);
                }     
                    
                break;
            case 'remove':
                $date_id = $this->getProperty('date_id');
                $datename_id = $this->getProperty('datename_id',0);
                if (!empty($datename_id)){
                    if ($this->modx->hasPermission('fbuch_add_persons_to_dates')){
                        $this->modx->fbuch->removePersonFromDate($datename_id);   
                    } else {
                        throw new Exception('Unauthorized', 401);
                    }                     
                    
                } elseif (is_array($persons)){
                    foreach ($persons as $person_id){
                        if ($object = $this->modx->getObject($this->classKey,['member_id'=>$person_id,'date_id'=>$date_id])){
                            $datename_id = $object->get('id');
                            if ($this->modx->hasPermission('fbuch_add_persons_to_dates')){
                                $this->modx->fbuch->removePersonFromDate($datename_id);   
                            } else {
                                throw new Exception('Unauthorized', 401);
                            } 
                        }
                    }
                } else {
                    $person_id = $persons;
                    if ($object = $this->modx->getObject($this->classKey,['member_id'=>$person_id,'date_id'=>$date_id])){
                        $datename_id = $object->get('id');
                    }
                    if (!empty($person_id) && $person_id == $current_member_id && $this->modx->hasPermission('fbuch_accept_invite')){
                        $this->modx->fbuch->cancelInvite($date_id,$person_id);
                        $this->modx->fbuch->removePersonFromDate($datename_id);
                    } elseif ($this->modx->hasPermission('fbuch_add_persons_to_dates')){
                        $this->modx->fbuch->removePersonFromDate($datename_id);   
                    } else {
                        throw new Exception('Unauthorized', 401);
                    } 
                }



                                  

                break;                
                                
        }

        $objectArray = [];
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    } 
    
    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $date_id = $this->getProperty('date_id',false);
        $joins = '[{"alias":"Member","selectfields":"id,name,firstname,member_status"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);         

        if ($date_id){
            $c->where(['date_id' => $date_id]);
        }

        $c->where(['Member.id:!=' => 'null']);

        $c->sortby('Member.name','ASC');
        $c->sortby('Member.firstname','ASC');
        
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('Member_name') . ' ' . $object->get('Member_firstname');
                if ($object->get('guestname') != '') {
                    $output['label'] = $object->get('guestname') . ' (Gasteintrag)' ;
                } elseif ($object->get('Member_member_status') != 'Mitglied') {
                    $output['label'] .= ' (' . $object->get('Member_member_status') . ')';
                }
                $output['value'] = $object->get('Member_id');
                break;
        }

        return $output;
    }    

    public function verifyAuthentication() {
        return true;
    }
    
}
