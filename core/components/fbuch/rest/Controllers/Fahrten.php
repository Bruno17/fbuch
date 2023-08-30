<?php

include 'BaseController.php';

class MyControllerFahrten extends BaseController {
    public $classKey = 'fbuchFahrt';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $defaultLimit = 1000;
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    } 

    public function getProperties(){
        $this->unsetProperty('locked');
        $old_finished = 0;
        $new_finished = (int) $this->getProperty('finished');
        if ($this->object instanceof $this->classKey ){
            $old_finished = (int) $this->object->get('finished');
        }
        if ($new_finished == 1 && $new_finished != $old_finished){
            $now = date_create();
            $this->setProperty('finishedon',date_format($now, 'Y-m-d h:i:s'));
            $this->setProperty('finishedby',$this->modx->user->get('id'));
        }
        if (isset($_REQUEST['finished']) && $new_finished == 0){
            $this->setProperty('finishedon',null);
            $this->setProperty('finishedby',0);
        }        

        return $this->properties;
    }
    
    public function beforePut() {

        if (!$this->modx->fbuch->checkPermission('fbuch_edit_old_entries', array('classname' => $this->classKey, 'object_id' => $this->object->get('id')))) {
            return 'cant_edit_old_entries';
        }

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
            $locked = $this->object->get('locked');

            if (!isset($_REQUEST['set_locked']) && $locked != 0){
                return 'is_locked';
            } elseif (isset($_REQUEST['set_locked'])) {
                $this->object->set('locked',$this->getProperty('set_locked'));
            }

            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
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
        $datenames_id = $this->getProperty('datenames_id',0);
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
        $objectArray['guestname'] = '';
        if (is_array($names)){ 
            foreach ($names as $name){
                $row = [];
                $row['id'] = $this->modx->getOption('member_id',$name,'');
                $row['value'] = $this->modx->getOption('member_id',$name,'');
                $row['datenames_id'] = $this->modx->getOption('datenames_id',$name,'');
                $row['firstname'] = $this->modx->getOption('Member_firstname',$name,'');
                $row['name'] = $this->modx->getOption('Member_name',$name,'');
                $row['member_status'] = $this->modx->getOption('Member_member_status',$name,'');
                $row['label'] = $row['name'] . ' ' . $row['firstname'];
                $row['cox'] = $this->modx->getOption('cox',$name,0);
                $row['obmann'] = $this->modx->getOption('obmann',$name,0);
                $row['guestname'] = $this->modx->getOption('guestname',$name,0);
                $row['guestemail'] = $this->modx->getOption('guestemail',$name,0);
                $row['member_status'] = !empty($row['guestname']) ? 'Gasteintrag' : $row['member_status'];
                $rows[] = $row;
                if ($row['obmann'] == '1') {
                    $objectArray['member_id'] = (int) $row['id'];
                    $objectArray['guestname'] = $this->modx->getOption('guestname',$name,'');
                }
            }
        }
        $objectArray['names'] = $rows; 
        $objectArray['can_edit'] = $this->modx->hasPermission('fbuch_edit_fahrten');
        if (!$this->modx->fbuch->checkPermission('fbuch_edit_old_entries', array('classname' => $this->classKey, 'object_id' => $this->object->get('id')))) {
            $objectArray['can_edit'] = false;
            $objectArray['cant_edit_reason'] = 'cant_edit_old_entries';
        }        
        return !$this->hasErrors();
    } 

    public function newEntry(){
        $gattungname = $this->getProperty('gattungname');

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

        $objectArray['can_edit'] = $this->modx->hasPermission('fbuch_create_fahrten');

        if (!empty($gattungname)){
            $objectArray['Gattung_name'] = $gattungname;    
        }
                
        return $this->success('',$objectArray);    
    }    
    
    public function afterPut(array &$objectArray) {
        //remove old, unused name(s)
        $this->modx->log(modX::LOG_LEVEL_DEBUG, 'afterPut');
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
        //$this->modx->log(modX::LOG_LEVEL_DEBUG, 'beforePut');
        if ($this->modx->hasPermission('fbuch_create_fahrten')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
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

    public function saveSingleName(){
        $member_id = (int) $this->getProperty('member_id',0); 
        $guestname = trim($this->getProperty('guestname','')); 
        $datenames_id = $this->getProperty('datenames_id',0);
        $existing = $this->getExistingNames();
        $existingguests = $this->getExistingGuests();
        if (empty($member_id) && empty($guestname)){
            return;
        }

        if ($member_id != 0 && $fahrtnam = $this->modx->getObject('fbuchFahrtNames', array('fahrt_id' => $this->object->get('id'), 'member_id' => $member_id))) {
            unset($existing[$fahrtnam->get('member_id')]);
        } elseif ($member_id == 0 && $guestname != '' && $fahrtnam = $this->modx->getObject('fbuchFahrtNames', array('fahrt_id' => $this->object->get('id'), 'guestname' => $guestname))) {
            unset($existingguests[$guestname]);
        } elseif ($member_id > 0) {
            $fahrtnam = $this->modx->newObject('fbuchFahrtNames');
            $fahrtnam->set('member_id', $member_id);
            if (!empty($datenames_id)) {
                $fahrtnam->set('datenames_id', $datenames_id);
            }
            $fahrtnam->set('fahrt_id', $this->object->get('id'));
            $fahrtnam->save();
            unset($existing[$member_id]);
        } elseif ($guestname != '') {
            $fahrtnam = $this->modx->newObject('fbuchFahrtNames');
            $fahrtnam->set('guestname', $guestname);
            if (!empty($datenames_id)) {
                $fahrtnam->set('datenames_id', $datenames_id);
            }
            $fahrtnam->set('fahrt_id', $this->object->get('id'));
            $fahrtnam->save();
            unset($existingguests[$guestname]);
        }
        
        
        foreach ($existing as $member){
            $member->remove();
        } 
        foreach ($existingguests as $member){
            $member->remove();
        }                
    }

    public function getExistingNames() {
        $existing = [];
        if ($members = $this->object->getMany('Names')) {
            foreach ($members as $member) {
                $member_id = $member->get('member_id');
                if (!empty($member_id)){
                    $existing[$member_id] = $member;    
                }
            }
        }
        return $existing;               
    }
    public function getExistingGuests() {
        $existing = [];
        if ($members = $this->object->getMany('Names')) {
            foreach ($members as $member) {
                
                $guestname = $member->get('guestname');
                if (!empty($guestname)){
                    $existing[$guestname] = $member;
                }
            }
        }
        return $existing;               
    }    

    public function getBootsgattung($boot_id){
        if ($boot = $this->modx->getObject('fbuchBoot',$boot_id)){
            if ($gattung = $boot->getOne('Bootsgattung')){
                return $gattung;
            }
        }
        return false;
    }

    public function saveNames(){

        /*
        $persons_option = 'team';
        if ($gattung = $this->getBootsgattung($this->object->get('boot_id'))){
            $formoptions = $gattung->get('formoptions');
            $persons_option = $this->modx->getOption('persons',$formoptions,'team');
        }
        */
        $persons_option = $this->getProperty('persons_option','team');

        if ($persons_option == 'single') {
            $this->saveSingleName();
            return;
        }        

        $names = $this->getProperty('names',[]);
        $existing = $this->getExistingNames();
        $existingguests = $this->getExistingGuests();
        
        if (is_array($names) & count($names) > 0) {

            foreach ($names as $name) {
                $member_id = $this->modx->getOption('value',$name,0);
                $guestname = $this->modx->getOption('guestname',$name,0);
                $datenames_id = $this->modx->getOption('datenames_id',$name,0);
                if ($member_id != 0 && $fahrtnam = $this->modx->getObject('fbuchFahrtNames', array('fahrt_id' => $this->object->get('id'), 'member_id' => $member_id))) {
                        $fahrtnam->set('cox', $this->modx->getOption('cox',$name,0));
                        $fahrtnam->set('obmann', $this->modx->getOption('obmann',$name,0));
                        $fahrtnam->save();
                } elseif ($member_id == 0 && $guestname != '' && $fahrtnam = $this->modx->getObject('fbuchFahrtNames', array('fahrt_id' => $this->object->get('id'), 'guestname' => $guestname))) {
                    $fahrtnam->set('cox', $this->modx->getOption('cox',$name,0));
                    $fahrtnam->set('obmann', $this->modx->getOption('obmann',$name,0));
                    $fahrtnam->save();
                } else {
                    if (!empty($member_id) && $fahrtnam = $this->modx->newObject('fbuchFahrtNames')) {
                        $fahrtnam->set('member_id', $member_id);
                        $fahrtnam->set('fahrt_id', $this->object->get('id'));
                        $fahrtnam->set('cox', $this->modx->getOption('cox',$name,0));
                        $fahrtnam->set('obmann', $this->modx->getOption('obmann',$name,0));
                        $fahrtnam->set('datenames_id', $datenames_id);
                        $fahrtnam->save();                        
                    }
                    else if (!empty($guestname) && $fahrtnam = $this->modx->newObject('fbuchFahrtNames')) {
                        $fahrtnam->set('guestname', $guestname);
                        $fahrtnam->set('guestemail', $this->modx->getOption('guestemail',$name,0));
                        $fahrtnam->set('fahrt_id', $this->object->get('id'));
                        $fahrtnam->set('cox', $this->modx->getOption('cox',$name,0));
                        $fahrtnam->set('obmann', $this->modx->getOption('obmann',$name,0));
                        $fahrtnam->set('datenames_id', $datenames_id);
                        $fahrtnam->save();                        
                    }                    
                }
                unset($existing[$member_id]);
                unset($existingguests[$guestname]);
            }            
            foreach ($existing as $member){
                $member->remove();
            }
            foreach ($existingguests as $member){
                $member->remove();
            }            
        }
    }
    
    public function afterSave($fields){
        $date_end = $this->object->get('date_end');
        if (empty($date_end)){
            $this->object->set('date_end',$this->object->get('date'));
            $this->object->save();
        }
        $this->saveNames($fields);
        $fahrt_id = $this->modx->getOption('fahrt_id',$fields,0);
        $this->modx->fbuch->forceObmann($fahrt_id);
        return;
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
                $finishedwhere['finished'] = 0;
                $datewhere['date:<='] = strftime('%Y-%m-%d 23:59:59');
                $datewhere['start_time:<='] = strftime('%H:%M');
                $datewhere['OR:date:<'] = strftime('%Y-%m-%d 00:00:00');
                break;
            case 'sheduled':
                $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                $finishedwhere['finished'] = 0;
                $datewhere['date:>='] = strftime('%Y-%m-%d 00:00:00');
                $datewhere['start_time:>'] = strftime('%H:%M');
                $datewhere['OR:date:>'] = strftime('%Y-%m-%d 23:59:00');                
                break;                
            case 'finished':
                $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                $finishedwhere['finished'] = 1;
                if (isset($_GET['start_date']) && isset($_GET['end_date'])){
                    $start = $this->getProperty('start_date');
                    $end = $this->getProperty('end_date');
                    $datewhere['date:>='] = $start;
                    $datewhere['date:<='] = $end;
                }        
                break; 
                case 'finished_coming':
                    $sortConfig = ['date'=>'ASC','start_time'=>'ASC'];
                    $finishedwhere['finished'] = 1;
                    if (isset($_GET['start_date']) && isset($_GET['end_date'])){
                        $end = $this->getProperty('end_date');
                        $datewhere['date:>'] = $end;
                    }        
                break; 
                case 'finished_past':
                    $sortConfig = ['date'=>'DESC','start_time'=>'DESC'];
                    $finishedwhere['finished'] = 1;
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
        $properties['sortConfig'] = '[{"sortby":"cox","sortdir":"ASC"},{"sortby":"pos"}]';
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