<?php

include 'BaseController.php';

class MyControllerBootcomments extends BaseController {

    public $classKey = 'fbuchBootComment';
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
    
    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')){
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $hide_done = (int) $this->getProperty('hide_done');
        $boot_id = (int) $this->getProperty('boot_id');

        if ($hide_done == 1){
            $c->where(['done:!=' => 1]);
        }       

        if ($boot_id > 0){
            $c->where(['boot_id' => $boot_id]);
        }    

        $joins = '[{"alias":"Boot","selectfields":"name"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        return $c;

    }

    
}