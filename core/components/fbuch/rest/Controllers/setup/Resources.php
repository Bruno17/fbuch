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
            foreach ($resources as $r){
                if ($resource = $this->modx->getObject('modResource',['uri'=>$r['uri'],'context_key'=>$r['context_key']])){
                    $resource->set('deleted',1);
                    $resource->save();
                }
            }
        }
        if ($resources = $this->getResources('update_uris')){
            foreach ($resources as $r){
                if ($resource = $this->modx->getObject('modResource',['uri'=>$r['uri'],'context_key'=>$r['context_key']])){
                    if (isset($r['new_uri'])){
                        $resource->set('uri',$r['new_uri']);
                        $resource->set('uri_override',1);
                        $resource->save();                        
                    }
                }
            }
        }        
        if ($resources = $this->getResources('update_resources')){
            foreach ($resources as $r){
                if ($resource = $this->modx->getObject('modResource',['uri'=>$r['uri'],'context_key'=>$r['context_key']])){

                } else {
                    $resource = $this->modx->newObject('modResource');
                }
                $resource->fromArray($r);
                if (!empty($r['template_name']) && $template=$this->modx->getObject('modTemplate',['templatename'=>$r['template_name']])){
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
                if (isset($r['tvs']) && is_array($r['tvs'])){
                    foreach ($r['tvs'] as $tv){
                        if (isset($tv['name']) && isset($tv['value']))
                        $resource->setTVValue($tv['name'],$tv['value']);
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