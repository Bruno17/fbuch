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
        //check_set_permission
        //option_for_web_memberform
        $option_for_web_memberform = $this->getProperty('option_for_web_memberform');
        $newlist = $list;
        if (!empty($option_for_web_memberform) && count($list)>0){
            $newlist = [];
            foreach ($list as $item){
               $show_option = $this->modx->getOption('option_for_web_memberform',$item,'');
               if (!empty($show_option)){
                   $newlist[] = $item;    
               }
            }
        }
        return parent::collection($newlist,$total,$status); 
    }    

}