<?php
include 'BaseController.php';

class MyControllerDateInvited extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDateInvited';
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

        if ($this->modx->hasPermission('fbuch_edit_termin')) {
           
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');
        $persons = $this->getProperty('persons');
        $date_id = $this->getProperty('date_id');
        $to = $this->getProperty('to');

        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }
        /*
        $current_member_id = 0;
        if ($member = $this->getCurrentFbuchMember()){
            $current_member_id = $member->get('id');    
        }
        $properties['current_member_id'] = $current_member_id;
        */
        switch ($action) {
            case 'add':
                if (!empty($date_id) && is_array($persons)){
                    foreach ($persons as $person){
                        $member_id = $this->modx->getOption('id',$person,0);
                        if (!empty($member_id)){
                            if ($object = $this->modx->getObject($this->classKey,['date_id'=>$date_id,'member_id'=>$member_id])){

                            } else {
                                $object = $this->modx->newObject($this->classKey);
                                $object->fromArray(['date_id'=>$date_id,'member_id'=>$member_id]);                
                            }
                            $object->set('addedby',$this->modx->user->get('id'));
                            $object->set('removedby',0);
                            $object->save();                             
                        }
                    }
                }
                    
                break;
            case 'remove':
                if (!empty($date_id) && is_array($persons)){
                    foreach ($persons as $person){
                        $member_id = $this->modx->getOption('member_id',$person,0);
                        if (!empty($member_id)){
                            if ($object = $this->modx->getObject($this->classKey,['date_id'=>$date_id,'member_id'=>$member_id])){

                            } else {
                                $object = $this->modx->newObject($this->classKey);
                                $object->fromArray(['date_id'=>$date_id,'member_id'=>$member_id]);                
                            }
                            $object->set('removedby',$this->modx->user->get('id'));
                            $object->set('addedby',0);
                            $object->save();                             
                        }
                    }
                }
                break;
                case 'invite':
                    if (!empty($date_id) && !empty($to)){
                        $comment = $this->getProperty('comment');
                        $skip_accepted = $this->getProperty('skip_accepted');
                        $skip_canceled = $this->getProperty('skip_canceled');
                        if ($to == 'all'){
                            $action = 'mail_invites';
                            $properties = [
                                'date_id' => $date_id,
                                'comment' => $comment,
                                'skip_accepted' => $skip_accepted,
                                'skip_canceled' => $skip_canceled
                            ];
        
                            if ($date_o = $this->modx->getObject('fbuchDate', $properties['date_id'])) {
                                $this->modx->fbuch->scheduleInvites($action, $properties);
                            }
                        } else {
                            $action = 'mail_invite';
                            $properties = [
                                'date_id' => $date_id,
                                'comment' => $comment,
                                'member_id' => $to
                            ];
        
                            if ($date_o = $this->modx->getObject('fbuchDate', $properties['date_id'])) {
                                $this->modx->fbuch->scheduleInvites($action, $properties);
                            }
                        }
                    }
                        
                    break;                         
                                
        }

        $objectArray = [];
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }     

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $date_id = (int) $this->getProperty('date_id',false);
        $canceled = (int) $this->getProperty('canceled',false);
        $added = $this->getProperty('added',false);
        $added = is_bool($added) ? $added : (int) $added;
        $removed = $this->getProperty('removed',false);
        $removed = is_bool($removed) ? $removed : (int) $removed;
        $joins = '[{"alias":"Member","selectfields":"id,name,firstname,member_status"},
        {"alias":"Datename","classname":"fbuchDateNames","on":"Datename.member_id=fbuchDateInvited.member_id and Datename.date_id=fbuchDateInvited.date_id"}
        ]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);  
        //wenn keine Berechtigung, die Namen aufzulisten, hole zumindest den Datensatz vom eigeloggten Mitglied. 
        if ($member = $this->getCurrentFbuchMember()){
            $member_id = $member->get('id');
        }    
        $permission = '';  
        if ($date_o = $this->modx->getObject('fbuchDate',['id'=>$date_id])) {
            if ($type_o = $date_o->getOne('Type')) {
                $permission = $type_o->get('nameslist_permission'); 
            } 
        }  
        if (!empty($permission)){
            if ($this->modx->hasPermission($permission)){
    
            } else {
                $c->where(['member_id' => $member_id]);
            }               
        } 

        if ($date_id){
            $c->where(['fbuchDateInvited.date_id' => $date_id]);
        }
        if ($canceled){
            $c->where(['canceled' => $canceled]);
        }
        if ($added){
            $c->where(['addedby:>' => 0]);
        } elseif ($added === 0) {
            $c->where(['addedby' => 0]);
        }
        if ($removed){
            $c->where(['removedby:>' => 0]);
        } elseif ($removed === 0) {
            $c->where(['removedby' => 0]);
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
            default:
            if ($object->get('Datename_id')){
                $output['bgcolor'] = 'bg-light-green-2';
            } 
            if ($object->get('canceled')){
                $output['bgcolor'] = 'bg-red-2';
            }               
        }

        return $output;
    }    

    public function verifyAuthentication() {
        return true;
    }
    
}