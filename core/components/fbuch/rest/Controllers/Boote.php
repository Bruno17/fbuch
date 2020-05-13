<?php

class MyControllerBoote extends modRestController {
    public $classKey = 'fbuchBoot';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
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

        if ($this->modx->hasPermission('fbuch_create_fahrten')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')) {
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        //$joins = '[{"alias":"Boot"}]';

        //$this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0, 'gattung' => 'fahrzeug'));

        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output = array();
                $output['label'] = $object->get('name');
                $output['value'] = $object->get('id');
                break;
        }

        return $output;
    }

}
