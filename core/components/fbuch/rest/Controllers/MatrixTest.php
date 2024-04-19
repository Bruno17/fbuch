<?php

class MyControllerMatrixTest extends fbuchRestController {
    
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


    public function getList() {
        $total = 0;
        $list = [];

        $modx = &$this->modx;

        $mocCorePath = realpath($modx->getOption('matrixorgclient.core_path', null, $modx->getOption('core_path') . 'components/matrixorgclient')) . '/';
        $this->moc = $modx->getService('matrixorgclient', 'MatrixOrgClient', $mocCorePath . 'model/matrixorgclient/');

        $fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
        $this->fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');  
        
        $moc = & $this->moc;

        $result = $moc->login();
        //$result = $this->api->createRoom($fields);

        //$room_id = '!QSCpUchiLvpzxvcKyh:matrix.org';
        $space_id = '!NUxdvlonAPKPHUxTmF:matrix.org';//Kommende Termine
        //$room_id = '!LgQbLPsPUxsTuyPSvz:matrix.org';//Testraum2
        //$room_id = '!OtKeTeBsIHcfBeiXbS:matrix.org';
        //$room_id='!QUNMiEyMlZXjbgEgVX:matrix.org';
        $room_id='!YqZhPOuJhkSJoRiAml:matrix.org';//So 25.09.2022 18:06 testermin


        $fields = [
            "auto_join"=>false,
            "suggested"=> false,    
            "via"=> [
              "matrix.org"
            ]
    
        ];
        $fields = [];//zum Entfernen aktivieren
        $list['putRoomToSpace'] = $moc->api->putRoomToSpace($space_id,$room_id,$fields);
        //$list['putSpaceToRoom'] = $moc->api->putSpaceToRoom($space_id,$room_id,$fields);  
        
        $list['room_state'] = $moc->api->getState($room_id);
        $list['space_state'] = $moc->api->getState($space_id,'m.space.child',$room_id);  
        
        $list['room_messages'] = $moc->api->getMessages($room_id);
        $list['space_messages'] = $moc->api->getMessages($space_id);
        $list['hierarchy'] = $moc->api->getHierarchy($space_id);

        return $this->collection($list,$total);
    }    

}
