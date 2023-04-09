<?php

include 'BaseController.php';

class MyControllerModUser extends BaseController {
    public $classKey = 'modUser';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function get() {

        $id = $this->getProperty($this->primaryKeyField);
        if ($id == 'me' && $fbuchUser = $this->getCurrentFbuchUser()) {
    
        } else {
            throw new Exception('Unauthorized', 401);    
        }

        if ($id == 'me') {

            $id = $this->modx->user->get('id');

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
        'username',
        'Member_id',
        'Member_name',
        'Member_firstname',
        'Member_member_status'];
        $objectArray = [];
        if ($profile = $this->object->getOne('Profile')){
            $fields = $profile->toArray();
            foreach ($fields as $key => $value){
                $this->object->set('Profile_' . $key,$value);
            }
        }
        if ($member = $this->modx->getObject('mvMember',['modx_user_id'=>$this->object->get('id')])){
            $fields = $member->toArray();
            foreach ($fields as $key => $value){
                $this->object->set('Member_' . $key,$value);
            }
        }        

        foreach ($allowed_fields as $field){
            $objectArray[$field] = $this->object->get($field);
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

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        return $output;
    }

}