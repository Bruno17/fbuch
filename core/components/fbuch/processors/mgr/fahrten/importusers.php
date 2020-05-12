<?php

$default_email = 'mail@mail.ch';
$uploadpath = $modx->getOption('base_path') . 'userimport/';

/**
 * Add User Group memberships to the User
 * @return array
 */
function setUserGroups($groups, &$object)
{
    global $modx;
    $memberships = array();

    if ($groups !== null) {
        $groups = is_array($groups) ? $groups : $modx->fromJSON($groups);
        $groupsAdded = array();
        $idx = 0;
        foreach ($groups as $group) {
            if (in_array($group['usergroup'], $groupsAdded))
                continue;

            /**
             @var modUserGroupMember $membership */
            $membership = $modx->newObject('modUserGroupMember');
            $membership->set('user_group', $group['usergroup']);
            $membership->set('role', $group['role']);
            $membership->set('member', $object->get('id'));
            $membership->set('rank', isset($group['rank']) ? $group['rank'] : $idx);
            $membership->save();
            $memberships[] = $membership;
            $groupsAdded[] = $group['usergroup'];
            $idx++;
        }
    }
    return $memberships;
}

/**
 * @return modUserProfile
 */
function addProfile($properties, &$object)
{
    global $modx;
    $profile = $modx->newObject('modUserProfile');
    $profile->fromArray($properties);
    //$profile->set('blocked', $this->getProperty('blocked', false));
    //$profile->set('photo', '');
    $object->addOne($profile, 'Profile');
    return $profile;
}


set_time_limit(1000);

$dir = dir($uploadpath); // dir Objekt!
$files = array();
while ($file = $dir->Read()) {
    //dbg2( "UPLD:   Dir-Eintrag :" ,$child) ;
    $filename = strval($file);
    $p = strlen($filename);
    $ext = strtolower(substr($filename, $p - 4, 4)); //	check extension at end of name!

    if ($ext == '.txt') {
        $info['file'] = $filename;
        $info['type'] = $ext;
        $files[$filename] = $info;
    } //if
} //wend
$dir->close();

ksort($files);

foreach ($files as $filename => $file) {
    $idx = 1;
    if (($handle = fopen($uploadpath . $filename, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $row = array();
            if ($idx == 1) {
                $fields = $data;
            } else {
                $num = count($data);

                for ($c = 0; $c < $num; $c++) {
                    $field = $fields[$c];
                    $row[$field] = mb_convert_encoding($data[$c], "UTF-8", "ISO-8859-1");
                }
                if (!strstr($row['email'], '@')) {
                    $row['email'] = $default_email;
                }

                $data = array();
                $data['active'] = true;
                //$data['specifiedpassword'] = $row['password'];
                //$data['confirmpassword'] = $row['password'];
                //$data['passwordgenmethod'] = 'spec';
                //$data['passwordnotifymethod'] = 's';
                $data['username'] = $row['username'];
                $data['fullname'] = $row['name'];
                $data['email'] = $row['email'];

                $groups = explode('*', $row['usertype']);
                $usergroups = array();
                foreach ($groups as $group) {
                    $groupname = trim($group);

                    if ($usergroup = $modx->getObject('modUserGroup', array('name' => $groupname))) {

                    } else {
                        $usergroup = $modx->newObject('modUserGroup');
                        $usergroup->set('name', $groupname);
                        $usergroup->set('dashboard', 1);
                        $usergroup->save();
                    }

                    $groupdata = array();
                    $groupdata['role'] = '1';
                    $groupdata['usergroup'] = $usergroup->get('id');
                    $usergroups[] = $groupdata;
                }
                
                if ($object = $modx->getObject('modUser',array('username'=>$data['username']))){
                    
                }else{
                    $object = $modx->newObject('modUser');
                    $object->fromArray($data);
                    addProfile($data,$object);
                    $object->set('password',$row['password']);   
                    $object->save();
                    setUserGroups($usergroups,$object);
                        
                }               
                

                //$response = $modx->runProcessor('security/user/create', $data);
            }
            $idx++;
        }
        fclose($handle);
    }
}

return $modx->error->success();


