<?php

if (empty($scriptProperties['object_id'])) {
    return $modx->error->failure($modx->lexicon('quip.thread_err_ns'));
    return;
}

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
$classname = $config['classname'];
$object_id = $modx->getOption('object_id', $scriptProperties, 0);

if ($object = $modx->getObject($classname, array('id' => $object_id))) {
    $user_id = $object->get('modx_user_id');
    if ($user = $modx->getObject('modUser',array('id'=>$user_id))){
        
    } else {
        $user_id = 0;
    }
    
    if ($user_id == 0) {
        $vorname = $object->get('firstname');
        $lastname = $object->get('name'); 
        $birthdate = strftime('%Y',strtotime($object->get('birthdate'))); 
        $user = $modx->newObject('modUser');
       
        $year = substr($birthdate, 2, 2);
        $user->set('username', strtolower($vorname) . strtolower($lastname) . $year);
        $user->set('active', 0);
        $profile = $modx->newObject('modUserProfile');
        $user->addOne($profile);
        $profile->set('fullname', $vorname . ' ' . $lastname);
        $profile->set('email', $object->get('email'));
        $user->save();

        $user->joinGroup('fbuch', 'Member');
        
        $object->set('modx_user_id',$user->get('id'));
        $object->save();
        
    }
}


return $modx->error->success();
