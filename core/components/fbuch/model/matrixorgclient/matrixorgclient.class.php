<?php

/**
* MatrixOrgClient
*
* Copyright 2017 by Bruno Perner <b.perner@gmx.de>
*
* @package matrixorgclient
* @subpackage classfile
*/

class MatrixOrgClient {
    /**
    * A reference to the modX instance
    * @var modX $modx
    */
    public $modx;

    /**
    * The namespace
    * @var string $namespace
    */
    public $namespace = 'matrixorgclient';

    /**
    * The class options
    * @var array $options
    */
    public $options = array();


    /**
    * MatrixOrgClient constructor
    *
    * @param modX $modx A reference to the modX instance.
    * @param array $options An array of options. Optional.
    */
    function __construct(modX & $modx, array $options = array()) {
        $this->modx = & $modx;

        $this->modx->lexicon->load('matrixorgclient:default');

        //$corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/fbuch/');
        $mocCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/fbuch/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/fbuch/');

        // Load some default paths for easier management
        $this->options = array_merge(array('namespace' => $this->namespace, 'assetsPath' => $assetsPath, 'assetsUrl' => $assetsUrl, 'cssUrl' => $assetsUrl . 'css/', 'jsUrl' => $assetsUrl . 'js/', 'imagesUrl' => $assetsUrl . 'images/', 'corePath' => $corePath, 'modelPath' => $corePath . 'model/', 'chunksPath' => $corePath . 'elements/chunks/', 'pagesPath' => $corePath . 'elements/pages/', 'snippetsPath' => $corePath . 'elements/snippets/', 'pluginsPath' => $corePath . 'elements/plugins/', 'processorsPath' => $corePath . 'processors/', 'templatesPath' => $corePath . 'templates/', 'cachePath' => $this->modx->getOption('core_path') . 'cache/', 'connectorUrl' => $assetsUrl . 'connector.php'), $options);

        include $this->modx->getOption('core_path') . 'config/matrixorgclient.config.inc.php';
        $this->riot_isactive = isset($riot_isactive) ?  $riot_isactive : false;
        $this->username = $username;
        $this->password = $password;
        $this->comingdates_space_id = $comingdates_space_id;
        $this->pastdates_space_id = $pastdates_space_id;
        $this->results = [];
        //$this->groups = $groups;

        include $mocCorePath . 'model/mocapi/API.php';
        $this->api = new MatrixOrg_API('https://matrix.org');


        //$this->modx->addPackage('fbuch', $this->getOption('modelPath'));
    }

