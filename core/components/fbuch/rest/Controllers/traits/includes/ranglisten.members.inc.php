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
                if ($group == 'Gasteintrag'){
                    $query = 'select f8.guestname as id, f8.guestname as Nachname ,sum(f4.km) as km,count(f8.guestname)as Fahrten ';
                } else {
                    $query = 'select n3.id as id, n3.name as Nachname,n3.firstname as Vorname,sum(f4.km) as km,count(n3.name) as Fahrten ';
                    if ($this->modx->hasPermission('fbuch_view_birthdate')){
                        $query .= ', year(n3.birthdate) as Jahrgang ';    
                    }                    
                }
            break;
        }
        $query .= 'from modx_fbuch_fahrt_names f8,';
        return $query;
    } 
    
    public function addJoins($query){
        $returntype = $this->getProperty('returntype');
        $group = $this->getProperty('group');
        $query .= 'modx_fbuch_fahrten f4,';
        if ($group != 'Gasteintrag'){
            $query .= 'modx_mv_members n3,';
        } 
        $query .='
        modx_fbuch_boote b1
        left join modx_fbuch_bootsgattungen g on b1.gattung_id=g.id        
        ';
        return $query;
    } 
    
    public function addWhere($query){
        $returntype = $this->getProperty('returntype');
        $member_id = $this->getProperty('member_id');
        $group = $this->getProperty('group');

        $query .= 'WHERE f8.fahrt_id=f4.id ';
        if ($returntype == 'member_fahrten'){
            if ($group == 'Gasteintrag'){
                $query .= ' AND f8.guestname="' . $member_id . '" '; 
            } else {
                $query .= ' AND f8.member_id=' . $member_id . ' ';    
            }
            
        } else {
            if ($group == 'Gasteintrag'){

            } else {
                $query .= '
                AND f8.member_id=n3.id 
                AND n3.name != ""
                ';                  
            }
 
        }        
        $query .= '
        AND b1.id=f4.boot_id
        AND f4.deleted = 0 
        AND f4.finished = 1 ';
        return $query;
    } 
    
    public function addGroupQuery($query){
        $returntype = $this->getProperty('returntype');
        if ($returntype == 'member_fahrten'){
            //return $query;
        }  
        $start = $this->getProperty('start_date');
        $end = $this->getProperty('end_date');
        $group = $this->getProperty('group');
        $options = $this->controller->getGroupOptions();
        if (isset($options[$group])){
            $query .= $options[$group]['query'];  
        } else {
            $query .= $options['alle']['query']; 
        }
        
        return $query;
    } 
    
    public function addGroupBy($query){
        $returntype = $this->getProperty('returntype');
        $group = $this->getProperty('group');
        if ($returntype == 'member_fahrten'){
            return $query;
        } 
        if ($group == 'Gasteintrag'){
            $query .= ' 
            group by f8.guestname
          ';
        } else {
            $query .= ' 
            group by n3.name,n3.firstname
          ';            
        }

        $query .= ' order by km desc ';

        return $query;
    }    

}