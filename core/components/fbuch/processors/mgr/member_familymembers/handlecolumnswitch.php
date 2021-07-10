<?php

/**
 * MIGXdb
 *
 * Copyright 2012 by Bruno Perner <b.perner@gmx.de>
 *
 * This file is part of MIGXdb, for editing custom-tables in MODx Revolution CMP.
 *
 * MIGXdb is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * MIGXdb is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * MIGXdb; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA 
 *
 * @package migx
 */
/**
 * Columnswitch-processor for migxdb
 *
 * @package migx
 * @subpackage processors
 */
//if (!$modx->hasPermission('quip.thread_view')) return $modx->error->failure($modx->lexicon('access_denied'));

//return $modx->error->failure('huhu');

if (empty($scriptProperties['object_id'])) {
    return $modx->error->failure($modx->lexicon('quip.thread_err_ns'));
    return;
}

$config = $modx->migx->customconfigs;
$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);

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
$classname = $config['classname'];

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

$col = explode(':', $modx->getOption('col', $scriptProperties, ''));

$value = isset($col[1]) ? $col[1] : '0';
$col = isset($col[0]) ? $col[0] : '';

if (empty($col)) {
    return $modx->error->failure('no column');
}

$modx->migx->loadConfigs();


$object = $xpdo->getObject($classname, $scriptProperties['object_id']);
$object->set($col, $value);
$saveobject = true;
switch ($col) {
    case 'familymember':

        $saveobject = false;
        $req_configs = $modx->getOption('reqConfigs', $_REQUEST, 0);
        if ($req_configs == 'mv_families:fbuch') {
            $family = $xpdo->getObject('mvFamily', $scriptProperties['co_id']);
        } else {
            $member = $xpdo->getObject($classname, $scriptProperties['co_id']);
            //has the current member allready family?
            if ($family = $member->getOne('Family')) {

            } else {
                //if not, check if the clicked member has allready a family and use this one
                $family = $object->getOne('Family');
            }
        }


        if ($value == '1') {
            if ($family) {
                if ($member){
                    $family->set('member_id', $member->get('id'));
                }
                $family->save();
            } else {
                //if none of both have a family create a new one with the current member as family-mainmember
                $family = $modx->newObject('mvFamily');
                if ($member){
                    $family->set('name', $member->get('name'));
                    $family->set('member_id', $member->get('id'));                    
                }
                $family->save();
                if ($member){                
                    $member->set('family_id', $family->get('id'));
                    $member->save();
                }    
            }
            $object->set('family_id', $family->get('id'));
            $object->save();
        } elseif ($object->get('id') != $family->get('member_id')) {

            $object->set('family_id', 0);
            $object->save();

        }
        break;

    case 'payer':

        $saveobject = false;
        
        $req_configs = $modx->getOption('reqConfigs', $_REQUEST, 0);
        if ($req_configs == 'mv_families') {
            $family = $xpdo->getObject('mvFamily', $scriptProperties['co_id']);
        } else {
            $member = $xpdo->getObject($classname, $scriptProperties['co_id']);
            //has the current member allready family?
            if ($family = $member->getOne('Family')) {

            } else {
                //if not, check if the clicked member has allready a family and use this one
                $family = $object->getOne('Family');
            }
        }        

        if ($value == '1') {
            if ($family) {
                $family->set('member_id', $object->get('id'));
                $family->save();
            } else {
                //if none of both have a family create a new one with the current member as family-mainmember
                $family = $modx->newObject('mvFamily');
            }
            $family->set('name', $object->get('name'));
            $family->set('member_id', $object->get('id'));
            $family->save();
            if ($member){
                $member->set('family_id', $family->get('id'));
                $member->save();                
            }
            $object->set('family_id', $family->get('id'));
            $object->save();
        } else {

        }

        break;


}

if ($saveobject) {
    if ($object->save() == false) {
        return $modx->error->failure($modx->lexicon('quip.thread_err_save'));
    }
}


//clear cache for all contexts
/*
$collection = $modx->getCollection('modContext');
foreach ($collection as $context) {
$contexts[] = $context->get('key');
}
$modx->cacheManager->refresh(array(
'db' => array(),
'auto_publish' => array('contexts' => $contexts),
'context_settings' => array('contexts' => $contexts),
'resource' => array('contexts' => $contexts),
));
*/
return $modx->error->success();
