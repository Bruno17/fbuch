<?php

$id = $modx->getOption('object_id',$scriptProperties,'new');

$file = $modx->getOption('core_path') . 'components/fbuch/customchunks/customchunks.js';
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
    
    if ($chunk = $modx->getObject('modChunk',array('name'=>$row['field']))){
        //$field['default'] = $chunk->get('snippet');
        $record[$row['field']] = $chunk->get('snippet');
    }
}

$modx->migx->record_fields = $record;