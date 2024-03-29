<?php

trait RanglistenTrait {
    public function getGroupOptions(){
        $query =
        [
            'alle'=>['label'=>'Alle Mitglieder','query'=>'AND f8.member_status = "Mitglied" '],
            'SM'=>['label'=>'Männer','query'=>'AND [[+year]]-year(n3.birthdate)>18 AND n3.gender = "männlich" AND f8.member_status = "Mitglied"'],
            'SF'=>['label'=>'Frauen','query'=>'AND [[+year]]-year(n3.birthdate)>18 AND n3.gender = "weiblich" AND f8.member_status = "Mitglied"'],
            'JM'=>['label'=>'Junioren','query'=>'AND [[+year]]-year(n3.birthdate)<=18 AND [[+year]]-year(n3.birthdate)>=15 AND n3.gender = "männlich" AND f8.member_status = "Mitglied"'],
            'JF'=>['label'=>'Juniorinnen','query'=>'AND [[+year]]-year(n3.birthdate)<=18 AND [[+year]]-year(n3.birthdate)>=15 AND n3.gender = "weiblich" AND f8.member_status = "Mitglied"'],
            'Jung'=>['label'=>'Jungen','query'=>'AND [[+year]]-year(n3.birthdate)<=14 AND n3.gender = "männlich" AND f8.member_status = "Mitglied"'],
            'Maed'=>['label'=>'Mädchen','query'=>'AND [[+year]]-year(n3.birthdate)<=14 AND n3.gender = "weiblich" AND f8.member_status = "Mitglied"'],
            'Gast'=>['label'=>'Gast','query'=>'AND f8.member_status = "Gast"'],
            'Gasteintrag'=>['label'=>'Gasteintrag','query'=>'AND f8.member_status = "Gasteintrag"']
        ];
        $cmg = $this->getCustomMemberGroups();
        $query = array_merge($query,$cmg);
        return $query;        
    } 

    public function getCustomMemberGroups(){
        $query = 'select member_status from modx_fbuch_fahrt_names ';
        $query .= 'group by member_status';
        $results = $this->modx->query($query);
        $list = [];
        if ($results) {
            $i = 0;
            while ($r = $results->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($r['member_status'])){
                    $list[$r['member_status']] = ['label'=>$r['member_status'],'query'=>'AND f8.member_status = "' . $r['member_status'] . '"' ];
                }
            }
        }
        return $list;
    }
    
    public function addTimeRangeQuery($query){
        $start = $this->getProperty('start_date');
        $end = $this->getProperty('end_date');

        $query .= " AND if (date_end > '', date_end >= '$start 00:00:00', date >= '$start 00:00:00') ";
        $query .= " AND if (date_end > '', date_end <= '$end 23:59:59', date <= '$end 23:59:59') ";
        
        return $query;
    }

    public function replacePlaceholders($value){
        $start = $this->getProperty('start_date');
        $end = $this->getProperty('end_date');
        $group = $this->getProperty('group');
        $year = (int) substr($end,0,4);
        $year = $year > 1900 && $year < 3000 ? $year : date('Y');
        
        $value = str_replace('[[+year]]',$year,$value);

        return $value;
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
        $options = $this->getGroupOptions();
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

    public function getRangliste(){
        $modx = & $this->modx;
        
        $gattung = $this->getProperty('gattung');

        $query = $this->prepareSelect();
        $query = $this->addJoins($query);
        $query = $this->addWhere($query);

        $query = $this->addTimeRangeQuery($query);
        $query = $this->addGroupQuery($query);

        $query .= ' AND g.name = "' . $gattung . '" ';
        $query = $this->addGroupBy($query);

        $query  = $this->replacePlaceholders($query);

        $results = $modx->query($query);
        $list = [];
        if ($results) {
            $i = 0;
            while ($r = $results->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                $r['Rang'] = $i;
                $list[] = $r;
                if (isset($r['km'])){
                    $this->km_sum += (float) $r['km'];    
                }
            }
        }
        return $list;
    }    

}