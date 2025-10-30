<?php

class QueryHelper {

    public function __construct($controller){
        $this->modx = &$controller->modx;
        $this->controller = &$controller;
    }

    public function getProperty($property) {
        return $this->controller->getProperty($property);
    }

    public function prepareSelect(){
        $returntype = $this->getProperty('returntype');
        $group = $this->getProperty('group');
        switch ($returntype){
            case 'member_fahrten':
                $query = 'select f4.id as id ';
            break;
            default:
                    $query = 'SELECT b1.id as id,  b1.name as Name, SUM(f4.km) as km, COUNT(*) as Fahrten ';
             break;
        }
        $query .= 'FROM  modx_fbuch_boote b1 ';
        return $query;
    }  
    
    public function addJoins($query){
        $query .='
        left join modx_fbuch_bootsgattungen g on b1.gattung_id=g.id
        left join modx_fbuch_fahrten f4 on b1.id=f4.boot_id 
        ';
        return $query;
    } 
    
    public function addWhere($query){
        $returntype = $this->getProperty('returntype');
        $boot_id = $this->getProperty('member_id');

        $query .= '
        where f4.deleted = 0 
        AND f4.finished = 1 ';

        if ($returntype == 'member_fahrten'){
            $query .= ' AND b1.id =' . $boot_id . ' ';
        }        

        return $query;
    }
    
    public function addGroupQuery($query){
        return $query;
    }  
    
    public function addGroupBy($query){
        $returntype = $this->getProperty('returntype');
        if ($returntype == 'member_fahrten'){
            return $query;
        } 
        $query .= ' GROUP BY  b1.id,b1.name ';            
        $query .= ' order by km desc ';

        return $query;
    } 
    
    public function addUnused($list,$all_ids){
        //print_r($all_ids);
        $gattung = $this->controller->getProperty('gattung');
        $gattung_ids = [];
        if ($gattungen = $this->modx->getCollection('fbuchBootsGattung',['name'=>$gattung])){
            foreach ($gattungen as $gattung_o){
                $gattung_ids[] = $gattung_o->get('id');    
            }
            
        }

        $c = $this->modx->newQuery('fbuchBoot');
        $c->where(['id:NOT IN'=>$all_ids,'gattung_id:IN'=>$gattung_ids,'deleted'=>0]);
        $c->sortBy('Name');
        //$c->prepare();echo $c->toSql();
        if ($collection = $this->modx->getCollection('fbuchBoot',$c)){
            foreach ($collection as $object){
                $item = [
                    'id'=>$object->get('id'),
                    'Name'=>$object->get('name'),   
                    'km'=>0,
                    'Fahrten'=>0     
                ];
                $list[] = $item;
            }
        }

        return $list;
    }    
}