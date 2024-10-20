<?php 
$object = & $modx->getOption('object',$scriptProperties,null);
$properties = $modx->getOption('scriptProperties',$scriptProperties,array());
$postvalues = $modx->getOption('postvalues',$scriptProperties,array());

$configs = $modx->getOption('configs', $properties, '');

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

switch ($configs) {
    case 'mv_mitglieder':
    case 'mv_mitglieder:fbuch':
        $config = array(
            'id_field' => 'member_id',
            'link_field' => 'role_id',
            'link_classname' => 'mvMemberRoleLink',
            'link_alias' => 'RoleLinks',
            'postfield' => 'roles');
        $modx->migx->handleRelatedLinks($object, $postvalues, $config);
        
        $config = array(
            'id_field' => 'member_id',
            'link_field' => 'group_id',
            'link_classname' => 'fbuchBootsNutzergruppenMembers',
            'link_alias' => 'Nutzergruppen',
            'postfield' => 'nutzergruppen');
        $modx->migx->handleRelatedLinks($object, $postvalues, $config);        

        $object->afterSave($fbuch);


        /*
        $config = array(
            'id_field' => 'system_id',
            'link_field' => 'product_id',
            'link_classname' => 'vdbSystemProductLink',
            'link_alias' => 'ProductLinks',
            'postfield' => 'products',
            'extrafields' => 'productgroup_id',
            'resave_object' => '1'
            );          
        $modx->migx->handleRelatedLinksFromMIGX($object, $postvalues, $config);
        */
        break;
}

return;