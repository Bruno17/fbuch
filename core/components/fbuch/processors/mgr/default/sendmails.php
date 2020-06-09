<?php

$config = $modx->migx->customconfigs;

$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);

    if (count($packageNames) == '1') {
        //for now connecting also to foreign databases, only with one package by default possible
        $xpdo = $modx->migx->getXpdoInstanceAndAddPackage($config);
    } else {
        //all packages must have the same prefix for now!
        foreach ($packageNames as $packageName) {
            $packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
            $modelpath = $packagepath . 'model/';
            if (is_dir($modelpath)) {
                $modx->addPackage($packageName, $modelpath, $prefix);
            }

        }
        $xpdo = &$modx;
    }
} else {
    $xpdo = &$modx;
}

$object_id = $modx->getOption('object_id', $scriptProperties, 0);
$filter_id = $modx->getOption('filtermailmember', $scriptProperties, 0);
$classname = 'mvMail';

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

if (!empty($object_id) && $object = $xpdo->getObject($classname, $object_id)) {

    $where_json = $modx->runSnippet('mv_prepareMemberWhere');
    $where = $modx->fromJson($where_json);
    if (is_array($where) && !empty($where)) {

        $classname = 'mvMember';
        $c = $modx->newQuery($classname);
        $c->where($where);
        $count = $modx->getCount($classname, $c);
        if ($collection = $modx->getIterator($classname, $c)) {
            $log = $modx->newObject('mvMailLog');
            $log->set('count', $count);
            $log->set('mail_id', $object_id);
            $log->set('filter_id', $filter_id);
            $log->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            $log->set('createdby', $modx->user->get('id'));
            if ($log->save()) {
                $log_id = $log->get('id');
            }

            foreach ($collection as $recipient) {

                $properties = array();
                $properties['mail_id'] = $object->get('id');
                $properties['member_id'] = $recipient->get('id');
                $properties['log_id'] = $log_id;

                $reference = 'web/schedule/sendmail';
                $fbuch->createSchedulerTask('fbuch', array('snippet' => $reference));
                $fbuch->createSchedulerTaskRun($reference, 'fbuch', $properties);

                /*
                $object->sendMail($recipient);
                $log = $modx->newObject('mvMailLogRecipient');
                $log->set('member_id', $recipient->get('id'));
                $log->set('log_id', $log_id);
                $log->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                $log->set('createdby', $modx->user->get('id'));
                if ($log->save()) {

                }
                */

            }
        }
    } else {
        $text = "Scheinbar wurde kein Filter gewählt.<br/>Bitte Filter wählen!";
        $result['success'] = false;
        $result['message'] = $text;
        echo $modx->toJson($result);
        exit();
    }
}

$text = "Erfolg";
$result['success'] = true;
$result['message'] = $text;
echo $modx->toJson($result);
exit();

return $modx->error->success();
