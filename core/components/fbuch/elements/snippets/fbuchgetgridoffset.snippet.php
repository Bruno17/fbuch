<?php
$total = $modx->getOption('total',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,0);
$grid_id = $modx->getOption('grid_id',$scriptProperties,'');
$maxoffset = $offset = $total-$limit;

$dir = $modx->getOption('dir',$_REQUEST,'');
$active_id = $modx->getOption('active_id',$_REQUEST,'');
$current_offset = $modx->getOption('offset',$_REQUEST,'');
$modx->setPlaceholder($grid_id . '_lastactive','active');

if (!empty($dir) && !empty($current_offset)){
    switch ($dir){
        case 'up':
        $offset = $current_offset-$limit;
        //$modx->setPlaceholder($grid_id . '_lastactive','');
        //$modx->setPlaceholder($grid_id . '_firstactive','active');
        break;
        case 'down':
        $offset = $current_offset+$limit;
        $modx->setPlaceholder($grid_id . '_lastactive','');
        $modx->setPlaceholder($grid_id . '_firstactive','active');
        break;
        case 'none':
        if (!empty($active_id)){
            $offset = $current_offset;
            $modx->setPlaceholder($grid_id . '_lastactive','');
            $modx->setPlaceholder($grid_id . '_firstactive','');
            $modx->setPlaceholder($grid_id . '_' . $active_id . '_active','active');
        }
        break;
    }
}

if ($offset > $maxoffset){
    $modx->setPlaceholder($grid_id . '_firstlast_offset','lastoffset');
    $modx->setPlaceholder($grid_id . '_lastactive','active');
    $modx->setPlaceholder($grid_id . '_firstactive','');
    $offset = $maxoffset;
}

if ($offset < 0){
    $offset = 0;
}

$modx->setPlaceholder($grid_id . '_offset',$offset);
return '';