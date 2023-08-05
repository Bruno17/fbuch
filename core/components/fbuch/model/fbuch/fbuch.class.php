<?php

/**
 * Fbuch
 *
 * Copyright 2017 by Bruno Perner <b.perner@gmx.de>
 *
 * @package fbuch
 * @subpackage classfile
 */

class Fbuch {
    /**
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;

    /**
     * The namespace
     * @var string $namespace
     */
    public $namespace = 'fbuch';

    /**
     * The class options
     * @var array $options
     */
    public $options = array();

    public $authenticated = false;


    /**
     * Fbuch constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    function __construct(modX & $modx, array $options = array()) {
        $this->modx = &$modx;
        $mocCorePath = realpath($modx->getOption('matrixorgclient.core_path', null, $modx->getOption('core_path') . 'components/matrixorgclient')) . '/';
        $this->moc = $modx->getService('matrixorgclient', 'MatrixOrgClient', $mocCorePath . 'model/matrixorgclient/');

        $this->modx->lexicon->load('fbuch:default');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path') . 'components/fbuch/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path') . 'components/fbuch/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url') . 'components/fbuch/');

        // Load some default paths for easier management
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'pagesPath' => $corePath . 'elements/pages/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'pluginsPath' => $corePath . 'elements/plugins/',
            'processorsPath' => $corePath . 'processors/',
            'templatesPath' => $corePath . 'templates/',
            'cachePath' => $this->modx->getOption('core_path') . 'cache/',
            'connectorUrl' => $assetsUrl . 'connector.php'), $options);

        $this->modx->addPackage('fbuch', $this->getOption('modelPath'));
        //$this->getClientConfigOptions();
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
    public function getOption($key, $options = array(), $default = null, $skipEmpty = false) {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options !== null && array_key_exists($key, $options) && !($skipEmpty && empty($options[$key]))) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options) && !($skipEmpty && empty($options[$key]))) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}", null, $default, $skipEmpty);
            }
        }
        return $option;
    }
/*
    public function getClientConfigOptions() {
        $modx = &$this->modx;
        $path = $modx->getOption('clientconfig.core_path', null, $modx->getOption('core_path') . 'components/clientconfig/');
        $path .= 'model/clientconfig/';
        $clientConfig = $modx->getService('clientconfig', 'ClientConfig', $path);

        // If we got the class (gotta be careful of failed migrations), grab settings and go! 
        if ($clientConfig instanceof ClientConfig) {
            $contextKey = $modx->context instanceof modContext ? $modx->context->get('key') : 'web';
            $settings = $clientConfig->getSettings($contextKey);

            //print_r($settings);die();

            // Make settings available as [[++tags]] 
            $modx->setPlaceholders($settings, '+');

            // Make settings available for $modx->getOption() 
            foreach ($settings as $key => $value) {
                $modx->setOption($key, $value);
            }
        }
    }
*/

    public function checkPermission($permission, $properties = array()) {
        $modx = &$this->modx;
        $object_id = $this->modx->getOption('object_id', $properties);
        $classname = $this->modx->getOption('classname', $properties);
        $result = true;
        switch ($permission) {
            case 'fbuch_edit_old_entries':
                if (!$this->modx->hasPermission($permission)) {
                    if (!empty($object_id) && $object = $this->modx->getObject($classname, $object_id)) {
                        $finished = $object->get('finished');
                        $now = new DateTime(null, new DateTimeZone('Europe/Berlin'));
                        $today = date_format($now,'Y-m-d');
                        $date = $object->get('date_end');
                        $finishedon = $object->get('finishedon');
                        if ($finishedon != null) {
                            $date = $finishedon;
                        }
                        $date = substr($date,0,10);
                        if (!empty($finished) && $date < $today) {
                            $this->error('Du bist nicht berechtigt, abgeschlossene Einträge aus der Vergangenheit zu bearbeiten');
                            $result = false;
                        }
                    }
                }
                break;
            case 'fbuch_edit_names':
                if (!$this->modx->hasPermission($permission)) {
                    $this->error('Du hast keine Berechtigung für diesen Vorgang');
                    $result = false;
                }
                if ($modx->user->get('username') == 'rgm') {
                    $result = false;
                }
                break;
            default:
                if (!$this->modx->hasPermission($permission)) {
                    $this->error($permission . ' Du hast keine Berechtigung für diesen Vorgang');
                    $result = false;
                }
        }
        return $result;
    }

    public function error($error) {
        $this->modx->setPlaceholder('fbucherror', '<div class="col-sm-12"><div class="alert alert-danger" role="alert">' . $error . '</div></div>');
    }

    public function getCurrentFbuchMember() {
        $user_id = $this->modx->user->get('id');
        //try to get fbuch Member by logged in MODX User
        $fbuchMember = $this->modx->getObject('mvMember', array('modx_user_id' => $user_id));    

        return $fbuchMember;
    }    

    public function lockFahrt() {
        $modx = &$this->modx;
        $fahrt_id = $modx->getOption('fahrt_id', $_REQUEST, '');
        $lockaction = $modx->getOption('lockaction', $_REQUEST, '');
        $classname = 'fbuchFahrt';
        if ($object = $modx->getObject($classname, $fahrt_id)) {
            $locked = $lockaction == 'lock' ? 1 : 0;
            $object->set('locked', $locked);
            $object->save();
        }
    }

    public function getNameRiotUserId($object) {
        $user_id = '';
        if ($object) {
            $user_id = $object->get('riot_user_id');
            //try to get email from MV
        }
        return $user_id;
    }

    public function getNameEmail($object) {
        $email = '';
        if ($object) {
            $email = $object->get('email');
            //try to get email from MV
            /*
            $member_id = $object->get('mv_member_id');
            if ($member = $this->modx->getObject('mvMember', $member_id)) {
            $email = $member->get('email');
            }
            */
        }
        return $email;
    }

    public function cancelInvite($date_id,$member_id){
        if (!empty($date_id) && !empty($member_id)){ 
            if ($object = $this->modx->getObject('fbuchDateInvited', ['date_id'=>$date_id,'member_Id'=>$member_id])){
                $object->set('canceled',1); 
                $object->save();   
            }
        }
    }
    public function uncancelInvite($date_id,$member_id){
        if (!empty($date_id) && !empty($member_id)){ 
            if ($object = $this->modx->getObject('fbuchDateInvited', ['date_id'=>$date_id,'member_Id'=>$member_id])){
                $object->set('canceled',0);
                $object->save();     
            }
        }
    }    

    public function removePersonFromDate($datename_id){
        $current_member_id = 0;
        if ($member = $this->getCurrentFbuchMember()){
            $current_member_id = $member->get('id');    
        }
        if ($datename_o = $this->modx->getObject('fbuchDateNames', $datename_id)) {
            $member_id = $datename_o->get('member_id');
            $date_id = $datename_o->get('date_id');
            $name = $datename_o->get('guestname');
            if ($member = $datename_o->getOne('Member')){
                $name = $member->get('firstname') . ' ' . $member->get('name');
            }
            $datename_o->remove();
            $comment = 'Abmeldung: ' . $name;
            $this->addDateComment($comment,$date_id,$current_member_id);
            //$this->sendElementMessage($comment,$date_id,$current_member_id);                         
        }   
    }

    public function addPersonsToDate($properties) {
        $modx = &$this->modx;
        $date_id = $modx->getOption('date_id', $properties, '');
        $redirect = $modx->getOption('redirect', $properties, 0);
        $current_member_id = $modx->getOption('current_member_id', $properties, '');
        $persons = $modx->getOption('person', $properties, '');
        $guestnames = $modx->getOption('guestname', $properties, '');
        $removepersons = $modx->getOption('remove_person', $properties, '');
        $guestemails = $modx->getOption('guestemail', $properties, '');
        $code = $modx->getOption('code', $_REQUEST, '');
        $iid = $modx->getOption('iid', $_REQUEST, '');

        if (!empty($date_id)) {
            if ($date_object = $modx->getObject('fbuchDate', $date_id)) {
                $date = $date_object->get('date');
                $start_time = $date_object->get('start_time');
                $type = $date_object->get('type');
                if ($type_object = $date_object->getOne('Type')) {
                    $hooksnippet = $type_object->get('registration_hooksnippet');
                }
            }

            if (is_array($removepersons)) {
                foreach ($removepersons as $person) {
                    if (!empty($date_id) && !empty($person)){ 
                        if ($datename_o = $this->modx->getObject('fbuchDateNames', ['date_id'=>$date_id,'member_Id'=>$person])){
                            $datename_id = $datename_o->get('id');
                            $this->removePersonFromDate($datename_id);    
                        }
                    }
                }
            }
            if (is_array($persons)) {
                foreach ($persons as $index => $person) {
                    if (!empty($person) && $name_o = $modx->getObject('mvMember', $person)) {
                        if ($datename_o = $modx->getObject('fbuchDateNames', array('date_id' => $date_id, 'member_id' => $person))) {
                            if ($person == $current_member_id){
                                $datename_o->set('registeredby_member', 0);
                                $datename_o->save();
                            }
                        } else {
                            $datename_o = $modx->newObject('fbuchDateNames');
                            $datename_o->set('date_id', $date_id);
                            $datename_o->set('member_id', $person);
                            $datename_o->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                            if ($person != $current_member_id){
                                $datename_o->set('registeredby_member', $current_member_id);
                            }
                            $datename_o->save();
                            $comment = 'Anmeldung: ' . $name_o->get('firstname') . ' ' . $name_o->get('name');
                            $this->addDateComment($comment,$date_id,$current_member_id);
                            //$this->sendElementMessage($comment,$date_id,$current_member_id);
                        }

                        $this->uncancelInvite($date_id,$person); 
                        
                        $this->sendElementInvite($date_id,$person);

                        if (!empty($hooksnippet)) {
                            $sn_properties = [];
                            $sn_properties['object'] = $datename_o;
                            //$properties['fields'] = $fields;
                            $sn_properties['action'] = 'addperson';
                            $sn_properties['index'] = $index;
                            $modx->runSnippet($hooksnippet, $sn_properties);
                        }
                    }
                }
            }
            if (is_array($guestnames)) {
                $gast_id = 0;
                /*
                if ($gast_o = $modx->getObject('mvMember', array('firstname' => '', 'name' => 'Gast'))) {
                    $gast_id = $gast_o->get('id');
                }
                */
                foreach ($guestnames as $key => $guestname) {
                    if (!empty($guestname)) {
                        if ($datename_o = $modx->getObject('fbuchDateNames', array('date_id' => $date_id, 'guestname' => $guestname))) {

                        } else {
                            $datename_o = $modx->newObject('fbuchDateNames');
                            $datename_o->set('date_id', $date_id);
                            $datename_o->set('member_id', $gast_id);
                            $datename_o->set('guestname', $guestname);
                            $datename_o->set('guestemail', $guestemails[$key]);
                            $datename_o->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                            $datename_o->set('registeredby_member', $current_member_id);
                            $datename_o->save();
                            $comment = 'Gasteintrag: ' . $guestname;
                            $this->addDateComment($comment,$date_id,$current_member_id);
                            //$this->sendElementMessage($comment,$date_id,$current_member_id);                            
                        }
                    }
                }
            }
        }
        if (!empty($redirect)) {
            $modx->sendRedirect($modx->makeUrl($redirect, '', array(
                'iid' => $iid,
                'code' => $code,
                'date_id' => $date_id)));
        }
    }

