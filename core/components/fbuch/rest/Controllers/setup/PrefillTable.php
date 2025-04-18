<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupPrefillTable extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
        $classname = $this->getProperty('classname');

        if ($this->modx->getCollection($classname)){
            return $this->failure('table has allready items');    
        }
        
        $result = $this->prefillTable($classname);

        return $this->success('',$objectArray);
    }

    public function prefillTable($classname){
        $contents = $this->getPrefills($classname);
        $items = json_decode($contents,true);
        $keyfield = 'id';
        $jsonfields = false;
        if (!is_array($items)){
            return false;
        }
        foreach($items as $item){
            if (isset($item['_key'])){
                $keyfield = $item['_key'];    
            }
            if (isset($item['_jsonfields'])){
                $jsonfields = $item['_jsonfields'];    
            }            
            
            if ($object = $this->modx->getObject($classname,[$keyfield => $item[$keyfield]])){

            } else {
                $object = $this->modx->newObject($classname);
            }
            $object->fromArray($item);
            if ($jsonfields){
                foreach ($jsonfields as $field){
                    $object->set($field,json_encode($object->get($field)));
                }
            }
            $object->save();            
        }
    }

    public function getPrefills($classname){
        return file_get_contents(dirname(__FILE__).'/prefills/'.$classname.'.json');        
    }

    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}