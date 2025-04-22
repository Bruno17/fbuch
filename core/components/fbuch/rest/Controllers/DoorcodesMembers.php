<?php

include 'BaseController.php';

class MyControllerDoorcodesMembers extends BaseController {
    public $classKey = 'mvDoorAccesscodeMember';
    public $defaultSortField = 'mvDoorAccesscodeMember.code';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function afterRead(array &$objectArray) {
        if ($code_o = $this->object->getOne('Code')){
            $code_array = $code_o->toArray();
            foreach ($code_array as $key => $val){
                $objectArray['Code_' . $key] = $val;
            }
        }

        $members = []; 
        $c = $this->modx->newQuery('mvDoorAccesscodeMember');
        $c->where(['code'=>$this->object->get('code')]);
        if ($collection = $this->modx->getCollection('mvDoorAccesscodeMember',$c)){
            foreach ($collection as $object){
                $name = '';
                $member_status = ''; 
                $member = $object->toArray();  
                $other_person = $object->get('other_person');             
                if ($member_o = $object->getOne('Member')){
                    $name = $member_o->get('firstname') . ' ' . $member_o->get('name');
                    $member_status = $member_o->get('member_status');
                } elseif (!empty($other_person)){
                    $name = $other_person;
                    $member_status = 'Vereinsfremd';
                }
                if (!empty($name)){
                    $member['name'] = $name;
                    $member['member_status'] = $member_status;
                    $members[] = $member;                    
                }

            }
        }
        $objectArray['members'] = $members;


        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_manage_doorcodes')) {
            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_manage_doorcodes')) {
            $this->object->set('assignedby', $this->modx->user->get('id'));
            $this->object->set('assignedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPost(array &$objectArray) {
        $properties = $this->getProperties();

        if ($code_o = $this->object->getOne('Code')){
            if (isset($properties['Code_blocked'])){
                $code_o->set('blocked',$properties['Code_blocked']);   
                $code_o->save(); 
            }
            if (isset($properties['Code_time_setting'])){
                $code_o->set('time_setting',$properties['Code_time_setting']);   
                $code_o->save(); 
            }            
            
        }
    }

    public function afterPut(array &$objectArray) {
        $properties = $this->getProperties();

        if ($code_o = $this->object->getOne('Code')){
            if (isset($properties['Code_blocked'])){
                $code_o->set('blocked',$properties['Code_blocked']);   
                $code_o->save(); 
            }
            if (isset($properties['Code_time_setting'])){
                $code_o->set('time_setting',$properties['Code_time_setting']);   
                $code_o->save(); 
            }            
            
        }


    }    

    public function verifyAuthentication() {

        if (!$this->modx->hasPermission('fbuch_view_doorcodes')) {
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $member_id = $this->getProperty('member_id');
        $sortby = $this->getProperty('sortby');

        if (!empty($member_id)){
            $c->where(['member_id'=>$member_id]);
        }

        if ($sortby=='Member_name'){
            $c->sortby('other_person','ASC');  
            $c->sortby('Member.name','ASC');
            $c->sortby('Member.firstname','ASC');
        }
  
        $joins = '[{"alias":"Member","selectfields":"name,firstname,member_status"},{"alias":"Code"}]';
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);  
        
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);
        $output['Member_name'] = $output['Member_name'] . ' ' . $output['Member_firstname'];
        if (!empty($output['other_person'])){
            $output['Member_name'] = $output['other_person'];
            $output['Member_member_status'] = 'Vereinsfremd';
        }

        return $output;
    }

}
