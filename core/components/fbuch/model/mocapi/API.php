<?php

/**
 * Copyright (c) 2016 Cooperativa EITA (eita.org.br)
 * @author Vinicius Cubas Brand <vinicius@eita.org.br>
 * This file is licensed under the Licensed under the MIT license:
 * http://opensource.org/licenses/MIT
 */

/**
 * Class MatrixOrg_API
 *
 * Implements the Client-Server communication, according to the API
 *
 *
 * @package MatrixOrg
 */
class MatrixOrg_API {

    private $home_server;
    private $access_token = null;

    private $request_timeout = 30000;

    private $return_assoc_array = true;

    private $user_id = null;

    //Set user and password values for auto-login if we detect that the token expired
    private $al_username = null;
    private $al_password = null;

    /**
     * @var - (bool) if connection could happen with current home_server, user, auth
     */
    public $could_connect = null;

    public function __construct($home_server, $access_token = null) {
        $this->home_server = $home_server;
        $this->access_token = $access_token;
    }

    /**
     * Username to be used to retry login when the access token is detected to be invalid
     * @param null $al_username
     */
    public function setAutoLoginUsername($al_username) {
        $this->al_username = $al_username;
    }

    /**
     * Password to be used to retry login when the access token is detected to be invalid
     * @param null $al_password
     */
    public function setAutoLoginPassword($al_password) {
        $this->al_password = $al_password;
    }

    #internal use only inside $this->doRequest()
    private $_request_relogin = false;

    /**
     * @param $method       - HTTP method
     * @param $relative_url - Url to reach in the homeserver, without the
     *                        servername and the '_matrix/' part
     * @param $params       - Request parameters
     * @param $use_access_token - True for methods that need auth
     */
    public function doRequest($relative_url, $params = array(), $method = 'GET', $use_access_token = true, $file = null, $json_force_object = true) {
        $url = $this->home_server . '/_matrix/' . $relative_url;

        //$this->access_token = 'MDAxOGxvY2F0aW9uIG1hdHJpeC5vcmcKMDAxM2lkZW50aWZpZXIga2V5CjAwMTBjaWQgZ2VuID0gMQowMDI1Y2lkIHVzZXJfaWQgPSBAQnJ1bm9QOm1hdHJpeC5vcmcKMDAxNmNpZCB0eXBlID0gYWNjZXNzCjAwMjFjaWQgbm9uY2UgPSBjdVBjX35VTmlYKi1lc1NACjAwMmZzaWduYXR1cmUgDhDub9G9FVYhe2gEwj6jvwnxNmdmL4aqGasRBlmIPuEK';

        $get_params = array();

        if ($file) {
            $http_headers = array('Accept: application/json', 'Content-type: ' . mime_content_type($file));
        } else {
            $http_headers = array('Accept: application/json', 'Content-type: application/json');
        }

        if ($use_access_token) {
            $get_params['access_token'] = $this->access_token;
        }

        if ($file) {
            $get_params['filename'] = $params['filename'];
        }

        if ($method == 'GET') {
            $get_params = array_merge($get_params, $params);
        }

        if (count($get_params) > 0) {
            $url .= '?' . http_build_query($get_params);
        }

        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Nextcloud');

        //FIXME set proper ssl verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':

                if ($file) {
                    #curl_setopt($ch,CURLOPT_VERBOSE,true);
                    #$flog = fopen('/tmp/curllog','a');
                    #fseek($flog,0,SEEK_END);
                    #curl_setopt($ch,CURLOPT_STDERR,$flog);

                    curl_setopt($ch, CURLOPT_POST, true);

                    $mime = mime_content_type($file);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file));


