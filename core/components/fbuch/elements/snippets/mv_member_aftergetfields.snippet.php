<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'mv_member_aftergetfields']);
$object = &$modx->getOption('object', $scriptProperties, null);
$properties = $modx->getOption('scriptProperties', $scriptProperties, array());
$record = $object->get('record_fields');

$configs = $modx->getOption('configs', $properties, '');
switch ($configs) {
    case 'mv_mitglieder':
    case 'mv_mitglieder:fbuch':        
        $role_ids = array();
        if ($roles = $object->getMany('RoleLinks')) {
            foreach ($roles as $r_object) {
                $role_ids[] = $r_object->get('role_id');
            }
        }
        $record['roles'] = implode('||',$role_ids);
        
        $group_ids = array();
        if ($groups = $object->getMany('Nutzergruppen')) {
            foreach ($groups as $g_object) {
                $group_ids[] = $g_object->get('group_id');
            }
        }
        $record['nutzergruppen'] = implode('||',$group_ids);        
        $object->set('record_fields',$record);
        break;
}