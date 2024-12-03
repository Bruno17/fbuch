<?php

include 'BaseController.php';

class MyControllerBoote extends BaseController {
    public $classKey = 'fbuchBoot';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_boot')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_create_boot')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        if (!$this->modx->hasPermission('fbuch_view_fahrten')) {
            return false;
        }
        return true;
    }

    public function getGattungen(){
        $gattung_ids = $this->object->get('gattung_ids');
        $gattungen = [];
        if (!empty($gattung_ids)){
            $gattung_ids = str_replace('||',',',$gattung_ids);
            $ids = explode(',',$gattung_ids);
            
            if ($collection = $this->modx->getCollection('fbuchBootsGattung',['id:IN'=>$ids])){
                foreach ($collection as $gattung){
                    $gattungen[] = $gattung->toArray();    
                }
            }
        }
        return $gattungen;        
    }

    public function afterRead(array &$objectArray) {
        $returntype = $this->getProperty('returntype');
        $fahrt_gattung_id = $this->getProperty('fahrt_gattung_id');
 

        switch ($returntype){
            case 'availability':
                $boot_id = $this->object->get('id');
                $entry = $this->getProperty('entry',false);
                $start = false;
                $end = false;
                $objectArray['available'] = true;
                if ($entry){
                    $current_id = $this->modx->getOption('id',$entry,'new'); 
                    $startdate = $this->modx->getOption('date',$entry,false); 
                    $enddate = $this->modx->getOption('date_end',$entry,false); 
                    $starttime = $this->modx->getOption('start_time',$entry,false);  
                    $endtime = $this->modx->getOption('end_time',$entry,false);
                    if ($startdate && $starttime) {
                        $start = substr($startdate,0,10) . ' ' . $starttime . ':00';
                        $start = strtotime($start);
                    } 
                    if ($enddate && $endtime) {
                        $end = substr($enddate,0,10) . ' ' . $endtime . ':00';
                        $end = strtotime($end);
                    }
                    if ($start && $end) {
                        $success = $this->modx->fbuch->checkBoatAvailability($boot_id, $start, $end, $current_id);
                        if (!$success){
                            $objectArray['errorstart'] = $this->modx->fbuch->errorstart;
                            $objectArray['errorend'] = $this->modx->fbuch->errorend;
                            $objectArray['available'] = false;
                        }    
                    }
                }
               
                break;
            default:
                $objectArray['fahrt_gattung_id'] = $objectArray['gattung_id'];
                $objectArray['fahrt_nutzergruppe'] = $objectArray['nutzergruppe'];
                if ($gattung = $this->object->getOne('Bootsgattung')){
                    $item = $gattung->toArray();
                    foreach ($item as $field => $value){
                        $objectArray['Bootsgattung_' . $field] = $value;
                    }
                }
                $gruppe = $this->object->getOne('Nutzergruppe');
                if (!empty($fahrt_gattung_id)){
                    $objectArray['fahrt_nutzergruppe'] = $objectArray['nutzergruppe'];
                    $objectArray['fahrt_gattung_id'] = $fahrt_gattung_id;
                    $nutzergruppe = $this->getNutzergruppeByGattung($fahrt_gattung_id); 
                    if(!empty($nutzergruppe) && $object = $this->modx->getObject('fbuchBootsNutzergruppe',['id'=>$nutzergruppe])){
                        $gruppe = $object; 
                        $objectArray['fahrt_nutzergruppe'] = $nutzergruppe;   
                    }   
                }

                if ($gruppe){
                    $item = $gruppe->toArray();
                    foreach ($item as $field => $value){
                        $objectArray['Nutzergruppe_' . $field] = $value;
                    }
                }


                $objectArray['gattungen'] = $this->getGattungen();
                
                break;
        }
         
        return !$this->hasErrors();
    } 
    
    public function getNutzergruppeByGattung($gattung_id){
        $nutzergruppe = 0;
        $gattung_nutzergruppen = $this->object->get('gattung_nutzergruppen');
        $gattung_nutzergruppen = json_decode($gattung_nutzergruppen,true);
        if (is_array($gattung_nutzergruppen)){
            foreach ($gattung_nutzergruppen as $gruppe){
                if(!empty($gruppe['gattung_id']) && !empty($gruppe['nutzergruppe']) && $gruppe['gattung_id'] == $gattung_id){
                    $nutzergruppe = $gruppe['nutzergruppe'];
                }
            }
        }        

        return $nutzergruppe;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        $gattung_name = $this->getProperty('gattung_name',false);
        $gattung_id = $this->getProperty('gattung_id',false);
        $returntype = $this->getProperty('returntype');

        $joins = '[{"alias":"Bootsgattung"},{"alias":"Nutzergruppe"}]';

        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);

        $c->where(array('deleted' => 0));
        if ($gattung_name){
            $c->where(['Bootsgattung.name' => $gattung_name]);
        }
        if ($gattung_id){
            $c->where(['gattung_id' => $gattung_id]);
        } 
        switch ($returntype) {
            case 'gattungnames':
                $c->groupby('Bootsgattung.name');
                $c->sortby('Bootsgattung.name');
                break;            
            case 'bootsgattungen':
                $c->groupby('gattung_id');
                $c->sortby('Bootsgattung.shortname');
                break;
            default:
                $c->sortby('Bootsgattung.shortname');
                $c->sortby('name');
                break;
        }       
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('name') . ' (' . $object->get('Bootsgattung_shortname') . ')';
                $output['value'] = $object->get('id');
                $output['optcolor'] = $object->get('Nutzergruppe_color');
                break;
            case 'bootsgattungen':
                $output['label'] = $object->get('Bootsgattung_longname');
                $output['value'] = $object->get('Bootsgattung_id');
                break;
            case 'gattungnames':
                $output['label'] = $object->get('Bootsgattung_name');
                $output['value'] = $object->get('Bootsgattung_name');
                break;                                   
        }

        return $output;
    }

}
