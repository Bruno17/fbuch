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
        $query = 'select f4.id as id ';
        $query .= 'from modx_fbuch_fahrt_names f8,';
        return $query;
    } 
    
    public function addJoins($query){
        $group = $this->getProperty('group');
        $query .= 'modx_fbuch_fahrten f4 ';
        return $query;
    } 
    
    public function addWhere($query){
        $returntype = $this->getProperty('returntype');
        $member_id = $this->getProperty('member_id');
        $group = $this->getProperty('group');

        $query .= 'WHERE f8.fahrt_id=f4.id ';
        $query .= ' AND f8.member_id=' . $member_id . ' ';  
        $query .= '
        AND f4.deleted = 0 
        AND f4.finished = 1 ';
        return $query;
    } 
    
    public function addGroupQuery($query){
        return $query;
    } 

    public function addGattungQuery($query){
        return $query;      
    }
    
    public function addGroupBy($query){
        return $query;
    } 
    
    public function addUnused($list,$all_ids){
        return $list;
    }

}