<?php


$object = &$modx->getOption('object', $scriptProperties, null);
$properties = $modx->getOption('scriptProperties', $scriptProperties, array());
$record = $object->get('record_fields');

$configs = $modx->getOption('configs', $properties, '');
switch ($configs) {
    case 'mv_mitglieder':
        $role_ids = array();
        if ($roles = $object->getMany('RoleLinks')) {
            foreach ($roles as $r_object) {
                $role_ids[] = $r_object->get('role_id');
            }
        }
        $record['roles'] = implode('||',$role_ids);
        $object->set('record_fields',$record);
        break;
}