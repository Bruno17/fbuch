<?php

include 'BaseController.php';

class MyControllerBootsettings extends BaseController {

    public $classKey = 'fbuchBootSetting';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $defaultLimit = 1000;
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    } 

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
            $this->validateProperties();
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
            $this->validateProperties();
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function validateProperties(){
        $settings = $this->getProperty('settings');
        if (is_array($settings)){
            $this->object->set('settings',json_encode($settings));
        }
    }

    public function afterRead(array &$objectArray) {
       $settings = json_decode($this->object->get('settings'),1);
       if (is_array($settings)){
           $objectArray['settings'] = $settings;  
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

        $joins = '[{"alias":"Boot","selectfields":"name,seats"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        return $c;

    }

    protected function prepareListObject(xPDOObject $object) {
        $objectArray = $object->toArray();
        $settings = json_decode($object->get('settings'),1);
        if (is_array($settings)){
            $objectArray['settings'] = $settings;  
        }        
        return $objectArray; 
    }     

    
}