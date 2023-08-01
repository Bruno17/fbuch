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

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $date_id = (int) $this->getProperty('date_id',false);
        $canceled = (int) $this->getProperty('canceled',false);
        $joins = '[{"alias":"Member","selectfields":"id,name,firstname,member_status"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);         

        if ($date_id){
            $c->where(['date_id' => $date_id]);
        }
        if ($canceled){
            $c->where(['canceled' => $canceled]);
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