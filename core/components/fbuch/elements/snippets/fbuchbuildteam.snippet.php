<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchBuildTeam']);
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

$member_id = isset($_REQUEST['member_id']) ? $_REQUEST['member_id'] : '';
$member_ids = isset($_REQUEST['member_ids']) ? $_REQUEST['member_ids'] : '';
$member_ids = !empty($member_ids) ? explode(',',$member_ids) : array();

$tpl = $modx->getOption('tpl',$scriptProperties,'');

foreach ($member_ids as $id){
    if ($object = $modx->getObject('mvMember',$id)){
        $output[] = $modx->getChunk($tpl,$object->toArray());
    }
}

if ($object = $modx->getObject('mvMember',$member_id)){
    if (!in_array($member_id,$member_ids) || $fbuch->isguest($member_id)){
        $output[] = $modx->getChunk($tpl,$object->toArray());
        $member_ids[] = $member_id;
    }
}


$modx->setPlaceholder('team_output',implode('',$output));

$modx->setPlaceholder('member_ids',implode(',',$member_ids));
return '';