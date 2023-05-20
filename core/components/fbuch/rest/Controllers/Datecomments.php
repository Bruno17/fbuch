<?php
include 'BaseController.php';

class MyControllerDateComments extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDateComment';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $currentFbuchUser = false;

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_delete_datecomment')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }
        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_datecomment')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_accept_invite')) {
            if ($member = $this->getCurrentFbuchUser()){
                $this->object->set('member_id',$member->get('id'));
            }
               $date = new DateTime;
               $this->object->set('createdon',date_format($date,'Y-m-d H:i:s'));   
               $this->object->set('createdby',$this->modx->user->get('id'));          
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        $date_id = $this->getProperty('date_id',false);
        $this->currentFbuchUser = $this->getCurrentFbuchUser();        

        $joins = '[{"alias":"Member","selectfields":"name,firstname"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        if ($date_id){
            $c->where(['date_id' => $date_id]);
        } 

        $c->sortby('fbuchDateComment.createdon','DESC');
        return $c;
    } 
    
    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        $name = $object->get('Member_firstname') . ' ' . $object->get('Member_name');
        $member_id = $this->currentFbuchUser ? $this->currentFbuchUser->get('id') : false;
 
        switch ($returntype) {
            default:
            $output['from'] = $name;
            $date = date_create($object->get('createdon'));
            $output['createdon_formatted'] = date_format($date,'d.m.Y H:i'); 
            $output['text'] = [str_replace("\n",'<br>',$object->get('comment'))];
            $output['sent'] = $member_id == $object->get('member_id');
            $output['initials'] = substr($object->get('Member_firstname'),0,1) . substr($object->get('Member_name'),0,1);                
            break;                                 
        }

        return $output;
    }    

    public function verifyAuthentication() {
        return true;
    }
    
}