    public function is_authenticated(){
        return $this->authenticated;
    }    

    public function cancelAcceptInvite($scriptProperties = array()) {

        $modx = &$this->modx;
        $action = $modx->getOption('process', $_REQUEST, '');
        $code = $modx->getOption('code', $_REQUEST, '');
        $iid = $modx->getOption('iid', $_REQUEST, '');
        $comment = $modx->getOption('comment', $_REQUEST, '');
        $mail_comment = $modx->getOption('mail_comment', $_REQUEST, '');
        $date_id = $modx->getOption('date_id', $_REQUEST, false);
        $redirect = isset($scriptProperties['redirect']) ? $scriptProperties['redirect'] : '1';
        $email = false;
        $remove_comment = (int)$modx->getOption('remove_comment', $_REQUEST, '');
        $action = !empty($remove_comment) ? 'remove_comment' : $action;
        $send_riot = $modx->getOption('send_riot', $scriptProperties, '1');

        if ($invite_o = $modx->getObject('fbuchDateInvited', $iid)) {
            $date_id = $invite_o->get('date_id');
            $member_id = $invite_o->get('member_id');
            if ($name_o = $invite_o->getOne('Member')) {
                $email = $this->getNameEmail($name_o);
            }
        }
        $code_matches = ($date_id && $email && $code == md5($date_id . $email . $iid)) ? true : false;

        //try to get invite of the current user or create an invite for him, if he is calling this page
        if ($date_id && empty($member_id)) {
            if ($modx->user->ismember('fbuch')) {
                if ($modx->user->Profile->get('fullname') != 'rgm' && $name_o = $modx->getObject('mvMember', array('modx_user_id' => $modx->user->get('id')))) {
                    $member_id = $name_o->get('id');
                    $email = $this->getNameEmail($name_o);
                    if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'member_id' => $member_id))) {

                    } else {
                        $invite_o = $modx->newObject('fbuchDateInvited');
                        $invite_o->set('date_id', $date_id);
                        $invite_o->set('member_id', $member_id);
                        //$invite_o->save();
                    }
                    $_REQUEST['iid'] = $iid = $invite_o->get('id');
                    $_REQUEST['code'] = $code = md5($date_id . $email . $iid);
                }
            }
        }

        $modx->setPlaceholder('member_id', $member_id);
        $modx->setPlaceholder('date_id', $date_id);

        if ($action) {
            $cai_post = serialize($_REQUEST);
            if (isset($_SESSION['last_cai_post']) && $_SESSION['last_cai_post'] == $cai_post) {
                //return;
            }
            $_SESSION['last_cai_post'] = $cai_post;
        }

        $code_matches = ($date_id && $email && $code == md5($date_id . $email . $iid)) ? true : false;

        if ($code_matches) {
            $fields = $invite_o->toArray();

            switch ($action) {
                case 'add_persons':
                    $properties = $_REQUEST;
                    $properties['date_id'] = $date_id;
                    $properties['person'] = $member_id;
                    $properties['redirect'] = $redirect;
                    $this->addPersonsToDate($properties);
                    break;
                case 'accept':
                    $invite_o->set('canceled', 0);
                    $invite_o->save();
                    $this->updateTeamrowReservations($fields, 'add');

                    $iproperties = array();
                    $iproperties['invite_id'] = $invite_o->get('id');
                    $iproperties['comment'] = '';
                    $iproperties['add_datecomment'] = false;
                    $iproperties['subject_prefix'] = 'Deine Zusage';

                    $this->scheduleInviteMail($iproperties);

                    //$this->sendInviteMail($invite_o->get('id'), '', '', false, 'Deine Zusage');
                    $comment = empty($comment) ? 'Zusage' : $comment;
                    break;
                case 'cancel':
                    $invite_o->set('canceled', 1);
                    $invite_o->save();
                    $this->updateTeamrowReservations($fields, 'remove');

                    $iproperties = array();
                    $iproperties['invite_id'] = $invite_o->get('id');
                    $iproperties['comment'] = '';
                    $iproperties['add_datecomment'] = false;
                    $iproperties['subject_prefix'] = 'Deine Absage';

                    $this->scheduleInviteMail($iproperties);

                    //$this->sendInviteMail($invite_o->get('id'), '', '', false, 'Deine Absage');
                    $comment = empty($comment) ? 'Absage' : $comment;
                    break;
                case 'remove_comment':
                    $this->removeDateComment($remove_comment,$member_id);
                    break;
            }

            if (!empty($comment)) {
                $this->addDateComment($comment,$date_id,$member_id);

                if ($mail_comment == 'selected' && isset($_REQUEST['mailto'])) {
                    $mail_comment = ($_REQUEST['mailto']);
                }

                $this->sendCommentMails($comment, $mail_comment, $name_o, $date_id);
                if ($action == 'accept' || $action == 'cancel') {
                    $this->sendElementInvite($date_id,$member_id,$action);
                }

                if (!empty($redirect)) {
                    $modx->sendRedirect($modx->makeUrl($modx->resource->get('id'), '', array(
                        'iid' => $iid,
                        'code' => $code,
                        'date_id' => $date_id)));
                }
            }
        } elseif (!empty($_REQUEST['riot']) && $code == md5($date_id)) {

            $modx->setPlaceholder('hide_introbox', 1);

        } else {
            $this->error('Leider konnten wir keine passenden Daten zu Deiner Anfrage finden.');
        }

        return '';

    }
    
    public function addDateComment($comment,$date_id,$member_id){
        if (!empty($comment)) {
                $comment_o = $this->modx->newObject('fbuchDateComment');
                $comment_o->set('date_id', $date_id);
                $comment_o->set('member_id', $member_id);
                $comment_o->set('comment', $comment);
                $comment_o->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                $comment_o->set('createdby', $this->modx->user->get('id'));
                $comment_o->save(); 
                $this->sendElementMessage($comment,$date_id,$member_id);      
        }
              
    }

    public function removeDateComment($comment_id,$member_id){
        if ($comment_o = $this->modx->getObject('fbuchDateComment', $comment_id)) {
            $createdby = $comment_o->get('createdby');
            if ((!empty($createdby) && $this->modx->user->get('id') == $createdby) || $member_id == $comment_o->get('member_id')) {
                $comment_o->remove();
            }
        }    
    }

    public function sendElementInvite($date_id,$member_id,$action=''){
        $send_riot = $this->modx->getOption('send_riot', null, '1');
        if ($send_riot != '1'){
            return;
        }
        $element_invite = 0;
        if ($date_o = $this->modx->getObject('fbuchDate',$date_id)){
            if ($type_o = $date_o->getOne('Type')){
                $element_invite = (int) $type_o->get('element_invite');
            }
        }

        if ($member = $this->modx->getObject('mvMember',$member_id)){
            $item = $member->toArray();
        }        

        //$this->sendElementMessage($comment,$date_o->get('id'),$name_o->get('id'));
        
        if (!empty($element_invite)) {

            //invite to Element/Matrix - Room

            $properties = [
                'action' => 'invite',
                'invite_action' => $action,
                'date_id' => $date_id,
                'name_id' => $member_id
            ];
            //$this->modx->runSnippet('moc_hooks', $scriptProperties);
            $reference = 'web/schedule/invite';
            $this->createSchedulerTask('matrixorgclient', array('snippet' => $reference));
            $this->createSchedulerTaskRun($reference, 'matrixorgclient', $properties);

            //$this->modx->runSnippet('moc_hooks', $properties);
            
        }
    }    

    public function sendElementMessage($comment,$date_id,$name_id,$action='send'){
        $send_riot = $this->modx->getOption('send_riot', null, '1');
        if ($send_riot != '1'){
            return;
        }
        $element_invite = 0;
        if ($date_o = $this->modx->getObject('fbuchDate',$date_id)){
            if ($type_o = $date_o->getOne('Type')){
                $element_invite = (int) $type_o->get('element_invite');
            }
        }
        if (!empty($element_invite)){
            $properties = array(
                'action'  => $action,
                'date_id' => $date_id,
                'name_id' => $name_id,
                'comment' => $comment,
                'user_id' => $this->modx->user->get('id')
            );        
    
            $reference = 'web/schedule/sendcomment';
            $this->createSchedulerTask('matrixorgclient', array('snippet' => $reference));
            $this->createSchedulerTaskRun($reference, 'matrixorgclient', $properties);            
        }
    }

    public function sendCommentMails($comment, $mail_comment, $name_o, $date_id) {
        $modx = &$this->modx;
        $classname = '';
        $comment_name = $name_o->get('firstname') . ' ' . $name_o->get('name');
        $send_self = false;

        switch ($mail_comment) {
            case 'accepted':
                $classname = 'fbuchDateNames';
                $where = array('date_id' => $date_id);
                $id_field = 'member_id';
                break;
            case 'invited':
                $classname = 'fbuchDateInvited';
                $where = array('date_id' => $date_id);
                $id_field = 'member_id';
                break;
        }

        if (is_array($mail_comment)) {
            $classname = 'mvMember';
            $where = array('id:IN' => $mail_comment);
            $id_field = 'id';
            $send_self = true;
        }

        if (!empty($classname)) {
            $c = $modx->newQuery($classname);
            $c->where($where);
            if ($collection = $modx->getCollection($classname, $c)) {
                foreach ($collection as $object) {
                    $member_id = $object->get($id_field);
                    if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'member_id' => $member_id))) {

                    } else {
                        $invite_o = $modx->newObject('fbuchDateInvited');
                        $invite_o->set('member_id', $member_id);
                        $invite_o->set('date_id', $date_id);
                        $invite_o->save();
                    }
                    if ($send_self || $member_id != $name_o->get('id')) {

                        $iproperties = array();
                        $iproperties['invite_id'] = $invite_o->get('id');
                        $iproperties['comment'] = $comment;
                        $iproperties['comment_name'] = $comment_name;
                        $iproperties['add_datecomment'] = false;
                        $iproperties['subject_prefix'] = 'Neuer Kommentar';

                        $this->scheduleInviteMail($iproperties);

                        //$this->sendInviteMail($invite_o->get('id'), $comment, $comment_name, false, 'Neuer Kommentar');
                    }

                }
            }

        }
    }

    public function updateBootSetting() {
        $setting = isset($_REQUEST['setting']) ? $_REQUEST['setting'] : '';
        $settings = array();
        if (is_array($setting) && isset($setting['stemmbrett']) && is_array($setting['stemmbrett'])) {

            foreach ($setting['stemmbrett'] as $key => $v) {
                $values = array();
                $values['MIGX_id'] = $key + 1;
                foreach ($setting as $field => $value) {
                    $values[$field] = $value[$key];
                }
                $settings[] = $values;
            }
            $_POST['settings'] = $_REQUEST['settings'] = json_encode($settings);
        }
    }

    public function afterCreateDate(&$date_o) {
        
        $type_o = $date_o->getOne('Type');

        if (!$type_o) {
            return true;
        }

        $element_invite = $type_o->get('element_invite');
        
        if (!empty($element_invite)) {

            $scriptProperties = array('action' => 'createDateRoom', 'date_id' => $date_o->get('id'));
            $reference = 'web/schedule/createdateroom';
            $this->createSchedulerTask('matrixorgclient', array('snippet' => 'web/schedule/createdateroom'));
            $this->createSchedulerTaskRun($reference, 'matrixorgclient', $scriptProperties);

            /*
            $scriptProperties = array('action' => 'addRoomToGroup', 'date_id' => $date_o->get('id'));
            $reference = 'web/schedule/addroomtogroup';
            $this->createSchedulerTask('matrixorgclient', array('snippet' => 'web/schedule/addroomtogroup'));
            $this->createSchedulerTaskRun($reference, 'matrixorgclient', $scriptProperties);
            */
            //$this->modx->runSnippet('moc_hooks', $scriptProperties);
        }
        return true;
    }

    public function scheduleCheckComingOrPastSpace(){
        $modx = &$this->modx;
        $c = $modx->newQuery('fbuchDate');
        //check all currently in coming space
        $today = strftime('%Y-%m-%d 00:00:00');
        $c->where(['riot_room_id:!='=>'','matrix_space:IN'=>['1'],'date:<'=>$today]);
        //$c->prepare();echo $c->toSql();
        $count = 0;
        $perminute = 5;
        $minutes = 1;
        if ($collection = $modx->getIterator('fbuchDate', $c)) {
            foreach ($collection as $object) {
                $count++;
                //echo '<pre>' . print_r($object->toArray(),1) . '</pre>';
                $scriptProperties = array('date_id' => $object->get('id'));
                $reference = 'web/schedule/checkcomingorpastspace';
                $this->createSchedulerTask('matrixorgclient', array('snippet' => 'web/schedule/checkcomingorpastspace'));
                $this->createSchedulerTaskRun($reference, 'matrixorgclient', $scriptProperties, $minutes);  
                if ($count >= $perminute){
                    $minutes++;
                    $count=0;
                }                              
            }
        }
        $c = $modx->newQuery('fbuchDate');
        //check all currently in no space
        $c->where(['riot_room_id:!='=>'','matrix_space:IN'=>['0']]);
        $c->sortby('date','DESC');
        $c->limit(30);        
        //$c->prepare();echo $c->toSql();
        if ($collection = $modx->getIterator('fbuchDate', $c)) {
            foreach ($collection as $object) {
                $count++;
                //echo '<pre>' . print_r($object->toArray(),1) . '</pre>';
                $scriptProperties = array('date_id' => $object->get('id'));
                $reference = 'web/schedule/checkcomingorpastspace';
                $this->createSchedulerTask('matrixorgclient', array('snippet' => 'web/schedule/checkcomingorpastspace'));
                $this->createSchedulerTaskRun($reference, 'matrixorgclient', $scriptProperties, $minutes);  
                if ($count >= $perminute){
                    $minutes++;
                    $count=0;
                }                              
            }
        }        
    }

    public function cleanOldDates() {
        $modx = &$this->modx;
        //get all Dates older than now - 2 weeks
        //$rooms = $this->moc->getGroupRooms();
        $this->scheduleCheckComingOrPastSpace();
        //$this->scheduleKickUsersFromPastRooms();

    }

    public function autoduplicateDates() {
        $modx = &$this->modx;
        //get all Dates with autoduplicate==1 and duplicated==0 and older than now + 1 week
        $query_date = strftime('%Y-%m-%d 23:59:59', strtotime('+ 1 week'));
        $query_date_min = strftime('%Y-%m-%d 23:59:59', strtotime('- 1 week'));
        $c = $modx->newQuery('fbuchDate');
        $c->where(array(
            'date:<' => $query_date,
            'date:>' => $query_date_min,
            'deleted' => 0,
            'autoduplicate' => 1,
            'autoduplicated' => 0));
        if ($collection = $modx->getIterator('fbuchDate', $c)) {
            foreach ($collection as $old_object) {
                $object = $modx->newObject('fbuchDate');
                $object->fromArray($old_object->toArray());
                $object->set('date', strftime('%Y-%m-%d 00:00:00', strtotime('+ 1 week', strtotime($object->get('date')))));
                $date_end = $object->get('date_end');
                if (!empty($date_end)) {
                    $object->set('date_end', strftime('%Y-%m-%d 00:00:00', strtotime('+ 1 week', strtotime($object->get('date_end')))));
                }
                $old_object->set('autoduplicated', 1);
                $old_object->save();
                $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                $object->set('autoduplicated', 0);
                $object->set('matrix_members_kicked', 0);
                $object->set('matrix_space', 0);
                $object->set('riot_room_id', '');
                $object->set('last_matrix_start_token', '');
                $object->set('locked', 0);
                $object->save();
                $this->checkDateMailinglistNames($object->get('id'));
                $this->afterCreateDate($object);
                echo '<pre>' . print_r($object->toArray(), 1) . '</pre>';
                if ($old_object->get('autoduplicate_invited') == 1) {
                    $this->duplicateDateInvited($old_object, $object);
                }
                if ($old_object->get('autoduplicate_names') == 1) {
                    $this->duplicateDateNames($old_object, $object);
                }

            }
        }

    }

    public function duplicateDateNames(&$old_object, &$object) {
        $modx = &$this->modx;
        if ($names = $old_object->getMany('Members')) {
            foreach ($names as $name) {
                $new_name = $modx->newObject('fbuchDateNames');
                $new_name->fromArray($name->toArray());
                $new_name->set('date_id', $object->get('id'));
                $new_name->save();
            }
        }

    }

    public function duplicateDateInvited(&$old_object, &$object) {
        $modx = &$this->modx;
        if ($names = $old_object->getMany('Invited')) {
            foreach ($names as $name) {
                $member_id = $name->get('member_id');
                $date_id = $object->get('id');
                if ($new_name = $modx->getObject('fbuchDateInvited', array('member_id' => $member_id, 'date_id' => $date_id))) {

                } else {
                    $new_name = $modx->newObject('fbuchDateInvited');
                    $new_name->fromArray($name->toArray());
                    $new_name->set('canceled', 0);
                    $new_name->set('invited', 0);
                    $new_name->set('riot_state', 0);
                    $new_name->set('date_id', $object->get('id'));
                    $new_name->save();
                }
            }
        }
    }

    public function importMembersAsInvited($object) {
        $modx = &$this->modx;
        $c = $modx->newQuery('mvMember');
        $c->where(array('member_status' => 'Mitglied'));
        $c->sortby('name');
        $c->sortby('firstname');
        if ($names = $modx->getCollection('mvMember', $c)) {
            foreach ($names as $name) {
                //echo '<pre>' . print_r($name->toArray(),1) . '</pre>';
                $member_id = $name->get('id');
                $date_id = $object->get('id');
                if ($new_name = $modx->getObject('fbuchDateInvited', array('member_id' => $member_id, 'date_id' => $date_id))) {

                } else {
                    $new_name = $modx->newObject('fbuchDateInvited');
                    $new_name->set('member_id', $member_id);
                    $new_name->set('canceled', 0);
                    $new_name->set('invited', 0);
                    $new_name->set('riot_state', 0);
                    $new_name->set('date_id', $object->get('id'));
                    $new_name->save();
                }
            }
        }
    }

    public function createSchedulerTask($namespace, $properties) {
        $modx = &$this->modx;

        $task_type = 'sProcessorTask';

        $snippet = $modx->getOption('snippet', $properties, '');
        $path = $modx->getOption('scheduler.core_path', null, $modx->getOption('core_path') . 'components/scheduler/');
        $scheduler = $modx->getService('scheduler', 'Scheduler', $path . 'model/scheduler/');

        if ($task = $modx->getObject($task_type, array('reference' => $snippet))) {

        } else {
            $task = $modx->newObject($task_type);
            $task->fromArray(array(
                'class_key' => $task_type,
                'content' => $snippet,
                'namespace' => $namespace,
                'reference' => $snippet,
                'description' => ''));
            if (!$task->save()) {
                return 'Error saving Task';
            }
        }


    }

    public function createSchedulerTaskRun($reference, $namespace, $properties = array(),$minutes=1) {
        $modx = &$this->modx;

        // Load the Scheduler service class
        $path = $modx->getOption('scheduler.core_path', null, $modx->getOption('core_path') . 'components/scheduler/');
        $scheduler = $modx->getService('scheduler', 'Scheduler', $path . 'model/scheduler/');
        if (!($scheduler instanceof Scheduler)) {
            return 'Oops, could not get Scheduler service.';
        }

        /**
         * Get the task with reference "dosomething" in the "mycmp" namespace.
         * This task should have been added earlier via a build or the component. 
         */
        $task = $scheduler->getTask($namespace, $reference);
        if ($task instanceof sTask) {
            // Schedule a run in 10 minutes from now
            // We're passing along an array of data; in this case a client ID.
            $task->schedule('+' . $minutes . ' minutes', $properties);
        }


    }

    public function userCouldBeCreated($object_id){
        $usergroups = $this->getMemberUsergroups($object_id);
        $allowed = !empty($usergroups) ? true : false;
        return $allowed;
    }

    public function getMemberUsergroups($object_id){
        $modx = &$this->modx;
        if (!empty($object_id) && $object = $modx->getObject('mvMember', array('id' => $object_id))) {
            $member_status = $object->get('member_status');
            if (!empty($member_status) && $member_state = $modx->getObject('mvMemberState',['state'=>$member_status])){
                $usergroups = $member_state->get('add_to_usergroups');
                return $usergroups;
            }
        }
        return false;        
    }

    public function createUserFromMember($object_id,$active=0){
        $modx = &$this->modx;
        if (!empty($object_id) && $object = $modx->getObject('mvMember', array('id' => $object_id))) {
            $allowed = $usergroups = $this->getMemberUsergroups($object_id);
            if (!$allowed){
                return false;
            }
            $user_id = $object->get('modx_user_id');
            if ($user = $modx->getObject('modUser',array('id'=>$user_id))){
                
            } else {
                $user_id = 0;
            }
            
            if ($user_id == 0) {
                $firstname = $object->get('firstname');
                $lastname = $object->get('name'); 
                $date = new DateTime($object->get('birthdate'));
                $birthdate = date_format($date,'Y');
                $user = $modx->newObject('modUser');

                $password = "";
                for($i=0;$i<50;$i++) {
                    $password .= chr( (mt_rand(1, 36) <= 26) ? mt_rand(97, 122) : mt_rand(48, 57 ));
                }                
               
                $year = substr($birthdate, 2, 2);
                $user->set('username', trim(strtolower($firstname)) . trim(strtolower($lastname)) . $year);
                $user->set('active', $active);
                $user->set('password', $password);
                $profile = $modx->newObject('modUserProfile');
                $user->addOne($profile);
                $profile->set('fullname', $firstname . ' ' . $lastname);
                $profile->set('email', $object->get('email'));
                $user->save();
                $groups = explode(',',$usergroups);
                foreach ($groups as $group){
                    $user->joinGroup($group, 'Member');                  
                }
                $user_id = $user->get('id');
                if ($collection = $modx->getCollection('mvMember',['modx_user_id'=>$user_id])){
                    foreach ($collection as $member){
                        $member->set('modx_user_id',0);
                        $member->save();
                    }
                }
                
                $object->set('modx_user_id',$user_id);
                $object->save();
                
            }
            return $user;
        }
        return false;
    }

    public function scheduleInviteMail($properties) {
        $reference = 'web/schedule/sendinvitemail';
        $this->createSchedulerTask('fbuch', array('snippet' => $reference));
        $this->createSchedulerTaskRun($reference, 'fbuch', $properties);
    }

    public function update(&$hook, $scriptProperties) {
        $modx = &$this->modx;

        $classname = $modx->getOption('classname', $scriptProperties, '');
        $processproperty = $modx->getOption('processproperty', $scriptProperties, '');
        $grid_id = isset($_GET['grid_id']) ? $_GET['grid_id'] : '';
        $action = $modx->getOption('processaction', $scriptProperties, '');
        $action = $modx->getOption('processaction', $_REQUEST, $action);
        $type = $modx->getOption('type', $_REQUEST, '');
        $return = $modx->getOption('return', $_REQUEST, 'form');

        if ($action == 'import_invites') {
            $date_id = $modx->getOption('date_id', $_REQUEST, '');
            $object_id = $modx->getOption('object_id', $_REQUEST, '');
            if (!empty($date_id) && !empty($object_id)) {
                if ($old_object = $modx->getObject('fbuchDate', $object_id)) {
                    if ($object = $modx->getObject('fbuchDate', $date_id)) {
                        //print_r($object->toArray());
                        $this->duplicateDateInvited($old_object, $object);
                    }

                }
            }
            return '';
        }

        if ($action == 'import_members') {
            $date_id = $modx->getOption('date_id', $_REQUEST, '');
            $process = $modx->getOption('process', $_REQUEST, '');
            if (!empty($process) && !empty($date_id)) {
                if ($object = $modx->getObject('fbuchDate', $date_id)) {
                    //print_r($object->toArray());
                    $this->importMembersAsInvited($object);
                }
            }
            return '';
        }

        if ($action == 'autoduplicateDates') {
            $this->autoduplicateDates();
            return;
        }

        if ($action == 'cleanOldDates') {
            $this->cleanOldDates();
            return;
        }

        if ($action == 'updateBootSetting') {
            $this->updateBootSetting();
            return;
        }

        if ($action == 'dragdrop') {
            $this->handleDragDrop();
            return;
        }
        if ($action == 'lockfahrt') {
            $this->lockFahrt();
            return;
        }
        if ($action == 'remove_invite') {
            $classname = $classname . 'Remove';
        }

        switch ($classname) {
            case 'fbuchDateInvited':
                if ($action == 'mail_invite' || $action == 'riotinvite_invite') {

                    $object_id = $hook->getValue('object_id');
                    $comment = $hook->getValue('comment');
                    if ($object = $modx->getObject($classname, $object_id)) {
                        $date_o = $object->getOne('Date');
                        $profile = $modx->user->getOne('Profile');
                        $comment_name = $profile->get('fullname');
                        if ($action == 'mail_invite') {

                            $iproperties = array();
                            $iproperties['invite_id'] = $object->get('id');
                            $iproperties['comment'] = $comment;
                            $iproperties['comment_name'] = $comment_name;
                            $iproperties['add_datecomment'] = true;
                            $iproperties['subject_prefix'] = '';

                            $this->scheduleInviteMail($iproperties);

                            //$this->sendInviteMail($object->get('id'), $comment, $comment_name, true);
                        }
                        if ($action == 'mail_invite' || $action == 'riotinvite_invite') {
                            $this->sendElementInvite($object->get('date_id'),$object->get('member_id'),$action);
                        }
                        $modx->setPlaceholder('success_object_id', $object->get('date_id'));
                    }

                    return true;
                }
                if ($action == 'mail_invites' || $action == 'riotinvite_invites') {

                    $object_id = $hook->getValue('object_id');
                    $comment = $hook->getValue('comment');
                    if ($date_o = $modx->getObject('fbuchDate', $object_id)) {
                        $c = $modx->newQuery($classname);
                        $c->where(array('date_id' => $object_id));
                        $add_datecomment = true;
                        if ($collection = $modx->getCollection($classname, $c)) {
                            foreach ($collection as $object) {
                                $profile = $modx->user->getOne('Profile');
                                $comment_name = $profile->get('fullname');
                                if ($action == 'mail_invites') {
                                    $iproperties = array();
                                    $iproperties['invite_id'] = $object->get('id');
                                    $iproperties['comment'] = $comment;
                                    $iproperties['comment_name'] = $comment_name;
                                    $iproperties['add_datecomment'] = $add_datecomment;
                                    $iproperties['subject_prefix'] = '';

                                    $this->scheduleInviteMail($iproperties);
                                    //$this->sendInviteMail($object, $comment, $comment_name, $add_datecomment, 'RGM Einladung');
                                }
                                if ($action == 'mail_invites' || $action == 'riotinvite_invites') {
                                    $this->sendElementInvite($object->get('date_id'),$object->get('member_id'),$action);
                                }
                                $add_datecomment = false;
                            }
                        }
                        $modx->setPlaceholder('success_object_id', $object_id);
                    }
                    return true;
                }

            case 'ErgoterminReservierung':
                $fields['date_id'] = $modx->getOption('fahrt_id', $_REQUEST, '');
                $fields['member_id'] = $modx->getOption('member_id', $_REQUEST, '');
                $this->updateTeamrowReservations($fields, $action, $classname);
                if (!empty($action)) {

                }

                return '';
                break;
            case 'fbuchFahrtNames':

                $fields['fahrt_id'] = $modx->getOption('fahrt_id', $_REQUEST, '');
                $fields['member_id'] = $modx->getOption('member_id', $_REQUEST, '');
                $fields['id'] = $modx->getOption('fahrtnames_id', $_REQUEST, '');

                if (!$this->checkPermission('fbuch_edit_old_entries', array('classname' => 'fbuchFahrt', 'object_id' => $fields['fahrt_id']))) {
                    if ($return == 'form') {
                        $modx->setPlaceholder('output', $modx->getChunk('fbuchTeamForm'));
                    }

                    return false;
                }

                if (!empty($action)) {
                    $this->updateFahrtNames($fields, $action);

                }
                if ($return == 'form') {
                    $modx->setPlaceholder('output', $modx->getChunk('fbuchTeamForm'));
                }
                return '';
                break;
            case 'fbuchDateInvitedRemove':
                $classname = 'fbuchDateInvited';
            case 'fbuchDateNames':
                switch ($action) {
                    case 'remove_invite':
                    case 'remove_datename':
                        $success = false;
                        $object_id = $hook->getValue('object_id');
                        if ($object = $modx->getObject($classname, $object_id)) {

                            if ($name_o = $object->getOne('Member')) {
                                $name = $name_o->get('firstname') . ' ' . $name_o->get('name');
                                //check if input-name is the correct one
                                if ($name == $hook->getValue('name')) {
                                    $object->remove();
                                    $success = true;
                                }
                            }
                        }
                        if (!$success) {
                            $this->error('Der eingegebene Name entspricht nicht dem Namen, welcher entfernt werden soll.');
                            return false;
                        }
                        if ($object) {
                            $modx->setPlaceholder('success_object_id', $object->get('date_id'));
                        }
                        break;
                }
                break;

            case 'fbuchFahrt':

                $object_id = $hook->getValue('object_id');
                $values = $hook->getValues();

                $object = $this->updateFahrt($object_id, $values, $grid_id);
                if ($object) {
                    $modx->setPlaceholder('success_object_id', $object->get('id'));
                } else {
                    return false;
                }
                break;

            case 'fbuchDate':
                if ($processproperty == 'cancel_accept') {
                    $this->cancelAcceptInvite($scriptProperties);
                    return '';
                }

            case 'fbuchBootRiggerung':
                //$values = $hook->getValues();
                if ($boot = $modx->getObject('fbuchBoot', $hook->getValue('boot_id'))) {
                    $boot->set('gattung_id', $hook->getValue('gattung_id'));
                    $boot->save();
                }

            default:
                $object_id = $hook->getValue('object_id');

                $values = $hook->getValues();

                $duplicate = !empty($values['duplicate']) ? true : false;
                $duplicate_names = !empty($values['duplicate_names']) ? true : false;
                $duplicate_invited = !empty($values['duplicate_invited']) ? true : false;

                if (!$duplicate && !empty($object_id) && $object = $modx->getObject($classname, $object_id)) {

                    switch ($classname) {
                        case 'mvMember':
                            if (!$this->checkPermission('fbuch_edit_names')) {
                                return false;
                            }
                            break;
                    }

                } else {

                    switch ($classname) {
                        case 'mvMember':
                            $firstname = $values['firstname'];
                            $name = $values['name'];

                            if ($object = $modx->getObject($classname, array('firstname' => $firstname, 'name' => $name))) {
                                $modx->setPlaceholder('error_message', 'Ein Eintrag mit diesem Namen und dem Mitgliederstatus ' . $object->get('member_status') . ' existiert bereits in unserer Datenbank und kann nicht nochmal neu erstellt werden.');
                                return false;
                            }
                            if (!empty($firstname) & !empty($name)) {
                                $object = $modx->newObject($classname);
                                $values['inactive'] = 1;
                                $values['inactive_reason'] = 'ist kein Mitglied';

                            } else {
                                return false;
                            }
                            break;
                        default:
                            $object = $modx->newObject($classname);
                            break;
                    }


                }

                if (isset($values['start_time'])) {
                    $start_time = str_replace('.', ':', $values['start_time']);
                    $time_parts = explode(':', $start_time);
                    $hour = (int)$time_parts[0];
                    $hour = $hour < 10 ? '0' . $hour : $hour;
                    $min = isset($time_parts[1]) ? (int)$time_parts[1] : 0;
                    $min = $min < 10 ? '0' . $min : $min;

                    $values['start_time'] = $hour . ':' . $min;
                }

                $object->fromArray($values);
                $object->save();

                if ($classname = 'fbuchDate') {

                    $this->checkDateMailinglistNames($object->get('id'));
                    $this->afterCreateDate($object);

                    if ($type == 'Rudern' && $duplicate && $duplicate_names) {
                        if (!empty($object_id) && $old_object = $modx->getObject($classname, $object_id)) {
                            $this->duplicateDateNames($old_object, $object);
                        }
                    }
                    if ($type == 'Rudern' && $duplicate && $duplicate_invited) {
                        if (!empty($object_id) && $old_object = $modx->getObject($classname, $object_id)) {
                            $this->duplicateDateInvited($old_object, $object);
                        }
                    }
                }


                $modx->setPlaceholder('success_object_id', $object->get('id'));
                break;

        }


        return true;
    }

    public function hasPermission($pm,$user,$context_key) {
        if ($context = $this->modx->getObject('modContext',['key'=>$context_key])){
            $state = $context->checkPolicy($pm,null,$user);    
        }
        return $state;
    }    

    public function checkMemberMailinglists($member_id) {
        $modx = &$this->modx;
        //remove person from fbuchMailinglist, if not longer can be invited
        if ($object = $modx->getObject('mvMember', $member_id)) {
            $can_be_invited = false;
            $modx_user_id = $object->get('modx_user_id');
            $member_status = $object->get('member_status');
            if ($state = $modx->getObject('mvMemberState',['state'=>$member_status])){
                $can_be_invited = (bool) $state->get('can_be_invited');    
            }
            $deleted = $object->get('deleted');
            $can_be_invited = !empty($deleted) ? false : $can_be_invited;

            if (!$can_be_invited) {
                $c = $modx->newQuery('fbuchMailinglistNames');
                $c->where(array('member_id' => $member_id));
                if ($collection = $modx->getCollection('fbuchMailinglistNames', $c)) {
                    foreach ($collection as $name) {
                        $list_id = $name->get('list_id');
                        $name->remove();
                        $this->updateDatesMailinglistNames($list_id);
                        //$name->remove();
                    }
                }
            }
        }
    }

    public function updateDatesMailinglistNames($list_id) {
        $modx = &$this->modx;

        $date = strftime('%Y-%m-%d 00:00:00', strtotime('-2 weeks'));
        $c = $modx->newQuery('fbuchDate');
        $c->where(array('date:>' => $date, 'mailinglist_id' => $list_id));
        if ($dates = $modx->getCollection('fbuchDate', $c)) {
            foreach ($dates as $date) {
                $this->checkDateMailinglistNames($date->get('id'));
            }
        }
    }

    public function getMembersByFilter($filter_id) {
        $modx = &$this->modx;
        $where_json = $modx->runSnippet('mv_prepareMemberWhere', array('filter_id' => $filter_id));
        $where = $modx->fromJson($where_json);
        $collection = null;
        if (is_array($where) && !empty($where)) {

            $classname = 'mvMember';
            $c = $modx->newQuery($classname);
            $c->where($where);
            $count = $modx->getCount($classname, $c);
            $collection = $modx->getIterator($classname, $c);
        }
        return $collection;
    }

    public function checkDateMailinglistNames($date_id) {
        $modx = &$this->modx;
        if (!empty($date_id) && $object = $modx->getObject('fbuchDate', array('id' => $date_id))) {
            $mailinglist_id = $object->get('mailinglist_id');

            //get all existing invited names
            $c = $modx->newQuery('fbuchDateInvited');
            $c->where(array('date_id' => $date_id));
            //$c->prepare();echo $c->toSql();die();
            $existing = array();
            if ($invite_c = $modx->getCollection('fbuchDateInvited', $c)) {
                foreach ($invite_c as $invite_o) {
                    $member_id = $invite_o->get('member_id');
                    $existing[$member_id] = $member_id;
                }
            }

            //print_r($existing);die();

            if (!empty($mailinglist_id)) {
                $member_filter_id = 0;
                if ($mailinglist = $modx->getObject('fbuchMailinglist', array('id' => $mailinglist_id))) {
                    $member_filter_id = $mailinglist->get('member_filter_id');
                    if ($names = $this->getMembersByFilter($member_filter_id)) {
                        foreach ($names as $name) {
                            $member_id = $name->get('id');
                            //$unsubscribed = $name->get('unsubscribed');
                            $unsubscribed = 0;
                            if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'member_id' => $member_id))) {
                                if (!empty($unsubscribed)) {
                                    $invite_o->remove();
                                } else {
                                    $invite_o->set('mailinglist_id', $mailinglist_id);
                                    $invite_o->save();
                                }
                            } else {
                                $invite_o = $modx->newObject('fbuchDateInvited');
                                if (!empty($unsubscribed)) {
                                    //$invite_o->remove();
                                } else {
                                    $invite_o->set('mailinglist_id', $mailinglist_id);
                                    $invite_o->set('date_id', $date_id);
                                    $invite_o->set('member_id', $member_id);
                                    $invite_o->save();
                                }
                            }
                            unset($existing[$member_id]);
                        }
                    }
                }
                if (empty($member_filter_id)) {
                    $c = $modx->newQuery('fbuchMailinglistNames');
                    //only pull members which can be invited
                    $c->leftjoin('mvMember', 'Member');
                    $c->leftjoin('mvMemberState','State','State.state = Member.member_status');
                    $c->where(array('list_id' => $mailinglist_id,'State.can_be_invited' => 1));
                    if ($names = $modx->getCollection('fbuchMailinglistNames', $c)) {
                        foreach ($names as $name) {
                            $member_id = $name->get('member_id');
                            $unsubscribed = $name->get('unsubscribed');

                            if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'member_id' => $member_id))) {
                                if (!empty($unsubscribed)) {
                                    $invite_o->remove();
                                } else {
                                    $invite_o->set('mailinglist_id', $mailinglist_id);
                                    $invite_o->save();
                                }
                            } else {
                                if (!empty($unsubscribed)) {
                                    //$invite_o->remove();
                                } else {
                                    $invite_o = $modx->newObject('fbuchDateInvited');
                                    $invite_o->set('mailinglist_id', $mailinglist_id);
                                    $invite_o->set('date_id', $date_id);
                                    $invite_o->set('member_id', $member_id);
                                    $invite_o->save();
                                }
                            }
                            unset($existing[$member_id]);
                        }
                    }
                }

            }
            //remove evtl. remaining
            if (is_array($existing) && count($existing)>0){
                $c = $modx->newQuery('fbuchDateInvited');
                $c->where(array('date_id' => $date_id, 'member_id:IN' => $existing));
                //$c->prepare();echo $c->toSql();die();
                if ($invite_c = $modx->getCollection('fbuchDateInvited', $c)) {
                    foreach ($invite_c as $invite_o) {
                        $invite_o->remove();
                    }
                }                
            }

        }
    }

    public function getFormValues(&$hook, $scriptProperties) {
        $modx = &$this->modx;

        $classname = $modx->getOption('classname', $scriptProperties, '');
        $object_id = $modx->getOption('object_id', $_REQUEST, '');
        $grid_id = $modx->getOption('grid_id', $_REQUEST, '');
        $process = $modx->getOption('process', $_REQUEST, '');
        $modx->setPlaceholder('hideform', '0');
        $ruderverbot = '';


        if ($resource = $modx->getObject('modResource', array('id' => 76, 'published' => 1))) {
            $ruderverbot = $resource->get('content');
        }
        if ($process == 'mail_invites' || $process == 'riotinvite_invites') {
            $c = $modx->newQuery($classname);
            $c->where(array('date_id' => $object_id));
            $names = array();
            if ($collection = $modx->getCollection($classname, $c)) {
                foreach ($collection as $object) {
                    $name_o = $object->getOne('Member');
                    if ($name_o) {
                        if ($process == 'mail_invites') {
                            $email = $name_o->get('email');
                        }
                        if ($process == 'riotinvite_invites') {
                            $email = $name_o->get('riot_user_id');
                        }
                        if (!empty($email)) {
                            $names[] = $name_o->get('firstname') . ' ' . $name_o->get('name');
                        }
                    }
                }
            }

            $values['names'] = implode(',', $names);
            $hook->setValues($values);
            return true;
        }

        if ($process == 'closedatefahrt') {
            $classname = 'fbuchDateNames';
        }

        if (!empty($object_id) && $object = $modx->getObject($classname, $object_id)) {

            $values = $object->toArray();
            $values['object_id'] = $object_id;
            switch ($classname) {
                case 'fbuchFahrt':
                    $this->checkPermission('fbuch_edit_old_entries', array('classname' => 'fbuchFahrt', 'object_id' => $object_id));
                    if ($fahrtname = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $values['id']))) {
                        if ($name = $fahrtname->getOne('Member')) {
                            $values['name'] = $name->get('name') . ' ' . $name->get('firstname');
                            $values['member_id'] = $name->get('id');
                        }
                    }
                    if ($fahrtnames = $modx->getCollection('fbuchFahrtNames', array('fahrt_id' => $values['id']))) {
                        $member_ids = array();
                        foreach ($fahrtnames as $fahrtname) {
                            $member_ids[] = $fahrtname->get('member_id');
                        }
                        $values['member_ids'] = implode(',', $member_ids);
                    }
                    break;
                case 'mvMember':
                    $this->checkPermission('fbuch_edit_names', array('classname' => 'mvMember', 'object_id' => $object_id));
                    break;
                case 'fbuchDate':
                    //$this->checkPermission('edit_old_fbuchentries',array('classname'=>'fbuchFahrt', 'object_id'=>$object_id));
                    if ($name = $object->getOne('Instructor')) {
                        $values['instructor_name'] = $name->get('name') . ' ' . $name->get('firstname');
                        //$values['member_id'] = $name->get('id');
                    }

                    break;
                case 'fbuchDateInvited':
                case 'fbuchDateNames':
                    //$this->checkPermission('fbuch_edit_names', array('classname' => 'fbuchFahrt', 'object_id' => $object_id));
                    if ($process == 'closedatefahrt') {
                        if ($name = $object->getOne('Member')) {
                            $values['name'] = $name->get('name') . ' ' . $name->get('firstname');
                            $values['member_id'] = $name->get('id');
                        }
                        if ($dateo = $object->getOne('Date')) {
                            $values['date'] = $dateo->get('date');
                            $values['start_time'] = $dateo->get('start_time');
                        }
                        $values['object_id'] = 0;
                        $values['datenames_id'] = $object_id;
                    } else {
                        if ($name = $object->getOne('Member')) {
                            $name_values = $name->toArray();
                            foreach ($name_values as $key => $value) {
                                $values['Member_' . $key] = $value;
                            }
                        }
                    }

                    break;
            }

            $values['date'] = strftime('%d.%m.%Y', strtotime($values['date']));
            $values['date'] = $values['date'] == '01.01.1970' ? '' : $values['date'];
            $values['birth_date'] = strftime('%d.%m.%Y', strtotime($values['birth_date']));
            //$values['birth_date'] = $values['birth_date']=='01.01.1970'?'':$values['birth_date'];
            $values['date_end'] = strftime('%d.%m.%Y', strtotime($values['date_end']));
            $values['date_end'] = $values['date_end'] == '01.01.1970' ? '' : $values['date_end'];
            $values['selected_boot_' . $values['boot_id']] = 'selected="selected"';
            $values['selected_direction_' . $values['direction']] = 'selected="selected"';
            $values['selected_wfahrt_' . $values['wfahrt']] = 'selected="selected"';
            $values['start_time'] = $modx->runSnippet('fbuchValidateTime', array('input' => $values['start_time']));
            $values['end_time'] = $modx->runSnippet('fbuchValidateTime', array('input' => $values['end_time']));

            if (isset($_REQUEST['process']) && $_REQUEST['process'] == 'close' && empty($values['end_time'])) {
                $values['end_time'] = strftime('%H:%M');
            }

            $hook->setValues($values);
        }

        if (empty($object_id)) {
            $values['object_id'] = 0;
            if ($grid_id == 'Fahrten' && !empty($ruderverbot)) {
                $this->error($ruderverbot);
                $modx->setPlaceholder('hideform', '1');
            }
            $values = array();
            $values['date'] = strftime('%d.%m.%Y');
            $values['start_time'] = strftime('%H:%M');
            if (isset($_REQUEST['type'])) {
                $values['type'] = $_REQUEST['type'];
            }

            switch ($classname) {
                case 'fbuchBootRiggerung':
                    $boot_id = $modx->getOption('boot_id', $_REQUEST, '');
                    if ($boot_o = $modx->getObject('fbuchBoot', $boot_id)) {
                        $values['gattung_id'] = $boot_o->get('gattung_id');
                    }
                    break;
            }


            $hook->setValues($values);
        }

        if ($classname == 'fbuchBootSetting') {
            $boot_id = $modx->getOption('boot_id', $_REQUEST, '');
            $settings = json_decode($values['settings'], true);
            if ($boot_o = $modx->getObject('fbuchBoot', $boot_id)) {
                $seats = $boot_o->get('seats');
            }
            $settings = is_array($settings) ? $settings : array();

            //print_r($settings);

            for ($i = count($settings) + 1; $i <= $seats; $i++) {
                $setting = array();
                $setting['MIGX_id'] = $i;
                $settings[] = $setting;
            }
            $values['settings'] = json_encode($settings);
            $hook->setValues($values);
        }

        //print_r($values);die();
        //$modx->log(modX::LOG_LEVEL_ERROR, 'getFormValues');
        return true;

    }

    public function updateFahrt($object_id, $values, $grid_id) {
        $modx = &$this->modx;

        $classname = 'fbuchFahrt';
        if (!empty($object_id) && $object = $modx->getObject($classname, $object_id)) {
            if (!$this->checkPermission('fbuch_edit_old_entries', array('classname' => $classname, 'object_id' => $object_id))) {
                return false;
            }
            $locked = $object->get('locked');
            if ($locked) {
                $this->error('Diese Fahrt ist gesperrt und kann nicht bearbeitet werden');
                return false;
            }
            $values['editedby'] = $modx->user->get('id');
            $values['editedon'] = strftime('%Y-%m-%d %H:%M:%S');

        } else {
            $object = $modx->newObject($classname);
            $values['createdby'] = $modx->user->get('id');
            $values['createdon'] = strftime('%Y-%m-%d %H:%M:%S');
        }

        if (isset($values['km'])) {
            $values['km'] = str_replace(',', '.', $values['km']);
            $values['km'] = (float) $values['km'];
        }

        $start = '';
        if (isset($values['start_time'])) {
            $start_time = str_replace('.', ':', $values['start_time']);
            $time_parts = explode(':', $start_time);
            $hour = (int)$time_parts[0];
            $hour = $hour < 10 ? '0' . $hour : $hour;
            $min = isset($time_parts[1]) ? (int)$time_parts[1] : 0;
            $min = $min < 10 ? '0' . $min : $min;

            $values['start_time'] = $hour . ':' . $min;

            if (isset($values['date'])) {
                $start = $values['date'] . ' ' . $values['start_time'];
                $start = strtotime($start);

                $this->error(strftime('%d.%m.%Y %H:%M:%S', $start));
                //return false;

            }

        }
        $is_closed = false;
        if (isset($values['km'])) {
            if ($values['km'] > 0) {
                $is_closed = true;
            }
        }
        if (!empty($values['finished'])) {
            $is_closed = true;
        }

        $closetext = '<strong>Wichtig für eine ordnungsgemäße Dokumentation der Einheiten.</strong><br> Bitte zum Abschluß das Trainingsende prüfen und Richtigkeit bestätigen!';
        if ($is_closed && empty($values['endtime_checked'])) {
            $this->error($closetext);
            return false;
        }


        if (isset($values['end_time'])) {

            $start_time = str_replace('.', ':', $values['end_time']);
            $time_parts = explode(':', $start_time);
            $hour = (int)$time_parts[0];
            $hour = $hour < 10 ? '0' . $hour : $hour;
            $min = isset($time_parts[1]) ? (int)$time_parts[1] : 0;
            $min = $min < 10 ? '0' . $min : $min;

            $values['end_time'] = $hour . ':' . $min;

            if (isset($values['date_end'])) {
                if (empty($values['date_end'])) {
                    $values['date_end'] = $values['date'];
                }

                $end = $values['date_end'] . ' ' . $values['end_time'];
                $this->error($end);
                $end = strtotime($end);

                if ($end < $start) {
                    $this->error('Bitte gib ein gültiges (voraussichtliches) Trainingsende ein. Es muß nach der Startzeit liegen');
                    return false;
                } elseif (!$is_closed) {
                    if ($this->checkBoatAvailability($values['boot_id'], $start, $end, $object_id)) {

                    } else {
                        if (empty($values['force_entry'])) {
                            $this->error('Dieses Boot ist bereits belegt<br>von ' . $this->errorstart . '<br>bis ' . $this->errorend . '<br> 
                         <div class="checkbox">
                         <label>
                         <input name="force_entry" type="checkbox" value="1" > Eintrag trotzdem erzwingen. (Nur in dringenden, berechtigten Fällen!)
                         </label>
                         </div>');
                            //$this->error($this->error);
                            return false;
                        }

                    }
                }


            }
        }


        $object->fromArray($values);
        $object->save();

        if (isset($values['member_ids'])) {
            $member_ids = explode(',', $values['member_ids']);
            //first remove all Guests
            if ($members = $object->getMany('Names')) {
                foreach ($members as $member) {
                    if ($this->isguest($member->get('member_id'))) {
                        $member->remove();
                    }
                }
            }

            foreach ($member_ids as $key => $member_id) {
                if (!$this->isguest($member_id) && $fahrtnam = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $object->get('id'), 'member_id' => $member_id))) {
                    //name exists allready in fahrt, do nothing
                } else {
                    if (!empty($member_id) && $fahrtnam = $modx->newObject('fbuchFahrtNames')) {
                        $fahrtnam->set('member_id', $member_id);
                        $fahrtnam->set('fahrt_id', $object->get('id'));
                        $fahrtnam->save();
                    }
                }
            }
        }

        if ($grid_id == 'Ergofahrten' && !empty($values['member_id'])) {
            if ($fahrtnam = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $object->get('id')))) {
                $fahrtnam->set('member_id', $values['member_id']);
            } else {
                $fahrtnam = $modx->newObject('fbuchFahrtNames');
                $fahrtnam->set('member_id', $values['member_id']);
                if (!empty($values['datenames_id'])) {
                    $fahrtnam->set('datenames_id', $values['datenames_id']);
                }
                $fahrtnam->set('fahrt_id', $object->get('id'));
            }
            $fahrtnam->save();
        }

        return $object;
    }

    public function checkBoatAvailability($boot_id, $start, $end, $current_id) {
        $modx = &$this->modx;

        if ($boot = $modx->getObject('fbuchBoot', array('id' => $boot_id))) {
            if ($gattung = $boot->getOne('Bootsgattung')) {
                $check = $gattung->get('check_availability');
                if (empty($check)) {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }


        $result = true;
        $startdate = strftime('%Y-%m-%d 00:00:00', $start);
        $starttime = strftime('%H:%M', $start);
        $enddate = strftime('%Y-%m-%d 23:59:59', $end);
        $endtime = strftime('%H:%M', $end);

        $unixstart = strtotime(strftime('%Y-%m-%d ' . $starttime . ':00', $start));
        $unixend = strtotime(strftime('%Y-%m-%d ' . $endtime . ':00', $end));

        $this->error = $unixstart;
        //return false;

        $classname = 'fbuchFahrt';
        //startet eine Einheit zwischen Beginn und Ende?
        $c = $modx->newQuery($classname);
        $c->where(array(
            'deleted' => 0,
            'boot_id' => $boot_id,
            'id:!=' => $current_id));
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date), " ", start_time)) >= ' . $unixstart);
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date), " ", start_time)) < ' . $unixend);

        $c->prepare();
        $this->error = $c->toSql();

        if ($object = $modx->getObject($classname, $c)) {
            $this->errorstart = strftime('%d.%m.%Y ' . $object->get('start_time'), strtotime($object->get('date')));
            $this->errorend = strftime('%d.%m.%Y ' . $object->get('end_time'), strtotime($object->get('date_end')));
            return false;
        }

        //endet eine Einheit zwischen Beginn und Ende?
        $c = $modx->newQuery($classname);
        $c->where(array(
            'deleted' => 0,
            'boot_id' => $boot_id,
            'id:!=' => $current_id));
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date_end), " ", end_time)) > ' . $unixstart);
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date_end), " ", end_time)) <= ' . $unixend);

        $c->prepare();
        $this->error = $c->toSql();

        if ($object = $modx->getObject($classname, $c)) {
            $this->errorstart = strftime('%d.%m.%Y ' . $object->get('start_time'), strtotime($object->get('date')));
            $this->errorend = strftime('%d.%m.%Y ' . $object->get('end_time'), strtotime($object->get('date_end')));
            return false;
        }

        //startet eine Einheit vor Beginn und endet nach Ende
        $c = $modx->newQuery($classname);
        $c->where(array(
            'deleted' => 0,
            'boot_id' => $boot_id,
            'id:!=' => $current_id));
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date), " ", start_time)) < ' . $unixstart);
        $c->where('UNIX_TIMESTAMP(CONCAT(date(date_end), " ", end_time)) > ' . $unixend);

        $c->prepare();
        $this->error = $c->toSql();

        if ($object = $modx->getObject($classname, $c)) {
            $this->errorstart = strftime('%d.%m.%Y ' . $object->get('start_time'), strtotime($object->get('date')));
            $this->errorend = strftime('%d.%m.%Y ' . $object->get('end_time'), strtotime($object->get('date_end')));
            return false;
        }


        return true;

    }

    public function isguest($member_id) {
        $isguest = false;
        if ($name_o = $this->modx->getObject('mvMember', $member_id)) {
            if ($name_o->get('name') == 'Gast' && $name_o->get('firstname') == '') {
                $isguest = true;
            }
        }
        return $isguest;
    }

    public function updateTeamrowReservations($fields, $action, $classname = 'fbuchDateNames') {
        $modx = &$this->modx;
        if ($date_object = $modx->getObject('fbuchDate', $fields['date_id'])) {
            $date = $date_object->get('date');
            $start_time = $date_object->get('start_time');
            $type = $date_object->get('type');
            if ($type_object = $date_object->getOne('Type')) {
                $hooksnippet = $type_object->get('registration_hooksnippet');
            }

            if ($type == 'TeamrowingXXX') {
                $classname = 'fbuchFahrt';
                $c = $modx->newQuery($classname);
                $c->leftjoin('fbuchFahrtNames', 'Members');
                $c->where(array('Members.member_id' => $fields['member_id'], 'date_id' => $fields['date_id']));

                switch ($action) {
                    case 'add':
                        if ($object = $modx->getObject($classname, $c)) {
                            //Eintrag mit dieser Person existiert bereits
                        }
                        //Ergometer-Fahrt mit einer Person erstellen und mit Termin verknüpfen
                        if (!$object) {
                            $object = $modx->newObject($classname);
                            $object->set('date_id', $fields['date_id']);
                            $object->set('date', $date);
                            $object->set('start_time', $start_time);
                            $object->set('createdby', $modx->user->get('id'));
                            $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));

                            if ($boot = $modx->getObject('fbuchBoot', array('name' => 'Ergometer'))) {
                                $object->set('boot_id', $boot->get('id'));
                            }
                            $object->save();
                            $fahrtnam = $modx->newObject('fbuchFahrtNames');
                            $fahrtnam->set('member_id', $fields['member_id']);
                            $fahrtnam->set('fahrt_id', $object->get('id'));

                            $fahrtnam->save();
                        }
                        break;
                    case 'remove':

                        if ($object = $modx->getObject($classname, $c)) {
                            //Eintrag entfernen
                            $object->remove();
                        }

                        break;
                }
                $modx->setPlaceholder('reservation_names', $modx->getChunk('fbuchErgoterminNamen'));
            } else {
                $classname = $classname == 'ErgoterminReservierung' ? 'fbuchDateNames' : $classname;

                $c = $modx->newQuery($classname);
                $c->where(array('member_id' => $fields['member_id'], 'date_id' => $fields['date_id']));

                switch ($action) {
                    case 'add':
                        if ($object = $modx->getObject($classname, $c)) {
                            //Eintrag mit dieser Person existiert bereits
                        }
                        //Termin mit einer Person verknüpfen
                        if (!$object || $this->isguest($fields['member_id'])) {
                            $object = $modx->newObject($classname);
                            $object->set('date_id', $fields['date_id']);
                            $object->set('member_id', $fields['member_id']);
                            $object->set('createdby', $modx->user->get('id'));
                            $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                            $object->save();
                        }

                        if (!empty($hooksnippet) && $classname == 'fbuchDateNames') {
                            $properties = [];
                            $properties['object'] = $object;
                            $properties['fields'] = $fields;
                            $modx->runSnippet($hooksnippet, $properties);
                        }


                        break;
                    case 'remove':

                        if ($object = $modx->getObject($classname, $c)) {
                            //Eintrag entfernen
                            $object->remove();
                        }

                        break;
                }
                if ($classname == 'fbuchDateNames') {
                    $modx->setPlaceholder('reservation_names', $modx->getChunk('fbuchDateNamen'));
                }

            }


        }


    }

    public function handleDragDrop() {
        $modx = &$this->modx;
        $source = $modx->getOption('source', $_REQUEST, '');
        $target = $modx->getOption('target', $_REQUEST, '');
        $target_id = (int)$modx->getOption('target_id', $_REQUEST, 0);
        $datenames_id = (int)$modx->getOption('dateNames_id', $_REQUEST, 0);
        $fahrtnames_id = (int)$modx->getOption('fahrtNames_id', $_REQUEST, 0);

        switch ($source) {
            case 'dates':
                if (!empty($datenames_id) && $object = $modx->getObject('fbuchDateNames', $datenames_id)) {
                    $member_id = $object->get('member_id');
                    $guestname = $object->get('guestname');
                    $guestemail = $object->get('guestemail');
                    if ($date_o = $object->getOne('Date')) {
                        $source_date = $date_o->get('date');
                    }
                }
                break;
            case 'fahrten':
                if (!empty($fahrtnames_id) && $object = $modx->getObject('fbuchFahrtNames', $fahrtnames_id)) {
                    $member_id = $object->get('member_id');
                    $guestname = $object->get('guestname');
                    $guestemail = $object->get('guestemail');                    
                    if ($fahrt_o = $object->getOne('Fahrt')) {
                        $source_date = $fahrt_o->get('date');
                        $source_locked = $fahrt_o->get('locked');
                        if ($source_locked) {
                            return false;
                        }
                    }
                }
                break;
        }


        switch ($target) {
            case 'fahrten':
                $classname = 'fbuchFahrtNames';
                if ($target_o = $modx->getObject('fbuchFahrt', $target_id)) {
                    $target_date = $target_o->get('date');
                    $target_locked = $target_o->get('locked');
                    if ($target_locked) {
                        return false;
                    }
                }
                //get max pos
                $pos = 0;
                $c = $modx->newQuery($classname);
                $c->where(array('fahrt_id' => $target_id));
                $c->sortby('pos', 'DESC');
                $c->limit('1');
                $c->prepare();
                //echo $c->toSql();
                if ($object = $modx->getObject($classname, $c)) {
                    $pos = $object->get('pos');
                }
                $pos++;

                if ($target_date == $source_date) {
                    switch ($source) {
                        case 'fahrten':
                            if ($member_id != 0 && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'member_id' => $member_id))) {
                                //name exists allready in fahrt, do nothing
                            } elseif ($member_id == 0 && !empty($guestname) && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'guestname' => $guestname))) {
                                //name exists allready in fahrt, do nothing
                            }else {
                                if ($object = $modx->getObject($classname, $fahrtnames_id)) {
                                    $object->set('fahrt_id', $target_id);
                                    $object->set('pos', $pos);
                                    $object->set('cox',0);
                                    $object->set('obmann',0);
                                    $object->set('guestname',$guestname);
                                    $object->set('guestemail',$guestemail);                                    
                                    $object->save();
                                }
                            }
                            break;
                        case 'dates':
                            if ($member_id != 0 && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'member_id' => $member_id))) {
                                //name exists allready in fahrt, do nothing
                            } elseif ($member_id == 0 && !empty($guestname) && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'guestname' => $guestname))) {
                                //name exists allready in fahrt, do nothing
                            } else {
                                $object = $modx->newObject($classname);
                                $object->set('fahrt_id', $target_id);
                                $object->set('member_id', $member_id);
                                $object->set('pos', $pos);
                                $object->set('datenames_id', $datenames_id);
                                $object->set('createdby', $modx->user->get('id'));
                                $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                                $object->set('guestname',$guestname);
                                $object->set('guestemail',$guestemail);                                  
                                $object->save();
                            }
                            break;
                    }
                }


                break;
            case 'dates':
                $classname = 'fbuchDateNames';
                if ($target_o = $modx->getObject('fbuchDate', $target_id)) {
                    $target_date = $target_o->get('date');
                }
                if ($target_date == $source_date) {
                    switch ($source) {
                        case 'fahrten':
                            if ($object = $modx->getObject($classname, $datenames_id)) {
                                $object->set('date_id', $target_id);
                                $object->save();
                            } else {
                                if (!$this->isguest($member_id) && $object = $modx->getObject($classname, array('date_id' => $target_id, 'member_id' => $member_id))) {

                                } else {
                                    $object = $modx->newObject($classname);
                                    $object->set('date_id', $target_id);
                                    $object->set('member_id', $member_id);
                                    $object->set('createdby', $modx->user->get('id'));
                                    $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                                    $object->save();
                                }
                            }

                            if ($object = $modx->getObject('fbuchFahrtNames', $fahrtnames_id)) {
                                $object->remove();
                            }

                            break;
                        case 'dates':
                            if (!$this->isguest($member_id) && $object = $modx->getObject($classname, array('date_id' => $target_id, 'member_id' => $member_id))) {

                            } else {
                                if ($object = $modx->getObject($classname, $datenames_id)) {
                                    $object->set('date_id', $target_id);
                                    $object->save();
                                }
                            }

                            break;
                    }
                }
                break;
        }

    }

    public function forceObmann($fahrt_id){
        $classname = 'fbuchFahrtNames';
        if ($object = $this->modx->getObject($classname,['fahrt_id'=>$fahrt_id,'obmann'=>1])){
            
        } else {
            if ($object = $this->modx->getObject($classname,['fahrt_id'=>$fahrt_id])){
                $object->set('obmann',1);
                $object->save();
            }
        }
    }

    public function setObmann($fields){
        $modx = &$this->modx;
        $classname = 'fbuchFahrtNames';
        $fahrtnames_id = $this->modx->getOption('fahrtnames_id',$fields,0);
        if ($collection = $modx->getCollection($classname, array('fahrt_id' => $fields['fahrt_id']))) {
            foreach ($collection as $object) {
                $object->set('obmann', 0);
                $object->save();
            }
        }
        if ($object = $modx->getObject($classname, $fahrtnames_id)) {
            
            $object->set('obmann', '1');
            $object->save();
        }
    }

    public function setCox($fields){
        $modx = &$this->modx;
        $classname = 'fbuchFahrtNames';
        $fahrtnames_id = $this->modx->getOption('fahrtnames_id',$fields,0);
        if ($object = $modx->getObject($classname, $fahrtnames_id)) {
            $cox = $object->get('cox');
        }         
        if ($collection = $modx->getCollection($classname, array('fahrt_id' => $fields['fahrt_id']))) {
            foreach ($collection as $object) {
                $object->set('cox', 0);
                $object->save();
            }
        }
        if ($object = $modx->getObject($classname, $fahrtnames_id)) {
            $object->set('cox', $cox == 1 ? 0 : 1);
            $object->save();
        }    
    }

    public function updateFahrtNames($fields, $action) {
        $modx = &$this->modx;
        $classname = 'fbuchFahrtNames';
        if (isset($fields['id']) && $fahrtname_o = $modx->getObject($classname, $fields['id'])) {
            $fields['fahrt_id'] = $fahrtname_o->get('fahrt_id');
        }
        unset($fields['id']);
        if ($object = $modx->getObject('fbuchFahrt', $fields['fahrt_id'])) {
            $locked = $object->get('locked');
            if ($locked) {
                return false;
            }
        }
        switch ($action) {
            case 'add':
                $name = '';
                if ($object = $modx->getObject($classname, $fields)) {
                    if ($name_o = $object->getOne('Member')) {
                        $name = $name_o->get('name') . $name_o->get('firstname');
                    }
                }
                if (!$object || $name == 'Gast') {
                    $object = $modx->newObject($classname);
                    $object->fromArray($fields);
                    $object->save();
                }
                break;
            case 'remove':
                if ($object = $modx->getObject($classname, $fields)) {
                    $object->remove();
                } else {

                }
                break;
            case 'setobmann':
                $this->setObmann($fields); 
                break;

            case 'setCox':
                $this->setCox($fields);
                break;
        }
    }

    public function sendInviteMail($invite_id, $comment = '', $comment_name = '', $add_datecomment = false, $subj_prefix = '') {
        $modx = &$this->modx;

        if ($object = $modx->getObject('fbuchDateInvited', array('id' => $invite_id))) {

        } else {
            return '';
        }

        $subj_prefix = empty($subj_prefix) ? $this->getChunk('fbuch_invite_subject_prefix') : $subj_prefix;

        $modx->runSnippet('setlocale');
        $name_o = $object->getOne('Member');
        $date_o = $object->getOne('Date');
        $email = $name_o->get('email');

        $member_id = 0;//Todo: try to get and set currently logged in mv_Member here? 
        if ($add_datecomment && !empty($comment)) {
            $this->addDateComment($comment,$object->get('date_id'),$member_id);
        }

        if (!empty($email) && $object && $name_o && $date_o) {
            $properties = array_merge($name_o->toArray(), $date_o->toArray(), $object->toArray());
            $properties['Comment_name'] = $comment_name;
            $properties['status'] = 'invited';
            if (!empty($properties['canceled'])) {
                $properties['status'] = 'canceled';
            }

            if ($modx->getObject('fbuchDateNames', array('date_id' => $properties['date_id'], 'member_id' => $properties['member_id']))) {
                $properties['status'] = 'accepted';
            }

            $properties['comment'] = $comment;
            $properties['iid'] = $object->get('id');
            $properties['email'] = $email;
            $properties['tpl'] = $this->getChunkName('fbuch_invite_mail_tpl');
            $properties['subject'] = $subj_prefix . ': ' . $date_o->get('title') . ' ' . strftime('%a, %d.%m.%Y ', strtotime($date_o->get('date'))) . $date_o->get('start_time');
            $properties['code'] = md5($properties['date_id'] . $properties['email'] . $properties['iid']);
            //print_r($properties);die();
            //$values = $hook->getValues();
            $success = $this->sendMail($properties);
            if ($success) {
                $object->set('invited', 1);
                $object->save();
            }
            return $success;

        }

    }

    public function getChunk($name, $properties = array()) {
        return $this->modx->getChunk($this->getChunkName($name), $properties);
    }

    public function getChunkName($name) {
        $custom_name = 'custom_' . $name;
        if ($this->modx->getObject('modChunk', array('name' => $custom_name))) {
            $name = $custom_name;
        }

        return $name;
    }

    public function berechneBeitrag($scriptProperties) {
        $modx = $this->modx;

        $beitrags_teilung = (float)$modx->getChunk($this->getChunkName('mv_sepa_beitrags_teilung'), []);
        $alter = $this->berechneAlter($scriptProperties);
        //$alter = $modx->runSnippet('mv_berechne_alter', $scriptProperties);
        $typ = $modx->getOption('typ', $scriptProperties, '');
        $output = $modx->getOption('default', $scriptProperties, '');

        if ($typ == '1') {
            $c = $modx->newQuery('mvBeitragstyp');

            $c->where(array(
                'max_age:!=' => '0',
                'max_age:>=' => $alter,
                ''));
            $c->sortby('max_age');
            $c->limit('1');

            if ($object = $modx->getObject('mvBeitragstyp', $c)) {
                $output = $object->get('beitrag');
            }
        }
        //return round($output/4);
        return round($output / $beitrags_teilung);
    }

    public function calculateAverage($values, & $error){
        $output = 0;
        $values = is_array($values) ? $values : explode(',', $values);
        $valuesCount = count($values);
        $sum = 0;
        
        if ($valuesCount > 0) {
            $countedValues = 0;
            foreach ($values as $value) {
                $trimmedValue = trim($value);
                if (is_numeric($trimmedValue) && $trimmedValue > 0) {
                    $countedValues++;
                    $sum += $trimmedValue;
                }
            }
            if ($valuesCount == $countedValues) {
                $output = $sum / $countedValues;
            } else {
                $missingValueCount = $valuesCount - $countedValues;
                $error = ['missingValues' => $missingValueCount];
            }
        }
        
        return $output;        
    }

    public function berechneAlter($scriptProperties) {
        $modx = $this->modx;

        $birthdate = strtotime($modx->getOption('birthdate', $scriptProperties, 0));
        $when = $modx->getOption('when', $scriptProperties, '');
        $alter = '';

        if (!empty($birthdate)) {
            switch ($when) {
                case 'thisyear':
                    $alter = strftime('%Y') - strftime('%Y', $birthdate);
                    break;
                default:
                    $when = strtotime($when);

                    $day = date("d", $birthdate);
                    $month = date("m", $birthdate);
                    $year = date("Y", $birthdate);

                    $cur_day = date("d", $when);
                    $cur_month = date("m", $when);
                    $cur_year = date("Y", $when);

                    $calc_year = $cur_year - $year;

                    if ($month > $cur_month)
                        $alter = $calc_year - 1;
                    elseif ($month == $cur_month && $day > $cur_day)
                        $alter = $calc_year - 1;
                    else
                        $alter = $calc_year;

                    break;

            }
        }

        return $alter;

    }

    public function getNow(){
        return new DateTime(null, new DateTimeZone('Europe/Berlin'));
    } 
    
    public function getOtpExpireson($otp_createdon){
        $date = new DateTime($otp_createdon, new DateTimeZone('Europe/Berlin'));
        date_add($date,date_interval_create_from_date_string("24 hours"));
        return $date;
    }

    public function generateOTP(){
        $password = "";
        for($i=0;$i<50;$i++) {
            $password .= chr( (mt_rand(1, 36) <= 26) ? mt_rand(97, 122) : mt_rand(48, 57 ));
        } 
        return $password;        
    } 
    
    public function loginByInvite($iid,$code){
        $modx = &$this->modx;
        if ($invite_o = $modx->getObject('fbuchDateInvited', $iid)) {
            if ($date_o = $invite_o->getOne('Date')){
                $date = $date_o->get('date_end'); 
                $now = new DateTime(null, new DateTimeZone('Europe/Berlin'));
                $today = date_format($now,'Y-m-d');
                $date = substr($date,0,10);
                if ($date < $today) {
                    return 'Zugang nicht möglich. Der Termin dieses Einladungslinks liegt in der Vergangenheit.';
                }
    
            }
            $date_id = $invite_o->get('date_id');
            $member_id = $invite_o->get('member_id');
            if ($name_o = $invite_o->getOne('Member')) {
                $email = $this->getNameEmail($name_o);
                $code_matches = ($date_id && $email && $code == md5($date_id . $email . $iid)) ? true : false;    
    
                if ($code_matches && $user = $this->createUserFromMember($member_id,1)){
                    $rawResponse = $this->login($user);
                    $modx->sendRedirect('/termine/#/' . $date_id . '/anmeldung');          
                }
            }
        } 
        return false; 
    }

    public function loginByOtp($mid,$code){
        $modx = &$this->modx;
        if ($member = $modx->getObject('mvMember',$mid)){
            $otp = $member->get('otp');
            $otp_createdon = $member->get('otp_createdon');
           
            if (!empty($otp && !empty($otp_createdon))){
                $otp_expireson = $this->getOtpExpireson($otp_createdon);
                $now = $this->getNow();                
                $dateDifference = ($otp_expireson->getTimestamp() - $now->getTimestamp()) / 60;
                if ($dateDifference < 0) {
                    return 'Login nicht möglich. Der Login Link ist nicht mehr gültig.';
                }
                if ($otp == $code && $user = $this->createUserFromMember($mid,1)){
                    $rawResponse = $this->login($user);
                    $modx->sendRedirect('/fahrtenbuch/fahrtenbuch.html');       
                }                  
            }
        } 
        return false;   
    }

    public function login($user){
        $this->authenticated = true;
        $properties = array(
            'login_context' => 'fbuch',
            //'add_contexts'  => $this->getProperty('contexts',''),
            'username'      => $user->get('username'),
            'password'      => 'password',
            'returnUrl'     => '/',
            'rememberme'    => false
        );
        $rawResponse = $this->modx->runProcessor('security/login', $properties); 
        return $rawResponse;       
    }    

    public function sendLoginMail($member_id){
        $allowed = $this->userCouldBeCreated($member_id);
        if (!$allowed){
            return false;
        }
        $classname = 'mvMember';
        if ($object = $this->modx->getObject($classname,['id'=>$member_id])){
            /*
            $activated = $object->get('activated');
            if (empty($activated)){
                return false;
            }
            */
            $otp = $this->generateOTP();
            $now = $this->getNow();
            $object->set('otp',$otp);
            $object->set('otp_createdon',date_format($now,'Y-m-d H:i:s'));
            $object->save();

            $properties = $object->toArray();
            $otp_expireson = $this->getOtpExpireson($object->get('otp_createdon'));
            $properties['otp_expireson'] = date_format($otp_expireson,'d.m.Y H:i');
            $properties['tpl'] = 'fbuch_emailLogin';
            $properties['code'] = $object->get('otp');
            $properties['subject'] = 'Dein Login Link';
            return $this->sendMail($properties);
        }
        return false;            
    }

    public function sendMails($recipients, $properties = array()) {
        foreach ($recipients as $recipient) {
            $this->sendMail($properties);
        }
    }

    public function sendMail($properties = array()) {

        $modx = &$this->modx;

        $mailTo = $properties['email'];
        //$mailTo = 'b.perner@gmx.de';

        $tpl = $properties['tpl'];
        //$properties = $this->toArray();

        $message = $modx->getChunk($tpl, $properties);
        $subject = $properties['subject'];


        $emailFromAddress = $modx->getOption('emailsender');
        $emailFromName = $modx->getOption('site_name');

        $modx->getService('mail', 'mail.modPHPMailer');
        $modx->mail->set(modMail::MAIL_BODY, $message);
        $modx->mail->set(modMail::MAIL_FROM, $emailFromAddress);
        $modx->mail->set(modMail::MAIL_FROM_NAME, $emailFromName);
        $modx->mail->set(modMail::MAIL_SENDER, $emailFromAddress);
        $modx->mail->set(modMail::MAIL_SUBJECT, $subject);

        $mailTo = explode(',', $mailTo);

        if (is_array($mailTo)) {
            foreach ($mailTo as $email) {
                $modx->mail->address('to', $email);
            }
        }

        $modx->mail->address('reply-to', $emailFromAddress);
        $modx->mail->setHTML(true);

        $success = $modx->mail->send();
        if (!$success) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: Serienmail');
        }

        $modx->mail->reset();

        return $success;
    }

    function date_round(\DateTime $dt, $precision = 15) {
        $s = $precision * 60;
        $dt->setTimestamp($s * (int) ceil($dt->getTimestamp() / $s));
        return $dt;
    }    

}
