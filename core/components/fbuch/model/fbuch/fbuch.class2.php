<?php

/**
 * Fbuch
 *
 * Copyright 2017 by Bruno Perner <b.perner@gmx.de>
 *
 * @package fbuch
 * @subpackage classfile
 */

class Fbuch
{
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


    /**
     * Fbuch constructor
     *
     * @param modX $modx A reference to the modX instance.
     * @param array $options An array of options. Optional.
     */
    function __construct(modX & $modx, array $options = array())
    {
        $this->modx = &$modx;

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
    public function getOption($key, $options = array(), $default = null, $skipEmpty = false)
    {
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

    public function checkPermission($permission, $properties = array())
    {
        $object_id = $this->modx->getOption('object_id', $properties);
        $classname = $this->modx->getOption('classname', $properties);
        $result = true;
        switch ($permission) {
            case 'fbuch_edit_old_entries':
                if (!$this->modx->hasPermission($permission)) {
                    if (!empty($object_id) && $object = $this->modx->getObject($classname, $object_id)) {
                        $km = $object->get('km');
                        $date = strftime('%Y%m%d', strtotime($object->get('date')));
                        $today = strftime('%Y%m%d');
                        if (!empty($km) && $date < $today) {
                            $this->error('Du bist nicht berechtigt, abgeschlossene Einträge aus der Vergangenheit zu bearbeiten');
                            $result = false;
                        }
                    }
                }
                break;
            default:
                if (!$this->modx->hasPermission($permission)) {
                    $this->error($permission . 'Du hast keine Berechtigung für diesen Vorgang');
                    $result = false;
                }
        }
        return $result;
    }


    public function error($error)
    {
        $this->modx->setPlaceholder('fbucherror', '<div class="col-sm-12"><div class="alert alert-danger" role="alert">' . $error . '</div></div>');
    }

    public function lockFahrt()
    {
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

    public function getNameEmail($object)
    {
        $email = '';
        if ($object) {
            $email = $object->get('email');
            //try to get email from MV

            $member_id = $object->get('mv_member_id');
            if ($member = $this->modx->getObject('mvMember', $member_id)) {
                $email = $member->get('email');
            }
        }
        return $email;
    }

    public function cancelAcceptInvite($scriptProperties)
    {
        $modx = &$this->modx;
        $action = $modx->getOption('process', $_REQUEST, '');
        $code = $modx->getOption('code', $_REQUEST, '');
        $iid = $modx->getOption('iid', $_REQUEST, '');
        $comment = $modx->getOption('comment', $_REQUEST, '');
        $mail_comment = $modx->getOption('mail_comment', $_REQUEST, '');
        $date_id = $modx->getOption('date_id', $_REQUEST, false);

        $email = false;
        $remove_comment = (int)$modx->getOption('remove_comment', $_REQUEST, '');
        $action = !empty($remove_comment) ? 'remove_comment' : $action;

        if ($invite_o = $modx->getObject('fbuchDateInvited', $iid)) {
            $date_id = $invite_o->get('date_id');
            $name_id = $invite_o->get('name_id');
            if ($name_o = $invite_o->getOne('Name')) {
                $email = $this->getNameEmail($name_o);
            }
        }

        //try to get invite of the current user or create an invite for him, if he is calling this page
        if ($date_id && empty($name_id)) {
            if ($modx->user->ismember('fbuch')) {
                if ($modx->user->Profile->get('fullname') != 'rgm' && $name_o = $modx->getObject('fbuchNames', array('modx_user_id' => $modx->user->get('id')))) {
                    $name_id = $name_o->get('id');
                    $email = $this->getNameEmail($name_o);
                    if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'name_id' => $name_id))) {

                    } else {
                        $invite_o = $modx->newObject('fbuchDateInvited');
                        $invite_o->set('date_id',$date_id);
                        $invite_o->set('name_id',$name_id);
                        $invite_o->save();
                        
                    }
                    $_REQUEST['iid'] = $iid = $invite_o->get('id');
                    $_REQUEST['code'] = $code = md5($date_id . $email . $iid); 
                }
            }
        }

        if ($date_id && $email && $code == md5($date_id . $email . $iid)) {
            $fields = $invite_o->toArray();
            switch ($action) {
                case 'accept':
                    $invite_o->set('canceled', 0);
                    $invite_o->save();
                    $this->updateTeamrowReservations($fields, 'add');
                    $this->sendInviteMail($invite_o);
                    $comment = empty($comment) ? 'Zusage' : $comment;
                    break;
                case 'cancel':
                    $invite_o->set('canceled', 1);
                    $invite_o->save();
                    $this->updateTeamrowReservations($fields, 'remove');
                    $this->sendInviteMail($invite_o);
                    $comment = empty($comment) ? 'Absage' : $comment;
                    break;
                case 'remove_comment':
                    if ($comment_o = $modx->getObject('fbuchDateComment', $remove_comment)) {
                        if ($name_id == $comment_o->get('name_id')) {
                            $comment_o->remove();
                        }
                    }
                    break;
            }
            if (!empty($comment)) {
                $comment_o = $modx->newObject('fbuchDateComment');
                $comment_o->set('date_id', $date_id);
                $comment_o->set('name_id', $name_id);
                $comment_o->set('comment', $comment);
                $comment_o->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                $comment_o->save();
                $comment_name = $name_o->get('firstname') . ' ' . $name_o->get('lastname');
                $this->sendCommentMails($comment, $mail_comment, $comment_name, $date_id);

            }


        } else {
            $this->error('Leider konnten wir keine passenden Daten zu Deiner Anfrage finden.');
        }

        return '';

    }

