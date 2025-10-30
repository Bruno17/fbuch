<?php
//return '';

$date_id = $modx->getOption('date_id', $scriptProperties, 0);
if (!empty($date_id) && $date_object = $modx->getObject('fbuchDate',array('id'=>$date_id))){
    
} else {
    $date_object = &$modx->getOption('date_object', $scriptProperties, null);    
}

$name_id = $modx->getOption('name_id', $scriptProperties, 0);
if (!empty($name_id) && $name_object = $modx->getObject('mvMember',array('id'=>$name_id))){
    
} else {
    $name_object = &$modx->getOption('name_object', $scriptProperties, null);  
}

$run = &$modx->getOption('run',$scriptProperties,null);
$task = &$modx->getOption('task',$scriptProperties,null);

$results = array();


$action = $modx->getOption('action', $scriptProperties, '');
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

if ($action == 'createDateRoom' && $date_object) {
    $results = $moc->createDateRoom($date_object);
}

if ($action == 'invite' && $date_object && $name_object) {
    $invite_action = $modx->getOption('invite_action', $scriptProperties, '');
    $room_id = $date_object->get('riot_room_id');
    $riot_user_id = $name_object->get('riot_user_id');
    
    if ($run && !empty($riot_user_id) && empty($room_id)){
        $run->addError('keine riot_room_id', array(
            'riot_room_id' => $room_id,
            'date_id' => $date_object->get('id'),
            'rescheduled?' => '+5 minute'
        ));
        //$task->schedule('+5 minute',$run->get('data'));
        
    }

    if (!empty($room_id) && !empty($riot_user_id)) {
        $result = $moc->login();
        //echo '<pre>' . print_r($result, 1) . '</pre>';
        $result = $api->invite($room_id, array('user_id' => $riot_user_id));
        $results['invite'] = $result;
        //echo '<pre>' . print_r($result, 1) . '</pre>';
        //die();
        if (isset($result['status']) && $result['status'] == 200) {

        }
    } elseif ($invite_action == 'riotinvite_invites') {
        $email = $fbuch->getNameEmail($name_object);
        if (!empty($email)) {
            //$result = $moc->login();
            //echo '<pre>' . print_r($result, 1) . '</pre>';
            //$result = $api->invite($room_id, array('address' => $email));
            //echo '<pre>' . print_r($result, 1) . '</pre>';
            //die();
            if (isset($result['status']) && $result['status'] == 200) {

            }
        }

    }
}

if ($action == 'send' && $date_object && $name_object) {
    $room_id = $date_object->get('riot_room_id');
    $riot_user_id = $name_object->get('riot_user_id');
    $name = $name_object->get('firstname') . ' ' . $name_object->get('name');
    $comment_start = $modx->getOption('comment_start',$scriptProperties,'Von ');
    $comment = $comment_start . "$name ($riot_user_id): \n" . $modx->getOption('comment', $scriptProperties, '');
 
    if (!empty($room_id) && !empty($name)) {
        $result = $moc->login();
        //echo '<pre>' . print_r($result, 1) . '</pre>';
        $result = $api->send($room_id, 'm.room.message', array('body' => $comment, 'msgtype' => 'm.text'));
        //echo '<pre>' . print_r($result, 1) . '</pre>';
        //die();
        if (isset($result['status']) && $result['status'] == 200) {

        }
    }
}

return json_encode($results);