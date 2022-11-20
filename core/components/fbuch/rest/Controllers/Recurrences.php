<?php

class MyControllerRecurrences extends modRestController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDate';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_delete_termin')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_termin')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function createRecurrence($date){
        if ($this->object){
            $o = & $this->object;
            $fields = [
                'date' => $date . '00.00.00',
                'date_end' => $this->calculateDateEnd($date,$o->get('date'),$o->get('date_end')),
                'title' => $o->get('title'),
                'type' => $o->get('type'),
                'description' => $o->get('description'),
                'max_reservations' => $o->get('max_reservations'),
                'start_time' => $o->get('start_time'),
                'end_time' => $o->get('end_time'),
                'instructor_id' => $o->get('instructor_id'),
                'instructor_member_id' => $o->get('instructor_member_id'),
                'mailinglist_id' => $o->get('mailinglist_id'),
                'parent' => $o->get('id'),
                'createdby' => $this->modx->user->get('id'),
                'createdon' => date('Y-m-d') 
            ];
        }
        $object = $this->modx->newObject('fbuchDate');
        $object->fromArray($fields);
        if ($object->save()){
            $this->modx->fbuch->checkDateMailinglistNames($object->get('id'));
            $this->modx->fbuch->afterCreateDate($object);            
        }

    }

    public function calculateDateEnd($sourcedate,$startdate,$enddate){
        $date1 = new DateTime($startdate);
        $date2 = new DateTime($enddate);
        $source = new DateTime($sourcedate);
        $diff = $date1->diff($date2);
        $days = $diff->format("%R%a");
        if ($days > 100 || $days < 0){
            $result = $source->format('Y-m-d 00:00:00');
        } else {
            $end = $source->modify($days . ' days');
            $result = $end->format('Y-m-d 00:00:00');
        }
        return $result;
    }

    public function afterCreateRecurrence(array &$objectArray){
        $this->modx->fbuch->checkDateMailinglistNames($this->object->get('id'));
        $this->modx->fbuch->afterCreateDate($this->object);
    }    

    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_create_termin')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function post() {
        $properties = $this->getProperties();
        $parent = (int) $this->modx->getOption('parent',$properties,0);
        /** @var xPDOObject $object */
        if (!empty($parent)){
          $this->object = $this->modx->getObject($this->classKey,['id'=>$parent]);
          $this->object->fromArray($properties);            
        }

        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }

        if (isset($properties['days']) && count($properties['days']) > 0){
            foreach ($properties['days'] as $date){
                $this->createRecurrence($date);
            }
        }

        $objectArray = $this->object->toArray();
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }    

    public function verifyAuthentication() {
        return true;
    }
    
}
