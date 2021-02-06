<?php

$file = $modx->getOption('core_path') . 'components/fbuch/customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

$groups = array();

$i=1;
foreach ($input as $name =>  $row){
    $group = $modx->getOption('group',$row);
    $group = empty($group) ? $name : $group;
    $groups[$group] = $group;
}


$rows = array();

$i=1;
foreach ($groups as $group =>  $row){
    $row = array();
    $row['id'] = $i;
    $row['name'] = $group;
    $rows[] = $row;
    $i++;    
}