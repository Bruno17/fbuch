<?php

$config = $modx->migx->customconfigs;
$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);
    $packageName = isset($packageNames[0]) ? $packageNames[0] : '';

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
    if ($this->modx->lexicon) {
        $this->modx->lexicon->load($packageName . ':default');
    }
} else {
    $xpdo = &$modx;
}

if (empty($scriptProperties['objects'])) {
    return $modx->error->failure($modx->lexicon('quip.comment_err_ns'));
}

$objectIds = explode(',', $scriptProperties['objects']);

$classname = $config['classname'];
$checkdeleted = isset($config['gridactionbuttons']['toggletrash']['active']) && !empty($config['gridactionbuttons']['toggletrash']['active']) ? true : false;
$joins = isset($config['joins']) && !empty($config['joins']) ? $modx->fromJson($config['joins']) : false;

$where = !empty($config['getlistwhere']) ? $config['getlistwhere'] : '';
$where = $modx->getOption('where', $scriptProperties, $where);

$chunk = $modx->newObject('modChunk');
$chunk->setCacheable(false);
$chunk->setContent($where);
$where = $chunk->process($scriptProperties);

$c = $xpdo->newQuery($classname);
$c->select($xpdo->getSelectColumns($classname, $classname));

if ($joins) {
    $modx->migx->prepareJoins($classname, $joins, $c);
}

if (!empty($where)) {
    $c->where($modx->fromJson($where));
}

$c->where(array('id:IN' => $objectIds));

$count = $xpdo->getCount($classname, $c);


//$c->sortby($sort,$dir);
//$c->prepare();echo $c->toSql();
$rows = array();
if ($collection = $xpdo->getCollection($classname, $c)) {
    foreach ($collection as $object) {
        $user_id = $object->get('User_internalKey');
        $user_id = empty($user_id) ? 0 : $user_id;        
        $mv_id = $object->get('MV_id');
        $mv_id = empty($mv_id) ? 0 : $mv_id;
        switch ($scriptProperties['task']) {
            case 'pullmvids':
                $object->set('mv_member_id', $mv_id);
                break;
            case 'pulluserid':
                $object->set('modx_user_id', $user_id);
                break;                
            case 'pullemail':
                $email = $object->get('MV_email');
                if (!empty($email)) {
                    $object->set('email', $email);
                }

                break;
            case 'pullstatus':
                $ausgetreten = $object->get('MV_inactive');
                $old_status = $object->get('member_status');
                if (!empty($mv_id)) {
                    //Mitglied gefunden
                    //echo 'Mitglied gefunden';
                    if (empty($ausgetreten)) {
                        $status = 'Mitglied';
                    }
                    else {
                        $status = 'Ausgetreten';
                    }
                } else{
                    //Mitglied nicht gefunden
                    //echo 'Mitglied nicht gefunden';
                    switch ($old_status) {
                        case 'Gast':
                        case 'VHS':
                        $status = $old_status;
                        break 1;
                        default:
                        $status = 'Unbekannt';
                        break 1;
                    }
                }

                $object->set('member_status', $status);
                break;
        }
        

        $object->save();
    }
}


return $modx->error->success();
