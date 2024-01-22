<?php

include 'BaseController.php';

class MyControllerMemberStates extends BaseController {
    public $classKey = 'mvMemberState';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePost() {
        throw new Exception('Unauthorized', 401);
    }

    public function verifyAuthentication() {
        //keine bestimmte Berechtigung benötigt
        return true;
    }
    
    
    protected function prepareListObject(xPDOObject $object) {
        
        $output = $object->toArray();
        $returntype = $this->getProperty('returntype');
        
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('state');
                $output['value'] = $object->get('state');
                break;
        }

        return $output;
    } 
    
    public function collection($list = array(),$total = false,$status = null) {
        $check_set_permission = $this->getProperty('check_set_permission');
        $newlist = $list;
        if (!empty($check_set_permission) && count($list)>0){
            $newlist = [];
            foreach ($list as $item){
               $permission = $this->modx->getOption('needed_set_permission',$item,'');
               if (!empty($permission)){
                  if ($this->modx->hasPermission($permission)){
                      $newlist[] = $item;    
                  }
               } else {
                  $newlist[] = $item; 
               }

            }
        }
        return parent::collection($newlist,$total,$status); 
    }    

}