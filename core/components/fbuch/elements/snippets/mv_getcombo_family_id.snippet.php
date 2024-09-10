<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'mv_getcombo_family_id']);
$object_id = $modx->getOption('object_id',$_REQUEST,0);
$req_configs = $modx->getOption('reqConfigs',$_REQUEST,0);
$output='';
if ($req_configs == 'mv_families:fbuch'){
    $output = $object_id;
}else{
    if ($object=$modx->getObject('mvMember',$object_id)){
        $output = $object->get('family_id');
    }
}

return $output;