<?php
$mocCorePath = realpath($modx->getOption('matrixorgclient.core_path', null, $modx->getOption('core_path') . 'components/matrixorgclient')) . '/';
$moc = $modx->getService('matrixorgclient', 'MatrixOrgClient', $mocCorePath . 'model/matrixorgclient/');

if ($moc->riot_isactive){
    
} else {
    return 'Riot is not activated';
}

$api = &$moc->api;

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');


$result = $moc->login();
$username = '@' . $moc->username . ':matrix.org';
$emailsender = $modx->getOption('emailsender');

if (!class_exists('MocCollectComments')) {
    class MocCollectComments {

        function __construct(modX & $modx, &$fbuch, array $options = array()) {
            $this->modx = &$modx;
            $this->fbuch = &$fbuch;
        }

        public function cancelAcceptInvite($sender, $date_object, &$found, $process, $comment) {
            $modx = &$this->modx;
            $fbuch = &$this->fbuch;
            $processmessages = array();
            $processmessages['accept'] = 'Zusage';
            $processmessages['cancel'] = 'Absage';

            $properties = array();
            $date_id = $date_object->get('id');
            $date_o = $modx->getObject('fbuchDate',$date_id);
            
            if (!in_array($sender, $found) && $name_o = $modx->getObject('mvMember', array('riot_user_id' => $sender))) {
                $name_id = $name_o->get('id');
                if ($invite_o = $modx->getObject('fbuchDateInvited', array('member_id' => $name_id, 'date_id' => $date_id))) {
                    $_REQUEST['iid'] = $invite_o->get('id');
                    $_REQUEST['process'] = $process;
                    $email = $name_o->get('email');
                    $_REQUEST['code'] = md5($date_id . $email . $_REQUEST['iid']);
                    $properties['redirect'] = '0';
                    $properties['send_riot'] = '0';
                    $fbuch->cancelAcceptInvite($properties);
                    $namelist = array();
                    $cancellist = array();
                    if ($names = $date_o->getMany('Names')) {
                        foreach ($names as $name) {
                            if ($acc_name_o = $name->getOne('Member')) {
                                $namelist[] = $acc_name_o->get('firstname') . ' ' . $acc_name_o->get('name');
                            }
                        }
                    }
               
                    if ($names = $date_o->getMany('Invited',$c)) {
                        foreach ($names as $name) {
                            if ($name->get('canceled')=='1' && $acc_name_o = $name->getOne('Member')) {
                                $cancellist[] = $acc_name_o->get('firstname') . ' ' . $acc_name_o->get('name');
                            }
                        }
                    }                    
                    $comment = array();
                   
                    $comment[] = 'Zusagen: ' . implode(',', $namelist);
                    $comment[] = 'Absagen: ' . implode(',', $cancellist);
                    
                    $comment = implode("\n",$comment);
                    $properties = array(
                        'action' => 'send',
                        'date_object' => &$date_o,
                        'name_object' => &$name_o,
                        'comment_start' => $processmessages[$process] . ' von ',
                        'comment' => $comment);
                    $modx->runSnippet('moc_hooks', $properties);

                }
                $found[] = $sender;
            }
        }

    }
}


