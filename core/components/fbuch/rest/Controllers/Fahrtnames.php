<?php
include 'BaseController.php';

class MyControllerFahrtNames extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchFahrtNames';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_edit_fahrt')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrt')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_edit_fahrt')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');
        $names = $this->getProperty('names');
        $source = $this->getProperty('source');

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
                }
                break;
                case 'setObmann':
                    $fields = [];
                    $fields['member_id'] = $this->getProperty('member_id');
                    $fields['fahrt_id'] = $this->getProperty('fahrt_id');
                    $this->modx->fbuch->setObmann($fields);
                    break;
                case 'setCox':
                    $fields = [];
                    $fields['member_id'] = $this->getProperty('member_id');
                    $fields['fahrt_id'] = $this->getProperty('fahrt_id');
                    $this->modx->fbuch->setCox($fields);
                    break;                                     
        }

        $objectArray = [];
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }    

    public function verifyAuthentication() {
        return true;
    }
    
}
