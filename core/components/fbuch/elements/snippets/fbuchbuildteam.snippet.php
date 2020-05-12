<?php
$name_id = isset($_REQUEST['name_id']) ? $_REQUEST['name_id'] : '';
$name_ids = isset($_REQUEST['name_ids']) ? $_REQUEST['name_ids'] : '';
$name_ids = !empty($name_ids) ? explode(',',$name_ids) : array();
if (!in_array($name_id,$name_ids)){
    $name_ids[] = $name_id;
}

$modx->setPlaceholder('name_ids',implode(',',$name_ids));
return '';