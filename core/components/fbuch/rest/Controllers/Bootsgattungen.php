<?php

include 'BaseController.php';

class MyControllerBootsgattungen extends BaseController {
    public $classKey = 'fbuchBootsGattung';
    public $defaultSortField = 'shortname';
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

    public function getAssigned(){
        $gattung_ids = [];
        $c = $this->modx->newQuery('fbuchBoot');
        $c->select('id,gattung_id');
        $c->groupby('gattung_id');
        //$c->prepare();echo $c->toSql();
        if ($collection = $this->modx->getCollection('fbuchBoot',$c)){
            foreach ($collection as $object){
                $gattung_ids[] = $object->get('gattung_id');    
            }
        }
        $c = $this->modx->newQuery('fbuchBoot');
        $c->select('id,gattung_ids');
        $c->groupby('gattung_ids');
        //$c->prepare();echo $c->toSql();
        if ($collection = $this->modx->getCollection('fbuchBoot',$c)){
            foreach ($collection as $object){
                $ids = explode('||',$object->get('gattung_ids'));
                if (count($ids)>0){
                    foreach ($ids as $id){
                        if (!in_array($id,$gattung_ids)){
                            $gattung_ids[] = $id;      
                        }
                    }
                }

                  
            }
        }        
        return $gattung_ids;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $gattung_name = $this->getProperty('gattung_name',false);
        if ($gattung_name){
            $c->where(['name' => $gattung_name]);
        } 
        $gattung_ids = $this->getAssigned();
        $c->where(['id:IN' => $gattung_ids]);       

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        return $c;
    }
    
    protected function prepareListObject(xPDOObject $object) {
        $type = $object->toArray();
        $type['label'] = $object->get('longname');
        $type['value'] = $object->get('id');
        return $type;
    }    

}