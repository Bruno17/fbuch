<?php
class mvMember extends xPDOSimpleObject {

    public function afterSave($fbuch){
        $modx = &$this->xpdo;
        $object = &$this;

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
                        if (!$user->isMember($group)){
                            $user->joinGroup(trim($group),'Member');
                        }
                    }
                }
                $remove_from_usergroups = $state_object->get('remove_from_usergroups');
                if (!empty($remove_from_usergroups)) {
                    $groups = explode(',',$remove_from_usergroups);
                    foreach ($groups as $group){
                        if ($user->isMember($group)){
                            $user->leaveGroup(trim($group));    
                        }
                    }
                }
                $modx->cacheManager->flushPermissions();                
            }            

        }
        
        //remove person from fbuchMailinglist, if not longer can be invited
        $member_id = $object->get('id');
        $fbuch->checkMemberMailinglists($member_id);        
    }

}