    public function sendCommentMails($comment, $mail_comment, $comment_name, $date_id)
    {
        $modx = &$this->modx;
        $classname = '';
        switch ($mail_comment) {
            case 'accepted':
                $classname = 'fbuchDateNames';
                break;
            case 'invited':
                $classname = 'fbuchDateInvited';
                break;
        }
        if (!empty($classname)) {
            $c = $modx->newQuery($classname);
            $c->where(array('date_id' => $date_id));
            if ($collection = $modx->getCollection($classname, $c)) {
                foreach ($collection as $object) {
                    $name_id = $object->get('name_id');
                    if ($invite_o = $modx->getObject('fbuchDateInvited', array('date_id' => $date_id, 'name_id' => $name_id))) {

                    } else {
                        $invite_o = $modx->newObject('fbuchDateInvited');
                        $invite_o->set('name_id', $name_id);
                        $invite_o->set('date_id', $date_id);
                        $invite_o->save();
                    }

                    $this->sendInviteMail($invite_o, $comment, $comment_name);
                }
            }

        }


    }


    public function update(&$hook, $scriptProperties)
    {
        $modx = &$this->modx;

        $classname = $modx->getOption('classname', $scriptProperties, '');
        $processproperty = $modx->getOption('processproperty', $scriptProperties, '');
        $grid_id = isset($_GET['grid_id']) ? $_GET['grid_id'] : '';
        $action = $modx->getOption('processaction', $_REQUEST, '');
        $type = $modx->getOption('type', $_REQUEST, '');
        $return = $modx->getOption('return', $_REQUEST, 'form');

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
                if ($action == 'mail_invite') {

                    $object_id = $hook->getValue('object_id');
                    $comment = $hook->getValue('comment');
                    if ($object = $modx->getObject($classname, $object_id)) {
                        $comment_name = '[[+modx.user.id:userinfo=`fullname`]]';
                        $this->sendInviteMail($object, $comment, $comment_name, true);
                        $modx->setPlaceholder('success_object_id', $object->get('date_id'));
                    }

                    return true;
                }
                if ($action == 'mail_invites') {

                    $object_id = $hook->getValue('object_id');
                    $comment = $hook->getValue('comment');
                    $c = $modx->newQuery($classname);
                    $c->where(array('date_id' => $object_id));
                    $add_datecomment = true;
                    if ($collection = $modx->getCollection($classname, $c)) {
                        foreach ($collection as $object) {
                            $comment_name = '[[+modx.user.id:userinfo=`fullname`]]';
                            $this->sendInviteMail($object, $comment, $comment_name, $add_datecomment);
                            $add_datecomment = false;
                        }
                    }
                    $modx->setPlaceholder('success_object_id', $object_id);
                    return true;
                }

            case 'ErgoterminReservierung':
                $fields['date_id'] = $modx->getOption('fahrt_id', $_REQUEST, '');
                $fields['name_id'] = $modx->getOption('name_id', $_REQUEST, '');
                $this->updateTeamrowReservations($fields, $action, $classname);
                if (!empty($action)) {

                }

                return '';
                break;
            case 'fbuchFahrtNames':

                $fields['fahrt_id'] = $modx->getOption('fahrt_id', $_REQUEST, '');
                $fields['name_id'] = $modx->getOption('name_id', $_REQUEST, '');
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

                            if ($name_o = $object->getOne('Name')) {
                                $name = $name_o->get('firstname') . ' ' . $name_o->get('lastname');
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
                }
                break;

            case 'fbuchDate':
                if ($processproperty == 'cancel_accept') {
                    $this->cancelAcceptInvite($scriptProperties);
                    return '';
                }

            default:
                $object_id = $hook->getValue('object_id');
                $values = $hook->getValues();
                $duplicate = !empty($values['duplicate']) ? true : false;
                $duplicate_names = !empty($values['duplicate_names']) ? true : false;
                $duplicate_invited = !empty($values['duplicate_invited']) ? true : false;

                if (!$duplicate && !empty($object_id) && $object = $modx->getObject($classname, $object_id)) {

                    switch ($classname) {
                        case 'fbuchNames':
                            if (!$this->checkPermission('fbuch_edit_names')) {
                                return false;
                            }
                            break;
                    }

                } else {
                    $object = $modx->newObject($classname);
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

                if ($type == 'Rudern' && $classname == 'fbuchDate' && $duplicate && $duplicate_names) {
                    if (!empty($object_id) && $old_object = $modx->getObject($classname, $object_id)) {
                        if ($names = $old_object->getMany('Names')) {
                            foreach ($names as $name) {
                                $new_name = $modx->newObject('fbuchDateNames');
                                $new_name->fromArray($name->toArray());
                                $new_name->set('date_id', $object->get('id'));
                                $new_name->save();
                            }
                        }
                    }
                }
                if ($type == 'Rudern' && $classname == 'fbuchDate' && $duplicate && $duplicate_invited) {
                    if (!empty($object_id) && $old_object = $modx->getObject($classname, $object_id)) {
                        if ($names = $old_object->getMany('Invited')) {
                            foreach ($names as $name) {
                                $new_name = $modx->newObject('fbuchDateInvited');
                                $new_name->fromArray($name->toArray());
                                $new_name->set('canceled', 0);
                                $new_name->set('invited', 0);
                                $new_name->set('date_id', $object->get('id'));
                                $new_name->save();
                            }
                        }
                    }
                }
                $modx->setPlaceholder('success_object_id', $object->get('id'));
                break;

        }


        return true;
    }

