<?php
$mocCorePath = realpath($modx->getOption('matrixorgclient.core_path', null, $modx->getOption('core_path') . 'components/matrixorgclient')) . '/';
$moc = $modx->getService('matrixorgclient', 'MatrixOrgClient', $mocCorePath . 'model/matrixorgclient/');

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

$api = &$moc->api;
$result = $moc->login();
echo '<pre>' . print_r($result, 1) . '</pre>';
//$result = $api->createRoom(array('name' => '2017_05_15 Rudern Allgemein', 'topic' => 'Rudern für Alle'));
//echo '<pre>' . print_r($result, 1) . '</pre>';

if (isset($result['status']) && $result['status'] == 200) {
    if (isset($result['data'])) {
        $data = $result['data'];
        if (isset($data['room_id'])) {
            $room_id = $data['room_id'];
            //$result = $api->invite($room_id, array('address' => 'b.perner@gmx.de'));

            echo '<pre>' . print_r($result, 1) . '</pre>';
        }
    }
}


$room_id = '!mSiJzhSFFoWhPRPzrb:matrix.org';
$result = $api->getState($room_id);
echo '<pre>' . print_r($result, 1) . '</pre>';

if (isset($result['status']) && $result['status'] == 200) {
    if (isset($result['data'])) {
        $data = $result['data'];
        if (is_array($data)){
            $members = array();
            foreach ($data as $row){
                if ($row['type'] == 'm.room.member'){
                    $members[$row['membership']] = $row['user_id'];
                    if ($name_o = $modx->getObject('fbuchNames',array('riot_user_id'=>$row['user_id']))){
                        $name_id = $name_o->get('id');
                        if ($object = $modx->getObject('fbuchDateInvited',array('name_id' => $name_id))){
                            print_r($object->toArray());
                        }    
                    }
                    
                    
                    
                }
            }
            
        }
    }
}





/*
$result = $api->getDevices();
echo '<br>Gefundene Devices:' . count($result['data']['devices']) . '<br>';
$params = array();
for ($i=0;$i<250;$i++){
    echo $i;
    $device = $result['data']['devices'][$i];
    echo '<pre>' . print_r($device, 1) . '</pre>';
    if (!empty($device['device_id']) && empty($device['display_name'])){
        $result2 = $api->deleteDevices($device['device_id'],$params);
        if ($result2['status'] == '401'){
            $params['auth']['session'] = $result2['data']['session'];
            $params['auth']['type'] = 'm.login.password';
            $params['auth']['user'] = $moc->username;
            $params['auth']['password'] = $moc->password;
            $result2 = $api->deleteDevices($device['device_id'],$params);
        }
        echo '<pre>' . print_r($result2, 1) . '</pre>';
    }
}
*/


//$room_id = '!TwxORPYFQSPeEPEJSm:matrix.org';
//$result = $api->send($room_id, 'm.room.message', array('body' => 'Gesendet per API', 'msgtype' => 'm.text'));
//echo '<pre>' . print_r($result, 1) . '</pre>';


$filter = array();
$filter['types'] = array('m.room.message');

//$result = $api->sync(null,$filter);
//echo '<pre>' . print_r($result, 1) . '</pre>';

//$result = $api->setPusher();
//echo '<pre>' . print_r($result, 1) . '</pre>';

//$result = $api->getPushers();
//echo '<pre>' . print_r($result, 1) . '</pre>';

//$result = $api->getPushRules();
//echo '<pre>' . print_r($result, 1) . '</pre>';

//$result = $api->logout();
//echo '<pre>' . print_r($result, 1) . '</pre>';