$c = $modx->newQuery('fbuchDate');
$c->where(array('riot_room_id:!=' => ''));
$c->where(array('date:>' => strftime('%Y-%m-%d 00:00:00', strtotime('- 1 week'))));
//$c->prepare();echo $c->toSql();
if ($collection = $modx->getIterator('fbuchDate', $c)) {
    foreach ($collection as $object) {
        $room_id = $object->get('riot_room_id');
        $date_id = $object->get('id');


        $result = $api->getState($room_id);
        //echo '<pre>' . print_r($result, 1) . '</pre>';

        //get and set riot_status for each Invite
        if (isset($result['status']) && $result['status'] == 200) {
            if (isset($result['data'])) {
                $data = $result['data'];
                if (is_array($data)) {
                    foreach ($data as $row) {
                        if ($row['type'] == 'm.room.member') {
                            if (isset($row['content'])){
                                $content = $row['content'];
                                $row['membership'] = isset($content['membership']) ? $content['membership'] : '';      
                            }                            
                            //echo $row['membership'] . '<br>';
                            //echo '<pre>' . print_r($row['content'], 1) . '</pre>';                            
                            
                            
                            if ($name_o = $modx->getObject('mvMember', array('riot_user_id' => $row['state_key']))) {
                                $name_id = $name_o->get('id');
                                if ($i_object = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'member_id' => $name_id))) {
                                    $i_object->set('riot_state', $row['membership']);
                                    $i_object->save();
                                } else {
                                    //create new Invite, if member is invited or joined in riot-room, but not yet invited to fbuch_date
                                    //todo: permission needed to invite other persons?
                                    if ($row['membership'] == 'invite' || $row['membership'] == 'join') {
                                        $i_object = $modx->newObject('fbuchDateInvited');
                                        $i_object->set('date_id', $date_id);
                                        $i_object->set('member_id', $name_id);
                                        $i_object->set('riot_state', $row['membership']);
                                        $i_object->save();
                                    }
                                }
                            }


                        }
                    }

                }
            }
        }


        $last_matrix_start_token = $object->get('last_matrix_start_token');
        $params = array();
        if (!empty($last_matrix_start_token)) {
            $params['to'] = $last_matrix_start_token;
        }

        $params['dir'] = 'b';
        $params['limit'] = 100;
        $result = $api->getMessages($room_id, $params);
        echo 'Start:' . $result['data']['start'];
        echo '<br>';
        if (isset($result['data']['end'])){
            echo 'End:' . $result['data']['end'];
            echo '<br>';
        }
        $found = array();

        foreach ($result['data']['chunk'] as $chunk) {
            if ($chunk['type'] == 'm.room.message') {
                $content = $chunk['content'];
                echo '<br>';
                echo '<br>';
                echo $createdon = strftime('%Y-%m-%d %H:%M:%S', $chunk['origin_server_ts'] / 1000);
                echo '<br>';
                echo $sender = $chunk['sender'];
                echo '<br>';
                echo $riot_event_id = $chunk['event_id'];
                echo '<br>';
                if ($content['msgtype'] == 'm.text') {
                    echo $comment = $content['body'];
                    echo '<br>';

                    /*
                    $comment_parts = explode(':',$comment);
                    if ($comment_parts == '##'){
                    $firstname = isset($comment_parts[1]) ? $comment_parts[1] : '';
                    $lastname = isset($comment_parts[2]) ? $comment_parts[2] : '';
                    $email = isset($comment_parts[3]) ? $comment_parts[3] : '';
                    $all = $firstname.$lastname.$email;
                    if (!empty($all) && $name_o = $modx->getObject('fbuchNames',array('firstname:LIKE'=>$firstname,'lastname:LIKE'=>$lastname,'email:LIKE'=>$email))){
                    $name_o->set('riot_user_id',$sender);
                    $name_o->save();                            
                    }
                    }
                    */
                    if (substr($comment, 0, 3) == '++:') {
                        $sender = substr($comment, 3);
                        $comment = '';
                        $mcc = new MocCollectComments($modx, $fbuch);
                        $mcc->cancelAcceptInvite($sender, $object, $found, 'accept', $comment);

                    } elseif (substr($comment, 0, 3) == '--:') {
                        $sender = substr($comment, 3);
                        $comment = '';
                        $mcc = new MocCollectComments($modx, $fbuch);
                        $mcc->cancelAcceptInvite($sender, $object, $found, 'cancel', $comment);

                    } elseif (substr($comment, 0, 2) == '++') {
                        $comment = substr($comment, 2);
                        $mcc = new MocCollectComments($modx, $fbuch);
                        $mcc->cancelAcceptInvite($sender, $object, $found, 'accept', $comment);

                    } elseif (substr($comment, 0, 2) == '--') {
                        $comment = substr($comment, 2);
                        $mcc = new MocCollectComments($modx, $fbuch);
                        $mcc->cancelAcceptInvite($sender, $object, $found, 'cancel', $comment);

                    } elseif (substr(strtolower($comment), 0, 2) == 'xx') {
                        $comment = substr($comment, 1);
                        $mcc = new MocCollectComments($modx, $fbuch);
                        $mcc->cancelAcceptInvite($sender, $object, $found, 'cancel', $comment);

                    }                    
                    
                    

                    if (!empty($comment)) {
                        if ($comment_o = $modx->getObject('fbuchDateComment', array('date_id' => $date_id, 'riot_event_id' => $riot_event_id))) {

                        } else {
                            $comment_o = $modx->newObject('fbuchDateComment');
                            if ($name_o = $modx->getObject('mvMember', array('riot_user_id' => $sender))) {
                                $name_id = $name_o->get('id');
                                $comment_o->set('member_id', $name_id);
                            } else {
                                if ($sender != $username) {
                                    $message = "$sender \n";
                                    $message .= "Wir konnten Dich im Fahrtenbuchsystem nicht zuordnen.\n";
                                    $message .= "Dein momentaner Matrix - Benutzername ist bei uns nicht eingetragen\n";
                                    $message .= "Sende uns Deinen registrierten Matrix - Benutzernamen per Mail an $emailsender \n";
                                    $message .= "Wir tragen diesen dann ins Fahrtenbuchsystem ein, damit wir Dich in Zukunft zuordnen können.\n";
                                    $api->send($room_id, 'm.room.message', array('body' => $message, 'msgtype' => 'm.text'));
                                }

                            }
                            if ($sender != $username) {
                                $comment_o->set('riot_user_id', $sender);
                                $comment_o->set('date_id', $date_id);
                                $comment_o->set('riot_event_id', $riot_event_id);
                                $comment_o->set('createdon', $createdon);
                                $comment_o->set('comment', $comment);
                                $comment_o->save();
                            }
                        }
                    }

                }

                echo '<pre>' . print_r($chunk, 1) . '</pre>';


            }
        }

        echo '<pre>' . print_r($result, 1) . '</pre>';

        $object->set('last_matrix_start_token', $result['data']['start']);
        $object->save();

    }
}