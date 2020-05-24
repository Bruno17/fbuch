<?php

$uploadpath = $modx->getOption('base_path') . 'csvimport/';

function cleartable($classname,$scip_ids = '') {
    global $modx;

    $scip_ids = explode(',',$scip_ids);

    $c = $modx->newQuery($classname);

    if ($collection = $modx->getIterator($classname, $c)) {
        foreach ($collection as $object) {
            $id = $object->get('id');
            if (in_array($id,$scip_ids)){
                continue;
            }
            
            $object->remove();
        }
    }
    $tablename = $modx->getTableName($classname);
    $modx->exec("alter table {$tablename} AUTO_INCREMENT =1");
}


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
} else {
    $xpdo = &$modx;
}


set_time_limit(1000);

$filename = 'ruderer.csv';
$idx = 1;
if (($handle = fopen($uploadpath . $filename, "r")) !== false) {

    $classname = $config['classname'];

    cleartable($classname);

    $user_classname = 'modUser';
    cleartable($user_classname,'1,4');

    $gruppe_classname = 'fbuchBootsNutzergruppe';
    //cleartable($gruppe_classname);


    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        $row = array();
        if ($idx == 1) {
            $fields = $data;
        } else {
            $num = count($data);

            for ($c = 0; $c < $num; $c++) {
                $field = $fields[$c];
                //$row[$field] = mb_convert_encoding($data[$c], "UTF-8", "ISO-8859-1");
                $row[$field] = $data[$c];
            }

            $data = array();

            //print_r($row);

            /*
            $gattung_ids = array();
            $shortnames = explode(',', $row['TypeVariant']);
            if ($row['LastVariant'] > count($shortnames)) {
            $row['LastVariant'] = count($shortnames);
            }
            if (is_array($shortnames) && count($shortnames) > 0) {
            $idx = 1;
            foreach ($shortnames as $shortname) {
            if ($gattung = $modx->getObject($gattung_classname, array('shortname' => $shortname))) {

            } else {
            $gattung = $modx->newObject($gattung_classname);
            $gattung->set('shortname', $shortname);
            $gattung->save();
            }
            if ($row['LastVariant'] == $idx) {
            $gattung_id = $gattung->get('id');
            }

            $gattung_ids[] = $gattung->get('id');
            $idx++;
            }

            }

            if (!empty($row['Gruppe'])) {
            if ($gruppe = $modx->getObject($gruppe_classname, array('name' => $row['Gruppe'], 'color_name' => $row['Farbe']))) {

            } else {
            $gruppe = $modx->newObject($gruppe_classname);
            $gruppe->set('name', $row['Gruppe']);
            $gruppe->set('color_name', $row['Farbe']);
            $gruppe->save();
            }
            $data['nutzergruppe'] = $gruppe->get('id');
            }
            */
            
            //create user
            
            $vorname = $row['﻿Vorname'];
            $user = $modx->newObject('modUser');
            $year = substr($row['Birthday'],2,2);
            $user->set('username',strtolower($vorname) . strtolower($row['Nachname']).$year);
            $user->set('active',0);
            $profile = $modx->newObject('modUserProfile');
            $user->addOne($profile);
            $profile->set('fullname',$vorname. ' ' . $row['Nachname']);
            $profile->set('email',$row['Email']);
            $user->save();
            
            $user->joinGroup('fbuch','Member');

            $data['modx_user_id'] = $user->get('id');
            $data['firstname'] = $vorname;
            $data['name'] = $row['Nachname'];
            $data['anredetitel'] = $row['Titel'];
            $data['gender'] = $row['Geschlecht'];
            $data['birthdate'] = $row['Birthday'] . '-01-01 00:00:00';
            $data['drv_nr'] = $row['DRV_Nr'];
            $data['startberechtigt'] = $row['Startberechtigt'];
            $data['email'] = $row['Email'];
            $data[''] = ucfirst($row['Erlaubnis']);
            $data['efa_id'] = $row['EFA_Id'];
            if ($row['Mitglied'] == 1) {
                $data['member_status'] = 'Mitglied';
                $data['inactive'] = 0;
            } else {
                $data['member_status'] = 'Gast';
                $data['inactive'] = 1;
                $data['inactive_reason'] = 'kein Mitglied';
            }


            //print_r($data);


            if ($object = $modx->newObject($classname)) {
                $object->fromArray($data);
                $object->save();
            }

            if (!empty($row['Erlaubnis'])) {
                $color_name = ucfirst($row['Erlaubnis']);
                if ($gruppe = $modx->getObject($gruppe_classname, array('color_name' => $color_name))) {

                } else {
                    $gruppe = $modx->newObject($gruppe_classname);
                    $gruppe->set('name', $color_name);
                    $gruppe->set('color_name', $color_name);
                    $gruppe->save();
                }
                $group_id = $gruppe->get('id');
                $member_id = $object->get('id');
                $link_classname = 'fbuchBootsNutzergruppenMembers';
                if ($link = $modx->getObject($link_classname,array('group_id'=>$group_id,'member_id'=>$member_id))){
                    
                }else {
                    $link = $modx->newObject($link_classname);
                    $link->set('group_id',$group_id);
                    $link->set('member_id',$member_id);
                    $link->save();
                }
                
            }


        }
        $idx++;
    }
    fclose($handle);


}

return $modx->error->success();
