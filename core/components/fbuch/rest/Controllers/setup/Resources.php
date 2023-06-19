<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupResources extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
        $this->updateResources();
        return $this->success('',$objectArray);
    }

    public function getResources($type){
        include 'resources.inc.php';
        if (isset($$type) && is_array($$type)){
            return $$type;
        }
        return false;                   
    }

    public function updateResources(){
        if ($resources = $this->getResources('delete_resources')){
            foreach ($resources as $uri){
                if ($resource = $this->modx->getObject('modResource',['uri'=>$uri])){
                    $resource->set('deleted',1);
                    $resource->save();
                }
            }
        }
        if ($resources = $this->getResources('update_resources')){
            foreach ($resources as $r){
                if ($resource = $this->modx->getObject('modResource',['uri'=>$r['uri']])){

                } else {
                    $resource = $this->modx->newObject('modResource');
                }
                $resource->fromArray($r);
                if (isset($r['template_name']) && $template=$this->modx->getObject('modTemplate',['templatename'=>$r['template_name']])){
                    $resource->set('template',$template->get('id'));    
                } 
                if (isset($r['parent_uri']) && $parent = $this->modx->getObject('modResource',['uri'=>$r['parent_uri']])){
                    $resource->set('parent',$parent->get('id'));
                }                               
                $resource->save();
                if (isset($r['resource_groups']) && is_array($r['resource_groups'])){
                    foreach ($r['resource_groups'] as $group){
                        $resource->joinGroup($group);
                    }
                }                
            }
        }              
    }

  
    
    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}