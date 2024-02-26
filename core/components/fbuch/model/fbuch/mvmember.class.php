<?php
class mvMember extends xPDOSimpleObject {

    public function save($cacheFlag= null){
        if (!$this->isNew()) {
            $this->xpdo->oldObject = $this->xpdo->getObject('mvMember',$this->get('id'));
        }        
        return parent::save($cacheFlag);
    }    

    public function afterSave($fbuch){
        $modx = &$this->xpdo;
        $object = &$this;

        $member_status = $object->get('member_status');

        if ($modx->oldObject){
            /*
            bei geändertem Mitgliederstatus
            wenn der neue Status = 'Mitglied'
            Mitgliedstatus aller Fahrten des Eintrittsjahres auf 'Mitglied' setzen,
            falls vorher zb. Gast eingetragen war 
            (wegen Einordnung bei Kilometerauswertung)
            */
            $eintritt = $this->get('eintritt');
            $year_eintritt = substr($eintritt,0,4);            
            $old_member_status = $modx->oldObject->get('member_status');    
            if ($member_status == 'Mitglied' && $old_member_status != $member_status){
            //we do nothing here! Das ist zu komplex und zu fehleranfällig.
            //wenn nötig, wird der Mitgliedstatus der Fahrten manuell angepasst. Fertig.    
            }
        }        

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