    public function getFormValues(&$hook, $scriptProperties)
    {
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
        if ($process == 'mail_invites') {
            $c = $modx->newQuery($classname);
            $c->where(array('date_id' => $object_id));
            $names = array();
            if ($collection = $modx->getCollection($classname, $c)) {
                foreach ($collection as $object) {
                    $name_o = $object->getOne('Name');
                    if ($name_o) {
                        $email = $this->getNameEmail($name_o);
                        if (!empty($email)) {
                            $names[] = $name_o->get('firstname') . ' ' . $name_o->get('lastname');
                        }
                    }
                }
            }

            $values['names'] = implode(',', $names);
            $hook->setValues($values);
            return true;
        }

        if (!empty($object_id) && $object = $modx->getObject($classname, $object_id)) {

            $values = $object->toArray();
            switch ($classname) {
                case 'fbuchFahrt':
                    $this->checkPermission('fbuch_edit_old_entries', array('classname' => 'fbuchFahrt', 'object_id' => $object_id));
                    if ($fahrtname = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $values['id']))) {
                        if ($name = $fahrtname->getOne('Name')) {
                            $values['name'] = $name->get('lastname') . ' ' . $name->get('firstname');
                            $values['name_id'] = $name->get('id');
                        }
                    }
                    break;
                case 'fbuchNames':
                    $this->checkPermission('fbuch_edit_names', array('classname' => 'fbuchFahrt', 'object_id' => $object_id));
                    break;
                case 'fbuchDate':
                    //$this->checkPermission('edit_old_fbuchentries',array('classname'=>'fbuchFahrt', 'object_id'=>$object_id));
                    if ($name = $object->getOne('Name')) {
                        $values['instructor_name'] = $name->get('lastname') . ' ' . $name->get('firstname');
                        //$values['name_id'] = $name->get('id');
                    }

                    break;
                case 'fbuchDateInvited':
                case 'fbuchDateNames':
                    //$this->checkPermission('fbuch_edit_names', array('classname' => 'fbuchFahrt', 'object_id' => $object_id));
                    if ($name = $object->getOne('Name')) {
                        $name_values = $name->toArray();
                        foreach ($name_values as $key => $value) {
                            $values['Name_' . $key] = $value;
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

            $hook->setValues($values);
        }

        //print_r($values);die();
        return true;

    }

    public function updateFahrt($object_id, $values, $grid_id)
    {
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

        } else {
            $object = $modx->newObject($classname);
        }

        if (isset($values['km'])) {
            $values['km'] = str_replace(',', '.', $values['km']);
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

        if (isset($values['name_ids'])) {
            $name_ids = explode(',', $values['name_ids']);
            foreach ($name_ids as $key => $name_id) {
                if (!empty($name_id)) {
                    $fahrtnam = $modx->newObject('fbuchFahrtNames');
                    $fahrtnam->set('name_id', $name_id);
                    $fahrtnam->set('fahrt_id', $object->get('id'));
                    $fahrtnam->save();
                }
            }
        }

        if ($grid_id == 'Ergofahrten' && !empty($values['name_id'])) {
            if ($fahrtnam = $modx->getObject('fbuchFahrtNames', array('fahrt_id' => $object->get('id')))) {
                $fahrtnam->set('name_id', $values['name_id']);
            } else {
                $fahrtnam = $modx->newObject('fbuchFahrtNames');
                $fahrtnam->set('name_id', $values['name_id']);
                $fahrtnam->set('fahrt_id', $object->get('id'));
            }
            $fahrtnam->save();
        }

        return $object;
    }

    public function isguest($name_id)
    {
        $isguest = false;
        if ($name_o = $this->modx->getObject('fbuchNames', $name_id)) {
            if ($name_o->get('lastname') == 'Gast') {
                $isguest = true;
            }
        }
        return $isguest;
    }

    public function updateTeamrowReservations($fields, $action, $classname = 'fbuchDateNames')
    {
        $modx = &$this->modx;
        if ($date_object = $modx->getObject('fbuchDate', $fields['date_id'])) {
            $date = $date_object->get('date');
            $start_time = $date_object->get('start_time');
            $type = $date_object->get('type');

            if ($type == 'Teamrowing') {
                $classname = 'fbuchFahrt';
                $c = $modx->newQuery($classname);
                $c->leftjoin('fbuchFahrtNames', 'Names');
                $c->where(array('Names.name_id' => $fields['name_id'], 'date_id' => $fields['date_id']));

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
                            $fahrtnam->set('name_id', $fields['name_id']);
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
                $c->where(array('name_id' => $fields['name_id'], 'date_id' => $fields['date_id']));

                switch ($action) {
                    case 'add':
                        if ($object = $modx->getObject($classname, $c)) {
                            //Eintrag mit dieser Person existiert bereits
                        }
                        //Termin mit einer Person verknüpfen
                        if (!$object || $this->isguest($fields['name_id'])) {
                            $object = $modx->newObject($classname);
                            $object->set('date_id', $fields['date_id']);
                            $object->set('name_id', $fields['name_id']);
                            $object->set('createdby', $modx->user->get('id'));
                            $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                            $object->save();
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

    public function handleDragDrop()
    {
        $modx = &$this->modx;
        $source = $modx->getOption('source', $_REQUEST, '');
        $target = $modx->getOption('target', $_REQUEST, '');
        $target_id = (int)$modx->getOption('target_id', $_REQUEST, 0);
        $datenames_id = (int)$modx->getOption('dateNames_id', $_REQUEST, 0);
        $fahrtnames_id = (int)$modx->getOption('fahrtNames_id', $_REQUEST, 0);

        switch ($source) {
            case 'dates':
                if (!empty($datenames_id) && $object = $modx->getObject('fbuchDateNames', $datenames_id)) {
                    $name_id = $object->get('name_id');
                    if ($date_o = $object->getOne('Date')) {
                        $source_date = $date_o->get('date');
                    }
                }
                break;
            case 'fahrten':
                if (!empty($fahrtnames_id) && $object = $modx->getObject('fbuchFahrtNames', $fahrtnames_id)) {
                    $name_id = $object->get('name_id');
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
                echo $c->toSql();
                if ($object = $modx->getObject($classname, $c)) {
                    $pos = $object->get('pos');
                }
                $pos++;

                if ($target_date == $source_date) {
                    switch ($source) {
                        case 'fahrten':
                            if (!$this->isguest($name_id) && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'name_id' => $name_id))) {
                                //name exists allready in fahrt, do nothing
                            } else {
                                if ($object = $modx->getObject($classname, $fahrtnames_id)) {
                                    $object->set('fahrt_id', $target_id);
                                    $object->set('pos', $pos);
                                    $object->save();
                                }
                            }
                            break;
                            break;
                        case 'dates':
                            if (!$this->isguest($name_id) && $object = $modx->getObject($classname, array('fahrt_id' => $target_id, 'name_id' => $name_id))) {
                                //name exists allready in fahrt, do nothing
                            } else {
                                $object = $modx->newObject($classname);
                                $object->set('fahrt_id', $target_id);
                                $object->set('name_id', $name_id);
                                $object->set('pos', $pos);
                                $object->set('datenames_id', $datenames_id);
                                $object->set('createdby', $modx->user->get('id'));
                                $object->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
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
                                if (!$this->isguest($name_id) && $object = $modx->getObject($classname, array('date_id' => $target_id, 'name_id' => $name_id))) {

                                } else {
                                    $object = $modx->newObject($classname);
                                    $object->set('date_id', $target_id);
                                    $object->set('name_id', $name_id);
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
                            if (!$this->isguest($name_id) && $object = $modx->getObject($classname, array('date_id' => $target_id, 'name_id' => $name_id))) {

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

    public function updateFahrtNames($fields, $action)
    {
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
                $lastname = '';
                if ($object = $modx->getObject($classname, $fields)) {
                    if ($name_o = $object->getOne('Name')) {
                        $lastname = $name_o->get('lastname');
                    }
                }
                if (!$object || $lastname == 'Gast') {
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


                if ($collection = $modx->getCollection($classname, array('fahrt_id' => $fields['fahrt_id']))) {
                    foreach ($collection as $object) {
                        $object->set('obmann', 0);
                        $object->save();
                    }
                }
                if ($fahrtname_o) {
                    $fahrtname_o->set('obmann', 1);
                    $fahrtname_o->save();
                } elseif ($object = $modx->getObject($classname, $fields)) {
                    $object->set('obmann', 1);
                    $object->save();
                } else {

                }
                break;
        }
    }

    public function sendInviteMail(&$object, $comment = '', $comment_name = '', $add_datecomment = false)
    {
        $modx = &$this->modx;
        $modx->runSnippet('setlocale');
        $name_o = $object->getOne('Name');
        $date_o = $object->getOne('Date');
        $email = $this->getNameEmail($name_o);

        if ($add_datecomment && !empty($comment)) {
            $comment_o = $modx->newObject('fbuchDateComment');
            $comment_o->set('date_id', $object->get('date_id'));
            $comment_o->set('createdby', $modx->user->get('id'));
            $comment_o->set('comment', $comment);
            $comment_o->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            $comment_o->save();
        }


        if (!empty($email) && $object && $name_o && $date_o) {
            $properties = array_merge($name_o->toArray(), $date_o->toArray(), $object->toArray());
            $properties['Comment_name'] = $comment_name;
            $properties['status'] = 'invited';
            if (!empty($properties['canceled'])) {
                $properties['status'] = 'canceled';
            }

            if ($modx->getObject('fbuchDateNames', array('date_id' => $properties['date_id'], 'name_id' => $properties['name_id']))) {
                $properties['status'] = 'accepted';
            }

            $properties['comment'] = $comment;
            $properties['iid'] = $object->get('id');
            $properties['email'] = $email;
            $properties['tpl'] = 'fbuch_invite_mail_tpl';
            $properties['subject'] = 'RGM Einladung: ' . $date_o->get('title') . ' ' . strftime('%a, %d.%m.%Y', strtotime($date_o->get('date')));
            $properties['code'] = md5($properties['date_id'] . $properties['email'] . $properties['iid']);
            //print_r($properties);
            //$values = $hook->getValues();
            $this->sendMail($properties);
            $object->set('invited', 1);
            $object->save();

        }

    }

    public function sendMails($recipients, $properties = array())
    {
        foreach ($recipients as $recipient) {
            $this->sendMail($properties);
        }
    }

    public function sendMail($properties = array())
    {

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


    }

}
