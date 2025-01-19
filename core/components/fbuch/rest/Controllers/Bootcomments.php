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

    public function afterPost(array &$objectArray) {
        $this->object->set('is_new',1);
        $this->afterSave();
    }
    
    public function afterPut(array &$objectArray) {
        $this->object->set('is_new',0);
        $this->afterSave();
    }        

    public function afterSave(){
        $this->notify();
    }

    public function notify(){
        $notify = $this->getProperty('notify');        
        $email = $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_email_bootsschaden'));
        $tpl = $this->modx->fbuch->getChunkName('fbuch_bootsschaden_mail_tpl');        
        if (!empty($notify) && !empty($email)){
            $item = $this->object->toArray();
            if ($object = $this->modx->getObject('fbuchBoot',$item['boot_id'])){
                $item['Boot_name'] = $object->get('name');
            }

            $properties = [
                'email' => $email,
                'tpl' => $tpl,
                'subject' => '[Fahrtenbuch] Bootsschaden erstellt oder bearbeitet',
                'schaden' => json_encode($this->object->toArray())
            ];
            $this->modx->fbuch->sendMail(array_merge($item,$properties));
        }
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