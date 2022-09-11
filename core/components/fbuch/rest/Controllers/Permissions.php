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

    public function getList() {
        $total = 0;
        $permissions = explode(',',$this->getProperty('permissions'));
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
