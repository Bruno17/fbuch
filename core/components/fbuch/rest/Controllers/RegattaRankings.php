<?php
include 'BaseController.php';
class MyControllerRegattaRankings extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDate';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function collection($list = [],$total = false,$status = null) {
        if (empty($status)) $status = $this->getOption('defaultSuccessStatusCode',200);
        if ($total === false) {
            $total = count($list);
        }
        $this->summaries = [];
        $this->collectRankings($list);
        $this->response = [
            $this->getOption('collectionResultsKey','results') => $this->summaries,
            $this->getOption('collectionTotalKey','total') => $total
        ];
        $this->responseStatus = $status;
    } 
    
    public function collectRankings($list){
        foreach ($list as $date){
            $this->getFahrten($date);
        }
        return true;
    }

    public function beforePut() {

        throw new Exception('Unauthorized', 401);
        return !$this->hasErrors();
    }

    public function beforePost() {

        throw new Exception('Unauthorized', 401);
        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        return true;
    }

    public function getFahrten($date){
        $date_id = $date['id'];
        $fahrten =[];
        $where = [
            'date_id'=>$date_id,
            'race_final'=>1,
            'race_place:>'=>0,
            'deleted'=>0
            ];
        if ($collection = $this->modx->getCollection('fbuchFahrt',$where)){
            foreach ($collection as $object){
                $names = $this->getNames($object);
                if (count($names) == 0){
                    continue;
                }

                $place = $object->get('race_place');
                $race_id = $object->get('id');
                if (!array_key_exists($place,$this->summaries)){
                    $this->summaries[$place] = [
                        'count' => 0,
                        'members' => [],
                        'guests' => [],
                        'races' => []
                        ];
                }
                
                $fahrt = $object->toArray();
                
                foreach ($names as $name){
                    $member_status = $name['member_status'] == 'Mitglied' ? 'members' : 'guests'; 
                    $member_name = !empty($name['guestname']) ? $name['guestname'] : $name['Member_firstname'] . ' ' . $name['Member_name'];
                    $member_id = $name['member_id']; 
                    if (!array_key_exists($member_id,$this->summaries[$place][$member_status])){
                        $this->summaries[$place][$member_status][$member_id] = [
                            'count' => 0,
                            'name' => $member_name,
                            'regattas' => [],
                            'races' => []
                            ];
                    }
                    if (!array_key_exists($date_id,$this->summaries[$place][$member_status][$member_id]['regattas'])){
                        $this->summaries[$place][$member_status][$member_id]['regattas'][$date_id] = [
                            'count' => 0,
                            'id' => $date_id,
                            'title' => $date['title'],
                            'races' => []
                            ];
                    }
                              
                    $this->summaries[$place][$member_status][$member_id]['count'] ++;
                    $this->summaries[$place][$member_status][$member_id]['regattas'][$date_id]['count'] ++; 
                    if (!in_array($race_id,$this->summaries[$place]['races'])){
                        $this->summaries[$place]['races'][] = $race_id;
                    }                      
                    if (!in_array($race_id,$this->summaries[$place][$member_status][$member_id]['races'])){
                        $this->summaries[$place][$member_status][$member_id]['races'][] = $race_id;
                    }                        
                    if (!in_array($race_id,$this->summaries[$place][$member_status][$member_id]['regattas'][$date_id]['races'])){
                        $this->summaries[$place][$member_status][$member_id]['regattas'][$date_id]['races'][] = $race_id;
                    }                   
                }
                $this->summaries[$place]['count'] ++;
                
                $fahrt['names'] = $names;
                $fahrten[] = $fahrt;
            }
        }
        return $fahrten; 
    }

    public function calculateAge($birthdate,$when){
        $date = date_create($when);
        $when = date_format($date, 'Y-12-31 23:59:59'); 
        $age = (int) $this->modx->fbuch->berechneAlter(['birthdate' => $birthdate,'when' => $when]);
        return $age;
    }    

    public function getNames($object){
        $has_member = false;
        $id = $object->get('id');
        $memberfields = 'name,firstname,member_status';
        $memberfields .= $this->modx->hasPermission('fbuch_view_birthdate') ? ',birthdate' : '';
        $properties = [];
        $properties['classname'] = 'fbuchFahrtNames';
        $properties['where'] = '{"fahrt_id":"' . $id . '"}';
        $properties['joins'] = '
        [{"alias":"Member","selectfields":"'. $memberfields .'"}]';
        $properties['sortConfig'] = '[{"sortby":"cox","sortdir":"ASC"},{"sortby":"pos"}]';
        $properties['debug'] = '0';
        $names = [];

        $c = $this->modx->migx->prepareQuery($this->modx,$properties);
        $rows = $this->modx->migx->getCollection($c);
        if (count($rows)>0){
            $idx = 1;
            foreach ($rows as $row){
                if ($row['member_status'] == 'Mitglied'){
                    $has_member = true;
                }
                $row['age'] = $this->modx->hasPermission('fbuch_view_birthdate') ? $this->calculateAge($row['Member_birthdate'],$object->get('date')) : 0;
                $names[] = $row;
                $idx ++;
            }
        }

        if (!$has_member){
            $names = [];
        }

        return $names;
    }     
    
    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $returntype = $this->getProperty('returntype');
        $which_page = $this->getProperty('which_page');
        $showall_dates = $this->getProperty('showall_dates');

        //$joins = '[{"alias":"Type"}]';
        
        //$this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $start = $this->getProperty('start','undefined');
        $start = $this->getProperty('start_date',$start);
        $end = $this->getProperty('end','undefined');
        $end = $this->getProperty('end_date',$end);  
        $dir = $this->getProperty('dir','ASC');      

        $c->where(['deleted' => 0]);
        if (isset($_GET['types']) && !empty($_GET['types'])){
            $types = explode(',',$_GET['types']);
            $c->where(['type:IN' => $types]);
        }

        $c->where(['hidden' => 0]);

        $c->where(['date_end:>=' => $start]);
        $c->where(['date:<=' => $end]);

        $c->sortby('date',$dir);
        $c->sortby('start_time',$dir);
        //$c->prepare();echo $c->toSql();
        return $c;
        
    }
    
    protected function prepareListObject(xPDOObject $object) {
        $date = $object->toArray();
        //$date['fahrten'] = $this->getFahrten($object);
        //$date['Type_colorstyle'] = (string) $date['Type_colorstyle'] == '' ? 'grey' : $date['Type_colorstyle'];
        //$names = $this->addNames($object);
        //$date['names'] = $names;
        //$date['counted_names'] = count($names);
        //$date['description_with_br'] = nl2br($this->modx->getOption('description',$date,0));
        return $date;
    } 
       

}
