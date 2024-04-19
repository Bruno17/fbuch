<?php

include 'traits/ranglisten.trait.php'; 

class MyControllerRanglisten extends fbuchRestController {

    use RanglistenTrait;
    
    public $classKey = 'fbuchFahrt';
    public $km_sum = 0;
 
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
        return true;
    }

    public function getList() {
        $modx = & $this->modx;
        $returntype = $this->getProperty('returntype'); 

        $total = false;
        $list = [];

        switch ($returntype){
            case 'group_options':
                $options = $this->getGroupOptions();
                foreach ($options as $key => $value){
                    $list[] = ['value'=>$key,'label'=>$value['label']];
                }
                break;            
            default:
                $list = $this->getRangliste();
            break;
        }
 
        return $this->collection($list,$total);
    }  
    
    public function collection($list = array(),$total = false,$status = null) {
        parent::collection($list,$total,$status);
        $this->response['km_sum'] = (string) $this->km_sum;
    }    

}


