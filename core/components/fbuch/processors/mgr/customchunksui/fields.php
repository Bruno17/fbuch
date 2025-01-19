<?php

$id = $modx->getOption('object_id',$scriptProperties,'new');

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$file = $fbuchCorePath . 'customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

$groups = array();
$groups[] = array();

$i=1;
foreach ($input as $name => $row){
    $group = $modx->getOption('group',$row);
    $group = empty($group) ? $name : $group;
    if (!isset($groups[$group])){
        $groups[$group] = array();
    }
    $row['field'] = $name;
    $groups[$group][] = $row;
}

$numgroups = array();
foreach ($groups as $group){
    $numgroups[] = $group;    
}

$currentgroup = $numgroups[$id];
//echo '<pre>' . print_r($currentgroup,1) . '</pre>';
$record = array();
foreach ($currentgroup as $row){
    $original_name = str_replace('custom_', '', $row['field']);
    if ($chunk = $modx->getObject('modChunk',array('name'=>$row['field']))){
        //$field['default'] = $chunk->get('snippet');
        $record[$row['field']] = $chunk->get('snippet');
    } elseif ($chunk = $modx->getObject('modChunk',array('name'=>$original_name))){
        $record[$row['field']] = $chunk->getContent();
    } else {
        $record[$row['field']] = '';    
    }
}

$modx->migx->record_fields = $record;