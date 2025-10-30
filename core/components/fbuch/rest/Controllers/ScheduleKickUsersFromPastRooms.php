<?php

class MyControllerScheduleKickUsersFromPastRooms extends fbuchRestController {
    
    public $classKey = 'modResource';
 
    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePost() {
        throw new Exception('Unauthorized', 401);
    }

    public function verifyAuthentication() {
        return true;
    }

    public function scheduleKickUsersFromPastRooms(){
        $modx = &$this->modx;
        $query_date = strftime('%Y-%m-%d 23:59:59', strtotime('- 30 day'));
        $c = $modx->newQuery('fbuchDate');
        $c->where(['riot_room_id:!='=>'','date:<' => $query_date,'matrix_members_kicked'=>'0']); 
        $c->sortby('date','DESC');
        $c->limit(100);
        //$c->prepare();echo $c->toSql();
        if ($collection = $modx->getIterator('fbuchDate', $c)) {
            $count = 0;
            $perminute = 5;
            $minutes = 1;
            foreach ($collection as $object) {
                echo '<pre>' . print_r($object->toArray(),1) . '</pre>';
                $count++;
                $scriptProperties = array('date_id' => $object->get('id'));
                $reference = 'web/mocschedule/kickusersfrompastroom';
                $modx->fbuch->createSchedulerTask('fbuch', array('snippet' => 'web/mocschedule/kickusersfrompastroom'));
                $modx->fbuch->createSchedulerTaskRun($reference, 'fbuch', $scriptProperties,$minutes);
                
                if ($count >= $perminute){
                    $minutes++;
                    $count=0;
                }
                               
            }
        }               
    }

    public function getList() {
        $total = 0;
        $list = [];

        $this->scheduleKickUsersFromPastRooms();       
 
        return $this->collection($list,$total);
    }    

}
