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

        $member_status = $object->get('member_status');

        if ($user = $modx->getObject('modUser',array('id'=>$object->get('modx_user_id')))){
            /*
            $user_active = $object->get('User_active');
            if ($user_active != ''){
                $user->set('active',$user_active);    
            }
            $user->save();
            */
            $profile = $user->getOne('Profile');
            $email = $object->get('email');
            if (!empty($email)){
                $profile->set('email',$email);
                $profile->save();
            }

            if ($state_object = $modx->getObject('mvMemberState',['state' => $member_status])){
                $add_to_usergroups = $state_object->get('add_to_usergroups');
                if (empty($add_to_usergroups)){
                    //Status mit keiner Benutzergruppe verknüpft - Benutzer deaktivieren
                    $user->set('active',0);
                    $user->save();
                }
                if (!empty($add_to_usergroups)) {
                    $groups = explode(',',$add_to_usergroups);
                    foreach ($groups as $group){
                        $user->joinGroup(trim($group),'Member');
                    }
                }
                $remove_from_usergroups = $state_object->get('remove_from_usergroups');
                if (!empty($remove_from_usergroups)) {
                    $groups = explode(',',$remove_from_usergroups);
                    foreach ($groups as $group){
                        $user->leaveGroup(trim($group));
                    }
                }                
            }            

        }
        
        //remove person from fbuchMailinglist, if not longer Mitglied,Gast,VHS
        $member_id = $object->get('id');
        $fbuch->checkMemberMailinglists($member_id);


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