<?php

include 'BaseController.php';

class MyControllerFahrten extends BaseController {
    public $classKey = 'fbuchFahrt';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }    

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
            $this->setProperty('editedby', $this->modx->user->get('id'));
            $this->setProperty('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
            $this->validateProperties();
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function getPrimaryKeyCriteria($id) {

        $c = $this->modx->newQuery($this->classKey);
        $c->select($this->modx->getSelectColumns($this->classKey,$this->classKey));
        $joins = '[{"alias":"Boot"},
        {"alias":"Gattung","classname":"fbuchBootsGattung","on":"Gattung.id=Boot.gattung_id"},
        {"alias":"Nutzergruppe","classname":"fbuchBootsNutzergruppe","on":"Nutzergruppe.id=Boot.nutzergruppe"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        $c->where(array($this->primaryKeyField => $id));
        //$c->prepare();echo $c->toSql();die();
        return $c;
    }    

    public function get() {
        $pk = $this->getProperty($this->primaryKeyField);
        
        $date = $this->getProperty('date');
        if ($pk == 'new' ){
            return $this->newEntry();
        }

        if (empty($pk)) {
            return $this->getList();
        }
        return $this->read($pk);
    } 

    public function afterRead(array &$objectArray) {
        $names = $this->addNames($this->object);
        $rows = [];
        if (is_array($names)){ 
            foreach ($names as $name){
                $row = [];
                $row['id'] = $this->modx->getOption('member_id',$name,'');
                $row['value'] = $this->modx->getOption('member_id',$name,'');
                $row['firstname'] = $this->modx->getOption('Member_firstname',$name,'');
                $row['name'] = $this->modx->getOption('Member_name',$name,'');
                $row['member_status'] = $this->modx->getOption('Member_member_status',$name,'');
                $row['label'] = $row['name'] . ' ' . $row['firstname'];
                $rows[] = $row;
            }
        }
        $objectArray['names'] = $rows; 
        return !$this->hasErrors();
    }     
    
    public function newEntry(){
        $this->object = $this->modx->newObject($this->classKey);
        if (empty($this->object)) {
            return $this->failure($this->modx->lexicon('rest.err_obj_nf',array(
                'class_key' => $this->classKey,
            )));
        }

        $date = new DateTime(null, new DateTimeZone('Europe/Berlin'));
        $date_end = new DateTime(null, new DateTimeZone('Europe/Berlin'));   
        $date = $this->modx->fbuch->date_round($date);
        $date_end = $this->modx->fbuch->date_round($date_end);
   
        date_add($date_end,date_interval_create_from_date_string("90 minutes"));

        $this->object->set('date',date_format($date,'Y-m-d 00:00:00'));
        $this->object->set('start_time',date_format($date,'H:i'));
        $this->object->set('date_end',date_format($date_end,'Y-m-d 00:00:00'));
        $this->object->set('end_time',date_format($date_end,'H:i'));       
        $objectArray = $this->object->toArray();
                
        return $this->success('',$objectArray);    
    }    
    
    public function afterPut(array &$objectArray) {
        //remove old, unused name(s)
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'afterPut');
        $fields = array();
        $fields['member_id'] = $this->getProperty('Member_id');
        $fields['fahrt_id'] = isset($objectArray['id']) ? $objectArray['id'] : 0;

        $c = $this->modx->newQuery('fbuchFahrtNames');
        $c->where(array('fahrt_id'=>$fields['fahrt_id'],'fahrt_id:!='=>$fields['fahrt_id']));
        if ($collection = $this->modx->getCollection('fbuchFahrtNames',$c)){
            foreach ($collection as $object){
                $object->remove();
            }
        }
        $this->afterSave($fields);

    }       

    public function beforePost() {
        $this->modx->log(modX::LOG_LEVEL_ERROR, 'beforePut');
        if ($this->modx->hasPermission('fbuch_create_fahrten')) {
            $this->setProperty('createdby', $this->modx->user->get('id'));
            $this->setProperty('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
            $this->validateProperties();
        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }
    
    public function afterPost(array &$objectArray) {
        $fields = array();
        $fields['member_id'] = $this->getProperty('Member_id');
        $fields['fahrt_id'] = isset($objectArray['id']) ? $objectArray['id'] : 0;
        $this->afterSave($fields);
    }

    public function validateProperties(){
        $properties = $this->getProperties();
        if (isset($properties['kmstand_start'])){
            $this->object->set('kmstand_start',(int) $properties['kmstand_start']);
        }
        if (isset($properties['kmstand_end'])){
            $this->object->set('kmstand_end',(int) $properties['kmstand_end']);
        }
        if (isset($properties['km'])){
            $this->object->set('km',(float) $properties['km']);
        }                
    }

    public function saveNames(){

        $names = $this->getProperty('names',[]);
        $existing = [];
        if (is_array($names) & count($names) > 0) {
            //first remove all Guests
            if ($members = $this->object->getMany('Names')) {
                foreach ($members as $member) {
                    if ($this->modx->fbuch->isguest($member->get('member_id'))) {
                        $member->remove();
                    } else {
                        $existing[$member->get('member_id')] = $member;
                    }
                }
            }

            foreach ($names as $name) {
                $member_id = $this->modx->getOption('value',$name,0);
                if (!$this->modx->fbuch->isguest($member_id) && $fahrtnam = $this->modx->getObject('fbuchFahrtNames', array('fahrt_id' => $this->object->get('id'), 'member_id' => $member_id))) {
                    //name exists allready in fahrt, do nothing
                } else {
                    if (!empty($member_id) && $fahrtnam = $this->modx->newObject('fbuchFahrtNames')) {
                        $fahrtnam->set('member_id', $member_id);
                        $fahrtnam->set('fahrt_id', $this->object->get('id'));
                        $fahrtnam->save();
                    }
                }
                unset($existing[$member_id]);
            }
            foreach ($existing as $member){
                $member->remove();
            }
        }

        if ($grid_id == 'Ergofahrten' && !empty($values['member_id'])) {
            if ($fahrtnam = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $object->get('id')))) {
                $fahrtnam->set('member_id', $values['member_id']);
            } else {
                $fahrtnam = $modx->newObject('fbuchFahrtNames');
                $fahrtnam->set('member_id', $values['member_id']);
                if (!empty($values['datenames_id'])) {
                    $fahrtnam->set('datenames_id', $values['datenames_id']);
                }
                $fahrtnam->set('fahrt_id', $object->get('id'));
            }
            $fahrtnam->save();
        }
    }
    
    public function afterSave($fields){
        $this->saveNames($fields);
        return;

        $kmstand_start = $this->getProperty('kmstand_start');
        $kmstand_end = $this->getProperty('kmstand_end');
        if ($kmstand_end > $kmstand_start){
            $this->object->set('km',$kmstand_end-$kmstand_start);        
        }  else {
            $this->object->set('km',0); 
            $this->object->set('kmstand_end',0);  
        }
        $this->object->save();
        
        if ($this->modx->getObject('fbuchFahrtNames',$fields)){
            
        }else{
            $fahrtname = $this->modx->newObject('fbuchFahrtNames');
            $fahrtname->fromArray($fields);
            $fahrtname->save();
        }        
    }    

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')){
            return false;
        }
        return true;
    }
    
    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        
        $returntype = $this->getProperty('returntype');
        $show_in_offen = (int) $this->getProperty('show_in_offen',0);
        $where = [];
        $where['deleted'] = 0;
        $datewhere = [];
        $sortConfig = [];
        $finishedwhere = [];

        if ($show_in_offen == 1) {
            $where['Gattung.show_in_offen'] = '1';
        }
      
        switch ($returntype) {
            case 'open':
                $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                $finishedwhere['km'] = 0;
                $finishedwhere['finished'] = 0;
                $datewhere['date:<='] = strftime('%Y-%m-%d 23:59:59');
                $datewhere['start_time:<='] = strftime('%H:%M');
                $datewhere['OR:date:<'] = strftime('%Y-%m-%d 00:00:00');
                break;
            case 'sheduled':
                $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                $finishedwhere['km'] = 0;
                $finishedwhere['finished'] = 0;
                $datewhere['date:>='] = strftime('%Y-%m-%d 00:00:00');
                $datewhere['start_time:>'] = strftime('%H:%M');
                $datewhere['OR:date:>'] = strftime('%Y-%m-%d 23:59:00');                
                
                break;                
            case 'finished':
                $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                $finishedwhere['km:>'] = 0;
                $finishedwhere['OR:finished:='] = 1;
                if (isset($_GET['start_date']) && isset($_GET['end_date'])){
                    $start = $this->getProperty('start_date');
                    $end = $this->getProperty('end_date');
                    $datewhere['date:>='] = $start;
                    $datewhere['date:<='] = $end;
                }        
                break; 
                case 'finished_coming':
                    $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                    $finishedwhere['km:>'] = 0;
                    $finishedwhere['OR:finished:='] = 1;
                    if (isset($_GET['start_date']) && isset($_GET['end_date'])){
                        $end = $this->getProperty('end_date');
                        $datewhere['date:>'] = $end;
                    }        
                break; 
                case 'finished_past':
                    $sortConfig = ['date'=>'DESC','start_time'=>'DESC'];
                    $finishedwhere['km:>'] = 0;
                    $finishedwhere['OR:finished:='] = 1;
                    if (isset($_GET['start_date']) && isset($_GET['end_date'])){
                        $start = $this->getProperty('start_date');
                        $datewhere['date:<'] = $start;
                    }        
                break;                                               
        } 
        
        
                
        $joins = '[{"alias":"Boot"},
        {"alias":"Gattung","classname":"fbuchBootsGattung","on":"Gattung.id=Boot.gattung_id"},
        {"alias":"Nutzergruppe","classname":"fbuchBootsNutzergruppe","on":"Nutzergruppe.id=Boot.nutzergruppe"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        
        if ($gattung = $this->getProperty('gattung')){
            $where['Gattung.name'] = $gattung;
        }
        $w = array();
        $w[] = $where;
        $w[] = $datewhere;
        $w[] = $finishedwhere;
         $c->where($w);

        foreach ($sortConfig as $field => $dir){
            $c->sortby($field,$dir);
        }
        
        //$c->prepare();echo $c->toSql();
        return $c;
    }

    public function calculateAge($birthdate,$when){
        $date = date_create($when);
        $when = date_format($date, 'Y-12-31 23:59:59'); 
        $age = (int) $this->modx->fbuch->berechneAlter(['birthdate' => $birthdate,'when' => $when]);
        return $age;
    }

    public function calculateAverage($names,& $error){
        $ages = [];
        if (is_array($names)){
            foreach ($names as $name){
                $cox = $this->modx->getOption('cox',$name,'0');
                $age = $this->modx->getOption('age',$name,'0');
                if ($cox == '0'){
                  $ages[] = $age;
                } 
            } 
        }

        $average = $this->modx->fbuch->calculateAverage($ages,$error);

        return $average;
    }

    public function addNames($object){
        $id = $object->get('id');
        $nutzergruppe_id = (int) $object->get('Nutzergruppe_id');
        $memberfields = 'name,firstname,member_status';
        $memberfields .= $this->modx->hasPermission('fbuch_view_birthday') ? ',birthdate' : '';
        $properties = [];
        $properties['classname'] = 'fbuchFahrtNames';
        $properties['where'] = '{"fahrt_id":"' . $id . '"}';
        $properties['joins'] = '[{"alias":"Member","selectfields":"'. $memberfields .'"},{"alias":"NgMember","classname":"fbuchBootsNutzergruppenMembers","on":"NgMember.member_id=Member.id and NgMember.group_id=' . $nutzergruppe_id .'"}]';
        $properties['sortConfig'] = '[{"sortby":"obmann","sortdir":"DESC"},{"sortby":"pos"}]';
        $properties['debug'] = '0';
        $names = [];

        $c = $this->modx->migx->prepareQuery($this->modx,$properties);
        $rows = $this->modx->migx->getCollection($c);
        if (count($rows)>0){
            $idx = 1;
            foreach ($rows as $row){
                $row['selected'] = false;
                $row['idx'] = $idx;
                $row['age'] = $this->modx->hasPermission('fbuch_view_birthday') ? $this->calculateAge($row['Member_birthdate'],$object->get('date')) : 0;
                $names[] = $row;
                $idx ++;
            }
        }

        return $names;
    }    
    
    protected function prepareListObject(xPDOObject $object) {
        $names = $this->addNames($object);
        if (count($names) == 1){
            $name_array = $names[0];
            foreach ($name_array as $field => $value){
                if (substr($field,0,7)=='Member_'){
                    $object->set($field,$value);
                }
            }
            $object->set('Member_fullname',$object->get('Member_firstname') . ' ' . $object->get('Member_name'));                            
        }

        $objectArray = $object->toArray();
      
        $objectArray['names'] = $names;
        $error = false;
        $objectArray['average_age'] = $this->calculateAverage($names,$error);
        $objectArray['average_error'] = $error;
        $objectArray['date'] = substr($object->get('date'),0,10);
        $objectArray['date_end'] = substr($object->get('date_end'),0,10);
        
        return $objectArray; 
    }        
    
}