                    curl_setopt($ch, CURLOPT_HEADER, false);

                } else {
                    curl_setopt($ch, CURLOPT_POST, true);
                    if ($json_force_object){
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_FORCE_OBJECT));
                    } else {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                    }
                    
                }

                break;

            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                $params = is_array($params) ? $params : [];
                if ($json_force_object || count($params)<1){
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_FORCE_OBJECT));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                }                
                break;

            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, JSON_FORCE_OBJECT));
                break;

            default:
                //throw new Exception("MatrixOrg: invalid method to do a request.");
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
        #echo "BEFORE CURL_EXEC";
        $result = curl_exec($ch);

        #fclose($flog);
        #echo "AFTER CURL_EXEC";

        if ($result === false) {
            $errno = curl_errno($ch);

            switch ($errno) {
                case CURLE_OPERATION_TIMEOUTED:
                    //throw new \MatrixOrg_Exception_Timeout(curl_error($ch));
                default:
                    //throw new \MatrixOrg_Exception_NetworkError(curl_error($ch));
            }
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $json_result = json_decode($result, $this->return_assoc_array);

        //Try to relogin ONCE if access token expired, and retry request.
        if ($httpcode == 401 and $json_result['errcode'] == "M_UNKNOWN_TOKEN" and $this->al_username and $this->al_password and !$this->_request_relogin) {
            $login_res = $this->login($this->al_username, $this->al_password, true);
            $this->_request_relogin = true;
            $result2 = $this->doRequest($relative_url, $params, $method, $use_access_token, $file);
            $this->_request_relogin = false;
            $result2['new_login_done'] = true;
            $result2['login_res'] = $login_res;
            $result2['pre_login'] = array(
                'url' => $url,
                'params' => $params,
                'status' => $httpcode,
                'data' => $json_result);


            return $result2;
        }


        return array(
            'url' => $url,
            'params' => $params,
            'status' => $httpcode,
            'data' => $json_result);

    }

    //TODO tentar conexão persistente HTTP 1.1
    public function login($username, $password, $redo_login = false) {
        if ($this->access_token != null and !$redo_login)
            return;

        $fields = array(
            'type' => 'm.login.password',
            'user' => $username,
            'password' => $password);

        $result = $this->doRequest('client/r0/login', $fields, 'POST', false);

        $this->access_token = ($result['status'] == 200) ? $result['data']['access_token'] : null;
        $this->user_id = ($result['status'] == 200) ? $result['data']['user_id'] : null;


        if ($this->access_token == null) {
            //throw new \MatrixOrg_Exception_Connection("Matrix.org login: could not connect.");
        }
        return $result;
    }
    
    public function logout() {

        $result = $this->doRequest('client/r0/logout', array(), 'POST', true);

        return $result;
    }    

    /**
     * Returns true if there is a connection with Matrix.org server
     *
     * As Matrix.org server API is a WebService, there is not a persistent
     * connection to the server. Instead we simulate it by knowing if the last
     * login attempt was successful or not.
     *
     * @return bool true if the last login tentative was successful
     */
    public function connected() {
        return $this->access_token != null;
    }

    public function sync($since = null, $filter = 0) {
        $fields = array('filter' => $filter, 'timeout' => $this->request_timeout);

        if ($since != null)
            $fields['since'] = $since;

        return $this->doRequest('client/r0/sync', $fields, 'GET', true);
    }

    /**
     * @return mixed
     */
    public function getAccessToken() {
        return $this->access_token;
    }

    /**
     * @param mixed $access_token
     */
    public function setAccessToken($access_token) {
        $this->access_token = $access_token;
    }

    /**
     * @return mixed
     */
    public function getHomeServer() {
        return $this->home_server;
    }

    /**
     * Converts a mxc:// address to a file to a download address
     * @param $mxcAddress
     */
    public function downloadUrl($mxcAddress) {
        if (preg_match('/^mxc:\/\//', $mxcAddress)) {
            return $this->home_server . '/_matrix/media/v1/download/' . preg_replace('/^mxc:\/\//', "", $mxcAddress);
        }
        return "";
    }

    /**
     * @param $file filename in current filesystem
     * @param $filename filename in matrix
     */
    public function upload($file, $filename) {
        return $this->doRequest('media/v1/upload', array('filename' => $filename), 'POST', true, $file);
    }

    /**************************************************************************
    * User Methods                                                           *
    **************************************************************************/

    /**
     * Gets the user id of the logged-in user
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * @param $params (must follow spec in http://matrix.org/docs/spec/client_server/r0.2.0.html#id122)
     * @param $user_id (optional) If not provided, will use the logged in user_id
     */
    public function createFilter($params, $user_id = null) {
        if ($user_id == null) {
            $user_id = $this->user_id;
        }
        return $this->doRequest('client/r0/user/' . urlencode($user_id) . '/filter', $params, 'POST', true);
    }

    /**
     * @param $params (must follow spec in http://matrix.org/docs/spec/client_server/r0.2.0.html#id121)
     * @param $user_id (optional) If not provided, will use the logged in user_id
     */
    public function queryFilter($filter_id, $user_id = null) {
        if ($user_id == null) {
            $user_id = $this->user_id;
        }
        return $this->doRequest('client/r0/user/' . urlencode($user_id) . '/filter/' . urlencode($filter_id), array(), 'GET', true);
    }

    /**************************************************************************
    * Presence Methods                                                       *
    **************************************************************************/

    /**
     * @param $user_id (optional) If not provided, will use the logged in user_id
     */
    public function presenceStatus($user_id = null) {
        if ($user_id == null) {
            $user_id = $this->user_id;
        }
        return $this->doRequest('client/r0/presence/' . urlencode($user_id) . '/status', array(), 'GET', true);
    }


    /**************************************************************************
    * Room Methods                                                           *
    **************************************************************************/
    public function getMessages($room_id, $fields = array()) {
        $fields['dir'] = isset($fields['dir'])? $fields['dir'] : 'b';
        $fields['limit'] = isset($fields['limit'])? $fields['limit'] : 20;
        if (isset($fields['filter'])) {
            $fields['filter'] = json_encode($fields['filter'], JSON_FORCE_OBJECT);
        }
        
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/messages', $fields, 'GET', true);
    }
    
    public function getMembers($room_id, $fields = array()) {
        
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/members', array(), 'GET', true);
    }
    
    public function getHierarchy($room_id, $fields = array()) {
        $fields['suggested_only'] = isset($fields['suggested_only'])? $fields['suggested_only'] : 'false';
        $fields['limit'] = isset($fields['limit'])? $fields['limit'] : 20;        
        return $this->doRequest('client/v1/rooms/' . urlencode($room_id) . '/hierarchy', $fields, 'GET', true);
    }     
    
    public function getJoinedMembers($room_id) {
        
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/joined_members', array(), 'GET', true);
    }    
    
    public function getState($room_id,$event_type='',$state_key='') {
        
        $event_type = !empty($event_type) ? '/' . $event_type : '';
        $state_key = !empty($state_key) ? '/' . urlencode($state_key) : '';      
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/state' . $event_type . $state_key, array(), 'GET', true);
    }    

    public function createRoom($fields = array()) {
        //$fields['preset'] = isset($fields['preset']) ? $fields['preset'] : 'private_chat';
        if (isset($fields['room_alias_name'])) {
            $fields['room_alias_name'] = $fields['room_alias_name'];
        }
        $fields['name'] = isset($fields['name']) ? $fields['name'] : '';
        $fields['topic'] = isset($fields['topic']) ? $fields['topic'] : '';

        return $this->doRequest('client/r0/createRoom', $fields, 'POST', true, null, false);
    }
    
    public function updateRoom($room_id,$event_type = 'm.room.name',$fields = array()) {

        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/state/' . $event_type, $fields, 'PUT', true);
    }

    public function kickUserFromRoom($room_id,$fields = array()) {
        $fields['user_id'] = isset($fields['user_id']) ? $fields['user_id'] : '';
        $fields['reason'] = isset($fields['reason']) ? $fields['reason'] : '';
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/kick' , $fields, 'POST', true);
    }    

    public function putRoomToSpace($space_id,$room_id,$fields = array()) {
        //remove from space with empty $fields
        return $this->doRequest('client/r0/rooms/' . urlencode($space_id) . '/state/m.space.child/' . urlencode($room_id), $fields, 'PUT', true, null, false);
    } 
    public function putSpaceToRoom($space_id,$room_id,$fields = array()) {
        //remove space from room with empty $fields
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/state/m.space.parent/' . urlencode($space_id), $fields, 'PUT', true, null, false);
    }           

    public function addRoomToGroup($room_id,$group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/admin/rooms/' . urlencode($room_id) , $fields, 'PUT', true);
    }

    public function deleteRoomFromGroup($room_id,$group,$params = array()) {
        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/admin/rooms/' . urlencode($room_id), $params, 'DELETE', true);
    }

    public function getGroupRooms($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/rooms' , $fields, 'GET', true);
    }

    public function getGroupRoles($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/roles/' , $fields, 'GET', true);
    }

    public function getGroupUsers($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/users' , $fields, 'GET', true);
    }

    public function getGroupInvitedUsers($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/invited_users' , $fields, 'GET', true);
    }

    public function getGroupProfile($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/profile' , $fields, 'GET', true);
    }

    public function getGroupSummary($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/summary' , $fields, 'GET', true);
    }

    public function getGroupCategories($group,$fields = array()) {

        return $this->doRequest('client/r0/groups/' . urlencode($group) . '/categories/' , $fields, 'GET', true);
    }

    public function invite($room_id, $fields = array()) {

        if (isset($fields['address'])) {
            $fields['address'] = $fields['address'];
            $fields['id_server'] = isset($fields['id_server']) ? $fields['id_server'] : 'matrix.org';
            $fields['medium'] = isset($fields['medium']) ? $fields['medium'] : 'email';
        }

        if (isset($fields['user_id'])) {
            $fields['user_id'] = $fields['user_id'];
        }

        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/invite', $fields, 'POST', true);
    }


    public function redact($room_id, $event_id) {
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/redact/' . urlencode($event_id), array(), 'POST', true);
    }

    public function send($room_id, $event_type, $params = array(), $txn_id = null) {
        $txn_id = 'm' . round(microtime(true) * 1000) . ".0";

        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/send/' . $event_type . '/' . $txn_id, $params, 'PUT', true);
    }


    public function setPusher() {

        $fields = array();
        $fields['lang'] = 'en';
        $fields['kind'] = 'http';
        $fields['app_display_name'] = 'fbuch';
        $fields['device_display_name'] = 'Fbuch';
        $fields['profile_tag'] = 'xxyyzz';
        $fields['app_id'] = 'de.fbuch.app.php';
        $fields['pushkey'] = 'fbuchbHPRgkF3JUikC4ENAHEeMrd41Zxv3hVZjC9KtT8OvPVGJ-hQMRKRrZuJAEcl7B338qju59zJMjw2DELjzEvxwYv7hH5Ynpc1ODQ0aT4U4OFEeco8ohsN5PjL1iC2dNtk2BAokeMCg2ZXKqpc8FXKmhX94kIxQ';
        $fields['append'] = false;
        $fields['data'] = array('url' => 'https://www.fbuch.rgmarktheidenfeld.de/ajax/getmatrixnotifications.html');


        return $this->doRequest('client/r0/pushers/set', $fields, 'POST', true);
    }

    public function getPushers() {
        return $this->doRequest('client/r0/pushers', array(), 'GET', true);
    }
    
    public function getPushRules() {
        
        return $this->doRequest('client/r0/pushrules/', array(), 'GET', true);
    }
    
    
    public function setPushRule() {
        
        
        
/*        
{
     "rule_id": ".m.rule.message",
     "default": true,
     "enabled": true,
     "conditions": [
         {
             "kind": "event_match",
             "key": "type",
             "pattern": "m.room.message"
         }
     ],
     "actions": [
         "notify",
         {
             "set_tweak": "highlight",
             "value": false
         }
     ]
} 
*/       
        
    }
    
    


    /**************************************************************************
    * State Methods                                                           *
    **************************************************************************/

    public function setTopic($room_id, $topic) {
        return $this->state($room_id, "m.room.topic", array("topic" => $topic));
    }

    private function state($room_id, $event_type, $params = array()) {
        return $this->doRequest('client/r0/rooms/' . urlencode($room_id) . '/state/' . $event_type, $params, 'PUT', true);
    }

    /**************************************************************************
    * Message Methods                                                        *
    **************************************************************************/


    public function sendFile($room_id, $file, $filename, $file_thumbnail = null) {

        $final_result = array();

        $file_info = array('size' => filesize($file), 'mimetype' => mime_content_type($file));

        $mime_groups = explode('/', $file_info['mimetype']);
        $mime_group = $mime_groups[0];

        $file_type = (in_array($mime_group, array(
            'video',
            'audio',
            'image'))) ? 'm.' . $mime_group : 'm.file';

        if ($mime_group == 'image') {
            list($width, $height) = @getimagesize($file);
            $file_info['w'] = $width;
            $file_info['h'] = $height;

            if ($file_thumbnail) {
                $thumb_up_res = $this->upload($file_thumbnail, "undefined");
                list($thumb_w, $thumb_h) = @getimagesize($file_thumbnail);
                $thumb_file_info = array(
                    'w' => $thumb_w,
                    'h' => $thumb_h,
                    'mimetype' => mime_content_type($file_thumbnail),
                    'size' => filesize($file_thumbnail));

                $file_info['thumbnail_info'] = $thumb_file_info;
                $file_info['thumbnail_url'] = $thumb_up_res['data']['content_uri'];

                $final_result['thumb_upload_request_result'] = $thumb_up_res;

                $final_result['status'] = $thumb_up_res['status'];
                $final_result['new_login_done'] = $thumb_up_res['new_login_done'];
            }
        }

        $result = $this->upload($file, $filename);

        $final_result['file_upload_request_result'] = $result;
        $final_result['status'] = $result['status'] != 200 ? $result['status'] : $final_result['status'];
        $final_result['new_login_done'] = $result['new_login_done'] or $final_result['new_login_done'];

        if ($result['status'] == 200) {

            $final_result['url'] = $result['data']['content_uri'];

            $final_result = array_merge($final_result, $file_info);

            $final_result['msgtype'] = $file_type;

            $params = array(
                "body" => $filename,
                "info" => $file_info,
                "msgtype" => $file_type,
                "url" => $result['data']['content_uri']);

            $result = $this->send($room_id, 'm.room.message', $params);

            $final_result['3rd_request_result'] = $result;

            if ($result['status'] == 200) {
                $final_result['event_id'] = $result['data']['event_id'];
            }

            $final_result['status'] = $result['status'] != 200 ? $result['status'] : $final_result['status'];
            $final_result['new_login_done'] = $result['new_login_done'] or $final_result['new_login_done'];
        }

        return $final_result;
    }
    
    public function getDevices() {
        return $this->doRequest('client/unstable/devices', array(), 'GET', true);
    } 
    public function deleteDevices($device_id,$params = array()) {
        return $this->doRequest('client/unstable/devices/' . urlencode($device_id), $params, 'DELETE', true);
    }


}
