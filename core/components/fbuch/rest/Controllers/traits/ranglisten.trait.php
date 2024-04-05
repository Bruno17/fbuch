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
    
    public function getRangliste(){
        $modx = & $this->modx;
        $gattung = $this->getProperty('gattung');
        $querytype = $this->getProperty('querytype','members');

        include ('includes/ranglisten.' . $querytype . '.inc.php');
        $helper = new QueryHelper($this);

        $query = $helper->prepareSelect();
        $query = $helper->addJoins($query);
        $query = $helper->addWhere($query);

        $query = $this->addTimeRangeQuery($query);
        $query = $helper->addGroupQuery($query);

        $query .= ' AND g.name = "' . $gattung . '" ';
        $query = $helper->addGroupBy($query);

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