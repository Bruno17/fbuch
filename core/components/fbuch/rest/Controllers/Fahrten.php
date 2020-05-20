<?php

class MyControllerFahrten extends modRestController {
    public $classKey = 'fbuchFahrt';
    public $defaultSortField = 'date';
    public $defaultSortDirection = 'ASC';
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }    

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_fahrten')) {
            $this->setProperty('editedby', $this->modx->user->get('id'));
            $this->setProperty('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }
    
    public function afterPut(array &$objectArray) {
        //remove old, unused name(s)
        $fields = array();
        $fields['member_id'] = $this->getProperty('Name_id');
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

        if ($this->modx->hasPermission('fbuch_create_fahrten')) {
            $this->setProperty('createdby', $this->modx->user->get('id'));
            $this->setProperty('createdon', strftime('%Y-%m-%d %H:%M:%S')); 

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }
    
    public function afterPost(array &$objectArray) {
        $fields = array();
        $fields['member_id'] = $this->getProperty('Name_id');
        $fields['fahrt_id'] = isset($objectArray['id']) ? $objectArray['id'] : 0;
        $this->afterSave($fields);
       
    }
    
    public function afterSave($fields){
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
        $where = array('deleted'=>0);
        $datewhere = array();
      
        switch ($returntype) {
            case 'open':
                $this->setProperty('dir','ASC');
                $where['km'] = 0;
                $datewhere['date:<='] = strftime('%Y-%m-%d 23:59:59');
                $datewhere['start_time:<='] = strftime('%H:%M');
                $datewhere['OR:date:<'] = strftime('%Y-%m-%d 00:00:00');
                break;
            case 'sheduled':
                $this->setProperty('dir','ASC');
                $where['km'] = 0;
                $where['date:>='] = strftime('%Y-%m-%d 00:00:00');
                
                $datewhere['date:>='] = strftime('%Y-%m-%d 00:00:00');
                $datewhere['start_time:>'] = strftime('%H:%M');
                $datewhere['OR:date:>'] = strftime('%Y-%m-%d 23:59:00');                
                
                break;                
            case 'finished':
                $this->setProperty('dir','DESC');
                $where['km:>'] = 0;
                break;                
        } 
        
        
                
        $joins = '[{"alias":"Boot"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        
        if ($gattung = $this->getProperty('gattung')){
            $where['Boot.gattung'] = $gattung;
        }
        $w = array();
        $w[] = $where;
        $w[] = $datewhere;
        $c->where($w);
        
        //$c->prepare();echo $c->toSql();
        return $c;
    }
    
    protected function prepareListObject(xPDOObject $object) {
        
        $names_array = array();
        if ($fahrt_names = $object->getMany('Members')){
            foreach ($fahrt_names as $fahrt_name){

                if ($name = $fahrt_name->getOne('Member')){
                    $name_array = $name->toArray();
                    foreach ($name_array as $field => $value){
                        $fahrt_name->set('Member_' . $field,$value);
                        if (count($fahrt_names == 1)){
                            $object->set('Member_' . $field,$value);
                            $object->set('Member_fullname',$name->get('firstname') . ' ' . $name->get('name'));
                        }                            
                    }
                    
                }
                $names_array[] = $fahrt_name->toArray();
                
            }
        }
        $objectArray = $object->toArray();
        $objectArray['names'] = $names_array;
        $objectArray['date'] = substr($object->get('date'),0,10);
        $objectArray['date_end'] = substr($object->get('date_end'),0,10);
        
        return $objectArray; 
    }        
    
}