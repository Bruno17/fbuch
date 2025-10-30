<?php
//return '';

$date_object = &$modx->getOption('date_object', $scriptProperties, null);
$name_object = &$modx->getOption('name_object', $scriptProperties, null);
$action = $modx->getOption('action', $scriptProperties, 'send');
//echo '<pre>' . print_r($date_object->toArray(), 1) . '</pre>';
//die();
$mocCorePath = realpath($modx->getOption('matrixorgclient.core_path', null, $modx->getOption('core_path') . 'components/matrixorgclient')) . '/';
$moc = $modx->getService('matrixorgclient', 'MatrixOrgClient', $mocCorePath . 'model/matrixorgclient/');

if ($moc->riot_isactive){
    
} else {
    return '';
}

$api = &$moc->api;

$groups = $moc->groups;
$group = $groups['vereins_mitglieder'];

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');




if ($action == 'send' ) {
    $room_id = '!QCvjbeyYGnwUUxgNsh:matrix.org';

    $comment = 'Test Kommentar';
 
    if (!empty($room_id) ) {
        $result = $moc->login();
        //echo '<pre>' . print_r($result, 1) . '</pre>';
        $result = $api->send($room_id, 'm.room.message', array('body' => $comment, 'msgtype' => 'm.text'));
        echo '<pre>' . print_r($result, 1) . '</pre>';
        //die();
        if (isset($result['status']) && $result['status'] == 200) {

        }
    }
}

return '';