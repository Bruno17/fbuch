<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupPrefillFbuchCompetencyLevelSkills extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
        $classname = 'fbuchCompetencyLevelSkill';
        $category_classname = 'fbuchCompetencyLevelSkillsCategory';

        if ($this->modx->getCollection($classname)){
            return $this->failure('table '.$classname.' has allready items');    
        }
        if ($this->modx->getCollection($category_classname)){
            return $this->failure('table '.$category_classname.' has allready items');     
        }        
        
        $result = $this->prefillCategories($classname,$category_classname);

        return $this->success('',$objectArray);
    }

    public function prefillCategories($classname,$category_classname){
        $contents = $this->getPrefills($classname);
        $items = json_decode($contents,true);
        $keyfield = 'id';
        $jsonfields = false;
        if (!is_array($items)){
            return false;
        }
        foreach($items as $item){
            $object = $this->modx->newObject($category_classname);
            $object->set('name',$item['name']);
            $object->set('pos',$item['pos']);
            $object->save();
            $item['parent'] = $object->get('id');
            $this->addSubcategories($classname,$category_classname,$item);
        }
    }
    
    public function addSubcategories($classname,$category_classname,$item){
        if (is_array($item['children'])){
            foreach($item['children'] as $subcategory){
                $object = $this->modx->newObject($category_classname);
                $object->set('name',$subcategory['name']);
                $object->set('pos',$subcategory['pos']);
                $object->set('parent',$item['parent']);
                $object->save();
                $subcategory['category_id'] = $object->get('id');
                $this->addSkills($classname,$subcategory);
                            
            }                   
        }
    }

    public function addSkills($classname,$item){
        if (is_array($item['skills'])){
            foreach($item['skills'] as $skill){
                $object = $this->modx->newObject($classname);
                $object->set('name',$skill['name']);
                $object->set('pos',$skill['pos']);
                $object->set('levels',$skill['levels']);
                $object->set('category_id',$item['category_id']);
                $object->save();
                            
            }                   
        }
    }    

    public function prefillTable($classname,$category_classname){
        $contents = $this->getPrefills($classname);
        $items = json_decode($contents,true);
        $keyfield = 'id';
        $jsonfields = false;
        if (!is_array($items)){
            return false;
        }
        foreach($items as $item){
            print_r($item);
            continue;

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