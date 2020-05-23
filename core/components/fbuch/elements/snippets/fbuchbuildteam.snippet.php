<?php
$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : '';
$member_ids = isset($_REQUEST['member_ids']) ? $_REQUEST['member_ids'] : '';
$member_ids = !empty($member_ids) ? explode(',',$member_ids) : array();
if (!in_array($member_id,$member_ids)){
    $member_ids[] = $member_id;
}

$modx->setPlaceholder('member_ids',implode(',',$member_ids));
return '';