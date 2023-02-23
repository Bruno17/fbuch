<?php

include 'BaseController.php';

class MyControllerBoote extends BaseController {
    public $classKey = 'fbuchBoot';
    public $defaultSortField = 'id';
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

        $gattung_name = $this->getProperty('gattung_name',false);
        $gattung_id = $this->getProperty('gattung_id',false);
        $returntype = $this->getProperty('returntype');

        $joins = '[{"alias":"Bootsgattung"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0));
        if ($gattung_name){
            $c->where(['Bootsgattung.name' => $gattung_name]);
        }
        if ($gattung_id){
            $c->where(['gattung_id' => $gattung_id]);
        } 
        switch ($returntype) {
            case 'gattungnames':
                $c->groupby('Bootsgattung.name');
                $c->sortby('Bootsgattung.name');
                break;            
            case 'bootsgattungen':
                $c->groupby('gattung_id');
                $c->sortby('Bootsgattung.shortname');
                break;
            default:
                $c->sortby('Bootsgattung.shortname');
                $c->sortby('name');
                break;
        }       
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('name') . ' (' . $object->get('Bootsgattung_shortname') . ')';
                $output['value'] = $object->get('id');
                break;
            case 'bootsgattungen':
                $output['label'] = $object->get('Bootsgattung_longname');
                $output['value'] = $object->get('Bootsgattung_id');
                break;
            case 'gattungnames':
                $output['label'] = $object->get('Bootsgattung_name');
                $output['value'] = $object->get('Bootsgattung_name');
                break;                                   
        }

        return $output;
    }

}
