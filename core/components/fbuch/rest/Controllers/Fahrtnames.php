<?php
include 'BaseController.php';

class MyControllerFahrtNames extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchFahrtNames';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }
        $this->setProperty('fahrt_id',$this->object->get('fahrt_id'));
        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
           
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterDelete(array &$objectArray) {
        $fahrt_id = $this->getProperty('fahrt_id');
        $this->modx->fbuch->forceObmann($fahrt_id);
    }

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');
        $names = $this->getProperty('names');
        $source = $this->getProperty('source');
        $target = $this->getProperty('target');
        $target_id = $this->getProperty('target_id');

        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }

        switch ($action) {
            case 'moveNames':
                if (is_array($names)){
                    foreach ($names as $name){
                        if ($source == 'dates' && isset($name['id'])){
                            $_REQUEST['dateNames_id'] = $name['id'];
                            $this->modx->fbuch->handleDragDrop();                            
                        }
                        if ($source == 'fahrten' && isset($name['id'])){
                            $_REQUEST['fahrtNames_id'] = $name['id'];
                            $this->modx->fbuch->handleDragDrop();                            
                        }                        
                    }
                    if ($target == 'fahrten'){
                        $this->modx->fbuch->forceObmann($target_id);    
                    }
                    if ($source == 'fahrten'){
                        if (is_array($names[0])){
                            $fahrt_id = $this->modx->getOption('fahrt_id',$names[0],0);
                            $this->modx->fbuch->forceObmann($fahrt_id);      
                        }
  
                    }                    
                }
                break;
                case 'setObmann':
                    $fields = [];
                    $fields['fahrt_id'] = $this->getProperty('fahrt_id');
                    $fields['fahrtnames_id'] = $this->getProperty('id');
                    $this->modx->fbuch->setObmann($fields);
                    break;
                case 'setCox':
                    $fields = [];
                    $fields['fahrt_id'] = $this->getProperty('fahrt_id');
                    $fields['fahrtnames_id'] = $this->getProperty('id');
                    $this->modx->fbuch->setCox($fields);
                    break;                                     
        }

        $objectArray = [];
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }    

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')){
            return false;
        }        
        return true;
    }
    
}
