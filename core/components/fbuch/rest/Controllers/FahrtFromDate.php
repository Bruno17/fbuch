<?php

include 'Fahrten.php';

class MyControllerFahrtFromDate extends MyControllerFahrten {

    public function newEntryFromDate($date_id){
        if ($dateobject = $this->modx->getObject('fbuchDate',$date_id)){
            //print_r($dateobject->toArray());
            $this->object = $this->modx->newObject($this->classKey); 
            $this->object->set('date',$dateobject->get('date'));
            $this->object->set('date_end',$dateobject->get('date_end'));
            $this->object->set('start_time',$dateobject->get('start_time'));
            $this->object->set('end_time',$dateobject->get('end_time'));
            $this->object->set('Date_type',$dateobject->get('type'));
            $this->object->set('Date_id',$dateobject->get('id'));
            $this->object->set('date_id',$dateobject->get('id'));

            if ($datetype = $dateobject->getOne('Type')){
                $boot_id = $datetype->get('linkto_boot_id');
                $this->object->set('boot_id',$boot_id);
                $this->object->set('Gattung_name',$datetype->get('linkto_bootsgattung_name'));   
                if ($boot = $this->modx->getObject('fbuchBoot',$boot_id)){
                    if ($gattung = $boot->getOne('Bootsgattung')){
                        $this->object->set('Gattung_name',$gattung->get('name'));     
                    }
                }
                  
            }

            return $this->object;    
        }
        return false;
    }
    
    public function newEntry(){
        $datenames_id = $this->getProperty('datenames_id',0);

        if ($object = $this->modx->getObject('fbuchDateNames',$datenames_id)) {
            //print_r($object->toArray());
            $date_id = $object->get('date_id');
            $this->newEntryFromDate($date_id);
        }

        if (empty($this->object)) {
            return $this->failure($this->modx->lexicon('rest.err_obj_nf',array(
                'class_key' => $this->classKey,
            )));
        } 
        $objectArray = $this->object->toArray();
        $objectArray['datenames_id'] = $datenames_id;       
        $afterRead = $this->afterRead($objectArray);
        if ($afterRead !== true && $afterRead !== null) {
            return $this->failure($afterRead === false ? $this->errorMessage : $afterRead);
        }

        return $this->success('',$objectArray);
    }
    
    public function addNames($object){
        $id = $this->getProperty('datenames_id',0);
        //$nutzergruppe_id = (int) $object->get('Nutzergruppe_id');
        $memberfields = 'name,firstname,member_status';
        $memberfields .= $this->modx->hasPermission('fbuch_view_birthday') ? ',birthdate' : '';
        $properties = [];
        $properties['classname'] = 'fbuchDateNames';
        $properties['where'] = '{"id":"' . $id . '"}';
        $properties['joins'] = '[{"alias":"Member","selectfields":"'. $memberfields .'"}]';
        $properties['debug'] = '0';
        $names = [];

        $c = $this->modx->migx->prepareQuery($this->modx,$properties);
        $rows = $this->modx->migx->getCollection($c);
        if (count($rows)>0){
            $idx = 1;
            foreach ($rows as $row){
                $row['obmann'] = 1;
                $row['selected'] = false;
                $row['idx'] = $idx;
                $row['age'] = $this->modx->hasPermission('fbuch_view_birthday') ? $this->calculateAge($row['Member_birthdate'],$object->get('date')) : 0;
                $names[] = $row;
                $idx ++;
            }
        }

        return $names;
    }    
}