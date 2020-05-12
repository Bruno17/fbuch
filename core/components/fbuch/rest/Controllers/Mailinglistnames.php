<?php

class MyControllerMailinglistnames extends modRestController {
    public $classKey = 'fbuchMailinglistNames';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }    

    public function beforePut() {
        
        $properties = $this->getProperties();
        print_r($properties);
        $object_array = $this->object->toArray();
        print_r($object_array);
        
        die();        

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
        $fields['name_id'] = $this->getProperty('Name_id');
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
        
        $properties = $this->getProperties();
        print_r($properties);
        die();
        
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
        $fields['name_id'] = $this->getProperty('Name_id');
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
        /*  
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
        */
               
                
        $joins = '[{"alias":"Names","on":"list_id=fbuchMailinglist.id and name_id=129"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);
        
        $w = array();
        $w[] = $where;
        $c->where($w);
        
        //$c->prepare();echo $c->toSql();
        return $c;
    }
    
    protected function prepareListObject(xPDOObject $object) {

        $objectArray = $object->toArray();
        $name_id = $object->get('Names_id');
        $name_subscribed = $object->get('Names_subscribed');
        $name_unsubscribed = $object->get('Names_unsubscribed');

        $objectArray['Names_active'] = empty($name_id) ? false : true; 
        $objectArray['Names_subscribed'] = empty($name_subscribed) ? false : true;
        $objectArray['Names_unsubscribed'] = empty($name_unsubscribed) ? false : true;
        

        return $objectArray; 
    }        
    
}