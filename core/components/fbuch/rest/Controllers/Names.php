<?php

include 'BaseController.php';

class MyControllerNames extends BaseController {
    public $classKey = 'mvMember';
    public $defaultSortField = 'mvMember.name';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function get() {

        $id = $this->getProperty($this->primaryKeyField);
        if ($id == 'me') {

            if ($fbuchUser = $this->getCurrentFbuchMember()) {
                $id = $fbuchUser->get('id');
            }

            if (!empty($id)) {
                $this->setProperty('id', $id);
            } else {
                throw new Exception('Not logged in', 405);
            }

        }
        return parent::get();
    }

    public function afterRead(array &$objectArray) {
        $allowed_fields = [
        'id',
        'firstname',
        'name',
        'member_status'];
        $objectArray = [];
        foreach ($allowed_fields as $field){
            $objectArray[$field] = $this->object->get($field);
        }
        if ($state = $this->object->getOne('State')){
            $stateArray = $state->toArray();
            foreach ($stateArray as $key => $value){
                $objectArray['State_' . $key] = $value;
            } 
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_names')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_create_names')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function verifyAuthentication() {

        $id = $this->getProperty($this->primaryKeyField);
        if ($id == 'me' && $fbuchUser = $this->getCurrentFbuchMember()) {
            return true;
        }
        
        if (!$this->modx->hasPermission('fbuch_view_names')) {
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $this->getDefaultCompetencyLevel();
        $statefilter = $this->getProperty('statefilter');
        $statefilter = empty($statefilter) ? 'can_be_invited' : $statefilter;

        $joins = '[{"alias":"State"},{"alias":"CompetencyLevel"}]';
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);  
        
        $where = ['deleted' => 0];
        if ($statefilter != 'none'){
            $where['State.'.$statefilter] = 1; 
        }
        $c->where($where);             

        return $c;
    }

    protected function prepareListQueryAfterCount(xPDOQuery $c) {

        if (isset($c->query['columns']) && is_array($c->query['columns'])){
            foreach ($c->query['columns'] as $key => $column){
                if (strstr($column,'mvMember')){
                    unset($c->query['columns'][$key]);
                }
            }
        }

        //$c->query['columns'] = array(); //reset default $c->select

        $c->select(array(
            'mvMember.id',
            'mvMember.firstname',
            'mvMember.name',
            'mvMember.member_status'));

        //$c->prepare();echo $c->toSql();
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('name') . ' ' . $object->get('firstname');
                if (!empty($output['CompetencyLevel_color'])){
                    $output['optcolor'] = $output['CompetencyLevel_color'];
                } elseif (!empty($this->CompetencyLevel_color)){
                    $output['optcolor'] = $this->CompetencyLevel_color;    
                }

                if ($object->get('member_status') != 'Mitglied') {
                    $output['label'] .= ' (' . $object->get('member_status') . ')';
                }
                $output['value'] = $object->get('id');
                break;
        }

        return $output;
    }

}
