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
        'member_status',
        'competency_level',
        'safety_instructions_date',
        'riot_user_id',
        'gender' 
        ];
        $objectArray = [];
        foreach ($allowed_fields as $field){
            $objectArray[$field] = $this->object->get($field);
        }
        $email = $this->object->get('email');
        if (empty($email) || $this->modx->hasPermission('mv_administrate_members')){
            $objectArray['email'] = $email;
        }
        $phone = $this->object->get('phone');
        if (empty($phone) || $this->modx->hasPermission('mv_administrate_members')){
            $objectArray['phone'] = $phone;
        } 
        $birthdate = $this->object->get('birthdate');
        if ($this->modx->hasPermission('mv_administrate_members')){
            $objectArray['birthdate'] = $birthdate;
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
            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
            $this->checkCompetencyLevel();
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_create_names')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
            $name = $this->object->get('name');
            $firstname = $this->object->get('firstname');
            if ($existing = $this->modx->getObject('mvMember',['name'=>$name,'firstname'=>$firstname])){
                return 'name_exists';    
            }
            $this->checkCompetencyLevel();

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function checkCompetencyLevel(){
        $id = $this->object->get('id');
        $old_level == '';
        $new_level = $this->object->get('competency_level');
        if ($id > 0 && $object = $this->modx->getObject('mvMember',$id)){
            $old_level = $object->get('competency_level');
        }
        if ($new_level != $old_level){
            $this->object->set('competency_level_editedby', $this->modx->user->get('id'));
            $this->object->set('competency_level_editedon', strftime('%Y-%m-%d %H:%M:%S'));             
        }
    }

    public function afterPost(array &$objectArray) {
        $this->object->afterSave($this->modx->fbuch);
    }

    public function afterPut(array &$objectArray) {
        $this->object->afterSave($this->modx->fbuch);
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

        $columns = [
            'mvMember.riot_user_id',
            'mvMember.id',
            'mvMember.firstname',
            'mvMember.name',
            'mvMember.member_status'            
        ];

        if ($this->modx->hasPermission('fbuch_view_birthdate')){
            $columns[] = 'birthdate';
        }

        $c->select($columns);

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
            default:
            if (!empty($output['CompetencyLevel_color'])){

            } elseif (!empty($this->CompetencyLevel_color)){
                $output['CompetencyLevel_color'] = $this->CompetencyLevel_color;    
            }            
            break;
        }

        return $output;
    }

}
