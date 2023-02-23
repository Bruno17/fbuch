<?php

include 'BaseController.php';

class MyControllerNames extends BaseController {
    public $classKey = 'mvMember';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function get() {

        $id = $this->getProperty($this->primaryKeyField);
        if ($id == 'me') {

            if ($fbuchUser = $this->getCurrentFbuchUser()) {
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

    public function beforePut() {

        $userid = $this->modx->user->id;
        if ($playlist = $this->object->getOne('Playlist')) {
            if ($playlist->createdby == $userid) {
                //print_r($playlist->toArray());
            } else {
                throw new Exception('Unauthorized', 401);
            }
        } else {
            throw new Exception($this->modx->lexicon('rest.err_obj_nf', array('class_key' => 'trPlaylist', )), 200);
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
        if ($id == 'me' && $fbuchUser = $this->getCurrentFbuchUser()) {
            return true;
        }
        
        if (!$this->modx->hasPermission('fbuch_view_names')) {
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        //$joins = '[{"alias":"Boot"}]';

        //$this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0, 'member_status:IN' => array(
                'Mitglied',
                'VHS',
                'Gast')));

        return $c;
    }

    protected function prepareListQueryAfterCount(xPDOQuery $c) {

        $c->query['columns'] = array(); //reset default $c->select
        $c->select(array(
            'id',
            'firstname',
            'name',
            'member_status'));

        //$c->prepare();echo $c->toSql();
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('name') . ' ' . $object->get('firstname');
                if ($object->get('member_status') != 'Mitglied') {
                    $output['label'] .= ' (' . $object->get('member_status') . ')';
                }
                $output['value'] = $object->get('id');
                break;
        }

        return $output;
    }

}
