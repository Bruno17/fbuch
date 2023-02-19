<?php

class MyControllerDates extends modRestController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDate';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function get() {
        $pk = $this->getProperty($this->primaryKeyField);
        
        $date = $this->getProperty('date');
        if ($pk == 'new' && !empty($date)){
            return $this->newDate($date);
        }

        if (empty($pk)) {
            return $this->getList();
        }
        return $this->read($pk);
    }    

    public function newDate($date){
        $this->object = $this->modx->newObject($this->classKey);
        if (empty($this->object)) {
            return $this->failure($this->modx->lexicon('rest.err_obj_nf',array(
                'class_key' => $this->classKey,
            )));
        }
        $this->object->set('date',$date);
        $this->object->set('start_time','00:00'); 
        $this->object->set('date_end',$date);
        $this->object->set('end_time','01:30');         
        $objectArray = $this->object->toArray();
                
        return $this->success('',$objectArray);    
    }

    public function put() {
        $deleted = (int) $this->getProperty('deleted');
        if ($deleted == 1) {
            $id = $this->getProperty($this->primaryKeyField,false);
            if (empty($id)) {
                return $this->failure($this->modx->lexicon('rest.err_field_ns',array(
                    'field' => $this->primaryKeyField,
                )));
            }
            $c = $this->getPrimaryKeyCriteria($id);
            $old_deleted = 0;
            if ($object = $this->modx->getObject($this->classKey,$c)) {
                $old_deleted = (int) $object->get('deleted');
            }
            if ($deleted == 1 && $old_deleted == 0){
                if ($this->modx->hasPermission('fbuch_delete_termin') || $object->get('createdby') == $this->modx->user->get('id')) {
                    $this->setProperty('deletedby', $this->modx->user->get('id'));
                    $this->setProperty('deletedon', strftime('%Y-%m-%d %H:%M:%S'));   
                } else {
                    $this->unsetProperty('deleted');
                }
            }
        }       
        parent::put();
    }    

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_delete_termin')) {
            $this->object->set('deletedby', $this->modx->user->get('id'));
            $this->object->set('deletedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_termin')) {
            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPost(array &$objectArray){
        $this->modx->fbuch->checkDateMailinglistNames($this->object->get('id'));
        $this->modx->fbuch->afterCreateDate($this->object);
    }

    public function afterPut(array &$objectArray){
        $this->modx->fbuch->checkDateMailinglistNames($this->object->get('id'));
        $this->modx->fbuch->afterCreateDate($this->object);
        $recurrencies = $this->getProperty('recurrencies');
        if (is_array($recurrencies)){
            foreach($recurrencies as $id => $selected){
                if ($id != $this->object->get('id') && $selected) {
                    $this->updateRecurrence($id);
                }
            }
        }
    }

    public function updateRecurrence($id){
        if ($object = $this->modx->getObject($this->classKey,['id'=>$id])){
            $protected_fields = $object->get('protected_fields');
            $fields = [
                'title',
                'description',
                'start_time',
                'end_time',
                'mailinglist_id',
                'type',
                'max_reservations'
            ];
            $copy_fields = is_array($protected_fields) && count($protected_fields) > 0 ? (array_diff($fields,$protected_fields)):$fields;
            foreach ($copy_fields as $field){
                $object->set($field,$this->object->get($field));
            }
            $object->set('editedby', $this->modx->user->get('id'));
            $object->set('editedon', strftime('%Y-%m-%d %H:%M:%S'));  
            $object->save();           

            $this->modx->fbuch->checkDateMailinglistNames($id);
            $this->modx->fbuch->afterCreateDate($object);  
        }
    }
    
    
    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_create_termin')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        return true;
    }
    
    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $returntype = $this->getProperty('returntype');
        $which_page = $this->getProperty('which_page');

        $joins = '[{"alias":"Type"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $start = $this->getProperty('start','undefined');
        $start = $this->getProperty('start_date',$start);
        $end = $this->getProperty('end','undefined');
        $end = $this->getProperty('end_date',$end);  
        $dir = $this->getProperty('dir','ASC');      
        

     /*   
        if($show_hidden){
            $where = array('deleted' => 0);
        }else{
            $where = array('deleted' => 0,'hidden' => 0);    
        }
        $w = array();
        switch ($which_page){
            case 'edit_mailinglists':
            $w[] = array('member_filter_id' => 0);
            break;
        }
        
        
        $w[] = $where;
        $c->where($w);
        
        $c->groupby('type');
        */

        switch ($which_page){
            case 'rowinglogbook':
            $c->where(['Type.show_at_rowinglogbook_page' => 1]);
            break;
        }

        $c->where(['deleted' => 0]);
        if (isset($_GET['types']) && !empty($_GET['types'])){
            $types = explode(',',$_GET['types']);
            $c->where(['type:IN' => $types]);
        }

        if (isset($_GET['parent']) && !empty($_GET['parent'])){
            $c->where(['parent' => (int) $_GET['parent']]);
        }  
        
        if (isset($_GET['show_hidden']) && !empty($_GET['show_hidden'])){
 
        } else {
            $c->where(['hidden' => 0]);
        }         
        
        if ($start != 'undefined' && $end != 'undefined'){
            $c->where(['date_end:>=' => $start]);
            $c->where(['date:<=' => $end]);
        } elseif ($start != 'undefined') {
            $c->where(['date:>=' => $start]);
        } else {
            $date = strftime('%Y-%m-%d 00:00:00');
            $c->where(['date:>=' => $date]);
        }
        $c->sortby('date',$dir);
        $c->sortby('start_time',$dir);
        //$c->prepare();echo $c->toSql();
        return $c;
        
    }

    public function afterRead(array &$objectArray) {
        $objectArray['protected_fields'] = is_array($objectArray['protected_fields']) ? $objectArray['protected_fields'] : [];
        return !$this->hasErrors();
    } 
    
    public function addNames($id){
        $memberfields = 'name,firstname,member_status';
        $properties = [];
        $properties['classname'] = 'fbuchDateNames';
        $properties['where'] = '{"date_id":"' . $id . '"}';
        $properties['joins'] = '[{"alias":"Member","selectfields":"' . $memberfields . '"},{"alias":"RegisteredbyMember","selectfields":"' . $memberfields . '"},{"alias":"Fahrtname","selectfields":"id,fahrt_id"},{"alias":"Fahrt","classname":"fbuchFahrt","selectfields":"id","on":"Fahrt.id=Fahrtname.fahrt_id and Fahrt.deleted=0"}]';
        $properties['sortConfig'] = '[{"sortby":"Fahrtname.fahrt_id"},{"sortby":"registeredby_member"},{"sortby":"createdon"}]';
        $names = [];

        $c = $this->modx->migx->prepareQuery($this->modx,$properties);
        $rows = $this->modx->migx->getCollection($c);
        if (count($rows)>0){
            $idx = 1;
            $registeredby = '0';
            $fahrt_id = '0';
            foreach ($rows as $row){
                $row['Fahrt_id'] = $row['Fahrtname_fahrt_id'] = $this->modx->getOption('Fahrtname_fahrt_id',$row,'0');
                $row['new_fahrt_id'] = false;
                if ($row['Fahrt_id'] != $fahrt_id){
                    $row['new_fahrt_id'] = true;
                    $fahrt_id = $row['Fahrt_id'];    
                }
                $row['new_registeredby'] = false;
                if (isset($row['registeredby_member']) && $row['registeredby_member'] != $registeredby){
                    $row['new_registeredby'] = true;
                    $registeredby = $row['registeredby_member'];
                }
                $row['selected'] = false;
                $row['idx'] = $idx;
                if (isset($row['createdon'])){
                    $date = date_create($row['createdon']);
                    $row['createdon_formatted'] = date_format($date,'d.m.Y H:i');    
                }
                
                $names[] = $row;
                $idx ++;
            }
        }


        return $names;
    }
    
    protected function prepareListObject(xPDOObject $object) {
        $date = $object->toArray();
        $date['Type_colorstyle'] = (string) $date['Type_colorstyle'] == '' ? 'grey' : $date['Type_colorstyle'];
        $names = $this->addNames($object->get('id'));
        $date['names'] = $names;
        $date['counted_names'] = count($names);
        return $date;
    } 
       

}
