<?php

class MyControllerPermissions extends modRestController {
    
    public $classKey = 'modResource';
 
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

    public function getAllPermissions(){
        $permissions = [];
        if ($collection = $this->modx->getCollection('modAccessPermission')){
            foreach ($collection as $object){
                $permissions[$object->get('name')] = $object->get('name');
            }
        }
        return $permissions;
    }

    public function getList() {
        $total = false;
        if ($this->modx->user->get('sudo')) return $this->collection(['is_sudo'],$total);
        $permissions = $this->getAllPermissions();
        //$permissions = explode(',',$this->getProperty('permissions'));
        $list = [];
        if (is_array($permissions)){
            foreach ($permissions as $permission){
                if (!empty($permission) && $this->modx->hasPermission($permission)){
                    $list[] = $permission;
                }
            }
        }
 
        return $this->collection($list,$total);
    }    

}