    /**
    * Get a local configuration option or a namespaced system setting by key.
    *
    * @param string $key The option key to search for.
    * @param array $options An array of options that override local options.
    * @param mixed $default The default value returned, if the option is not found locally or as a namespaced system setting.
    * @param bool $skipEmpty If true: use default value if option value is empty.
    * @return mixed The option value or the default value specified.
    */
    public function getOption($key, $options = array(), $default
        = null, $skipEmpty = false ) {
            $option = $default
            ;
            if (!empty($key) && is_string($key)) {
                if ($options !== null && array_key_exists($key, $options) && !($skipEmpty && empty($options[$key]))) {
                    $option = $options[$key];
                }
                elseif (array_key_exists($key, $this->options) && !($skipEmpty && empty($options[$key]))) {
                    $option = $this->options[$key];
                }
                elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                    $option = $this->modx->getOption("{$this->namespace}.{$key}", null, $default
                        , $skipEmpty );
                }
            }
            return $option;
    }

    public function login($group = array()) {
        
        $modx = & $this->modx;
        $tokens = [];
        $access_token = '';
        $token_ok = false;
        $password = $this->password;
        $username = $this->username;
        $result = [];
        if (isset($group['password']) && isset($group['username'])) {
            $password = $group['password'];
            $username = $group['username'];
        }
        $file_name = $modx->getOption('core_path') . 'config/moc_access_token.json';
        if ($chunk = file_get_contents($file_name)) {
            $tokens = json_decode($chunk,true);
            $access_token = isset($tokens[$username]) ? $tokens[$username] : '';
        }
        if (!empty($access_token)) {
            //test, if access_token is still working
            $this->api->setAccessToken($access_token);
            $this->api->setAutoLoginUsername($username);
            $this->api->setAutoLoginPassword($password);
            $result = $this->api->presenceStatus('@' . $username . ':matrix.org');
            $token_ok = ($result['status'] == 200) ? true : false;
            $this->results['token_ok'] = $result;
            /*
            if ($chunk = $modx->getObject('modChunk', array('name' => 'moc_debug'))) {
                $chunk->set('content', print_r($result, 1));
                $chunk->save();
            }
            */
        }
        if (!$token_ok) {
            $result = $this->api->login($username, $password, true);
            //echo '<pre>'. $username . $password . print_r($result,1) . '</pre>';
            $tokens[$username] = ($result['status'] == 200) ? $result['data']['access_token'] : '';
            $this->results['login'] = $result;
        }

        //store access_token
        file_put_contents($file_name,json_encode($tokens,JSON_PRETTY_PRINT));

        return $result;
    }

    public function prepareRoomFields($fields = []){
        return 
        [
            "name"=> $fields['name'],
            "preset"=> "private_chat",
            "visibility"=> "private",
            "initial_state"=> [
              ["type" => "m.room.guest_access",
                "state_key"=> "",
                "content"=> [
                  "guest_access"=> "forbidden"
                ]
              ]/*,
              [
                "type"=> "m.space.parent",
                "content"=> [
                  "via"=> [
                    "matrix.org"
                  ],
                  "canonical"=> true
                ],
                "state_key"=> $this->comingdates_space_id
              ],
              [
                "type"=> "m.room.join_rules",
                "content"=> [
                  "join_rule"=> "restricted",
                  "allow"=> [
                    [
                      "type"=> "m.room_membership",
                      "room_id"=> $this->comingdates_space_id
                    ]
                  ]
                ]
              ]*/,
              [
                "type"=> "m.room.history_visibility",
                "content"=> [
                  "history_visibility"=> "shared"
                ]
              ]
            ],
            "room_version"=> "9"
        ];       
    }

    public function kickUsersFromRoom($date_object){
        $modx = & $this->modx;
        $result = $this->login();
        $room_id = $date_object->get('riot_room_id');
        $result = $this->api->getJoinedMembers($room_id);
        
        if (isset($result['status']) && isset($result['data']) && isset($result['data']['joined']) && $result['status'] == 200) {
            $this->results['kickUsersFromRoom']['status'] = '200'; 
            $this->results['kickUsersFromRoom']['results'] = []; 
            $username = '@' . $this->username . ':matrix.org';
            echo $username . '<br><br>';
            foreach ($result['data']['joined'] as $key => $value) {
                echo '<pre>' . print_r($key,1) . '</pre>'; 
                if ($username != $key){
                    $subresult = $this->api->kickUserFromRoom($room_id,['user_id'=>$key,'reason'=>'Termin liegt in der Vergangenheit']);
                    $this->results['kickUsersFromRoom']['results'][] = $subresult;
                    if (isset($subresult['status']) && $result['status'] == 200) {
    
                    }  else {
                        $this->results['kickUsersFromRoom']['status'] = '';     
                    }                     
                }
                               
            }
            
        }  

        return $this->results;
                
    }

    public function checkComingOrPastSpace($date_object) {
        $modx = & $this->modx;
        $result = $this->login();
        //echo '<pre>' . print_r($date_object->toArray(),1) . '</pre>';
        $room_id = $date_object->get('riot_room_id');

        $fields = array();
        $date =  strtotime($date_object->get('date'));
        $now = strtotime('- 1 day');
        
        if ($date > $now){
            $room_name = $this->generateRoomName($date_object);
            $result = $this->api->updateRoom($room_id, 'm.room.name', array('name' => $room_name));
            $this->results['updateRoom'] = $result;

            $space_name = $this->comingdates_space_id;
            $space_id = '1';
            $result = $this->api->putRoomToSpace($this->pastdates_space_id,$room_id);
            $this->results['removeRoomFromSpace'] = $result;               
        } else {
            $space_name = $this->pastdates_space_id;
            $space_id = '2';
            $result = $this->api->putRoomToSpace($this->comingdates_space_id,$room_id);
            $this->results['removeRoomFromSpace'] = $result;         
        }
        $fields = [
            "via"=> [
              "matrix.org"
            ],
            "suggested"=> false
        ];
        $result = $this->api->putRoomToSpace($space_name,$room_id,$fields);
        if (isset($result['status']) && $result['status'] == 200) {
            $date_object->set('matrix_space', $space_id);
            $date_object->save();        
        }        
        $this->results['putRoomToSpace'] = $result; 
                
        return $this->results;
    } 
    
    public function generateRoomName($date_object){
        $modx = $this->modx;
        $modx->runSnippet('setlocale');
        return strftime('%a %d.%m.%Y', strtotime($date_object->get('date'))) . ' ' . $date_object->get('start_time') . ' ' . $date_object->get('title');
    }

    public function createDateRoom($date_object) {
        $modx = & $this->modx;

        $riot_room_id = $date_object->get('riot_room_id');
        $fields = array();
           
        $room_name = $this->generateRoomName($date_object);

        $url = $modx->makeUrl('87','fbuch','date_id=' . $date_object->get('id') . '&riot=1&code=' . md5($date_object->get('id')),'full');
        
        $topic = 'Zusagen mit ++ Absagen mit xx Zur Terminübersicht:' . $url ;
        $guest_access = array('guest_access'=>'forbidden');
        $join_rules = array('join_rule'=>'public');

        $room_fields = $this->prepareRoomFields();
        //$room_fields = array();
        $room_fields['name'] = $room_name;
        $room_fields['topic'] = $topic;

        if (empty($riot_room_id)) {
            $result = $this->login();
            //$result = $this->api->createRoom($fields);
            $result = $this->api->createRoom($room_fields);
            $this->results['createRoom'] = $result;
            /*
			print_r($fields);
            print_r($result);
            die();
            */
            if (isset($result['status']) && $result['status'] == 200) {
                if (isset($result['data'])) {
                    $data = $result['data'];
                    if (isset($data['room_id'])) {
                        $room_id = $data['room_id'];
                        //$result = $api->invite($room_id, array('address' => 'b.perner@gmx.de'));

                        $date_object->set('riot_room_id', $room_id);
                        $date_object->save();
                    }
                    /*
                    $result = $this->login($group);
                    $fields = array();
                    $fields ['m.visibility'] = array('type' => 'private');
                    $result = $this->api->addRoomToGroup($room_id, $group['group'], $fields);
                    $results['addRoomToGroup'] = $result;
                    */
                    $this->checkComingOrPastSpace($date_object);
                }
            }
        }
        else {
            $result = $this->login();
            $result = $this->api->updateRoom($riot_room_id, 'm.room.name', array('name' => $room_name));
            $result = $this->api->updateRoom($riot_room_id, 'm.room.topic', array('topic' => $topic));
            //$result = $this->api->updateRoom($riot_room_id, 'm.room.guest_access', $guest_access);
            //$result = $this->api->updateRoom($riot_room_id, 'm.room.join_rules', $join_rules);
            $this->results['updateRoom'] = $result;
            $this->checkComingOrPastSpace($date_object);            
            /*
            //login as the group-admin
            $result = $this->login($group);
            $fields = array('m.visibility' => array('type' => 'private'));
            $result = $this->api->addRoomToGroup($riot_room_id, $group['group'], $fields);
            $results['addRoomToGroup'] = $result;
            */
            //echo '<pre>' . print_r($result, 1) . '</pre>';
            //die();
            
        }
        return $this->results;
    }

    public function getGroupRooms() {
        $groups = $this->groups;
        $group = $groups['vereins_mitglieder'];
        $result = $this->login($group);
        $result = $this->api->getGroupRooms($group['group']);
        $room_ids = array();
        if (isset($result['data']['chunk'])) {
            $rooms = $result['data']['chunk'];
            if (is_array($rooms)) {
                foreach ($rooms as $room) {
                    $room_ids[] = $room['room_id'];
                }
            }
        }
        return $room_ids;
    }

    public function cleanOldDate(& $object) {
        $groups = $this->groups;
        $group = $groups['vereins_mitglieder'];
        $room_id = $object->get('riot_room_id');
        $result = $this->login($group);
        $result = $this->api->deleteRoomFromGroup($room_id, $group['group']);
        //print_r($result);
    }

}
