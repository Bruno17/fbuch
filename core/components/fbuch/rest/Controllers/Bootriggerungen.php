<?php

include 'BaseController.php';

class MyControllerBootriggerungen extends BaseController {

    public $classKey = 'fbuchBootRiggerung';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $defaultLimit = 1000;
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    } 

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
            //$this->validateProperties();
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {
        //$this->modx->log(modX::LOG_LEVEL_DEBUG, 'beforePut');
        if ($this->modx->hasPermission('fbuch_create_fahrten')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            //$this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
            //$this->validateProperties();
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPut(array &$objectArray) {
        $this->afterSave();
    }
    
    public function afterPost(array &$objectArray) {
        $this->afterSave();
    } 
    
    public function afterSave(){
        $boot_id = $this->object->get('boot_id');
        $gattung_id = $this->object->get('gattung_id');
        if ($boot = $this->modx->getObject('fbuchBoot',$boot_id)) {
            $boot->set('gattung_id',$gattung_id);
            $boot->save();
        }

        return;
    }        
    
    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')){
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $boot_id = (int) $this->getProperty('boot_id');

        if ($boot_id > 0){
            $c->where(['boot_id' => $boot_id]);
        }    

        $joins = '[{"alias":"Boot","selectfields":"name"},{"alias":"Bootsgattung"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        return $c;

    }

    
}