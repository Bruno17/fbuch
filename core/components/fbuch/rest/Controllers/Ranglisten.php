<?php

include 'traits/ranglisten.trait.php'; 

class MyControllerRanglisten extends modRestController {

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

    public function getOptionByProperty($name,$options,$default){
        $property = $this->getProperty($name,$default);
        $options = json_decode($options,true);
        if (is_array($options) && isset($options[$property])){
            $option = $options[$property];
            return $option;
        }
        return '';
    }

    public function getTimeRangeOptions(){
        $current_year = date('Y');
        $year = $this->getProperty('year',$current_year);        
        $options = '
            {
                "ganz":{"label":"Ganzes Jahr","query":" AND year(f4.date)=[[+year]] "},
                "halb_1":{"label":"Winterhalbjahr (Okt - März [[+year]])","query":" AND ((year(f4.date)=[[+year]]-1 AND month(f4.date)>=10) OR (year(f4.date)=[[+year]] AND month(f4.date)<=3)) "},
                "halb_2":{"label":"Sommerhalbjahr (Apr - Sept [[+year]])","query":" AND year(f4.date)=[[+year]] AND month(f4.date)>=4 AND month(f4.date)<=9 "}
            }
        ';
        $options = str_replace('[[+year]]',$year,$options);
        return $options;        
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
            case 'timerange_options':
                $options_json = $this->getTimeRangeOptions();
                $options = json_decode($options_json,1);
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


