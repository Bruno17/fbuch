<?php
$object = & $modx->getOption('object',$scriptProperties,null);
$properties = $modx->getOption('scriptProperties',$scriptProperties,array());
$postvalues = $modx->getOption('postvalues',$scriptProperties,array());

$configs = $modx->getOption('configs', $properties, '');

switch ($configs) {
    case 'mv_mitglieder':
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

        if ($object->get('member_status') == 'Mitglied'){
            $object->set('inactive',0);
        } else {
            $object->set('inactive',1); 
        }
        $object->save();
        
        if ($user = $modx->getObject('modUser',array('id'=>$object->get('modx_user_id')))){
            $user->set('active',$object->get('User_active'));
            $user->save();
            $profile = $user->getOne('Profile');
            $email = $object->get('email');
            if (!empty($email)){
                $profile->set('email',$email);
                $profile->save();
            }

        }


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