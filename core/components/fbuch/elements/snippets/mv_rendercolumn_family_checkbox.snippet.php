<?php
$object_id = $modx->getOption('object_id',$_REQUEST,0);
$req_configs = $modx->getOption('reqConfigs',$_REQUEST,0);
if ($req_configs == 'mv_families:fbuch'){
    $o_family_id = $object_id;
} else {
    if ($object=$modx->getObject('mvMember',$object_id)){
        $o_family_id = $object->get('family_id');
    }
}
$m_family_id = $modx->getOption('family_id',$scriptProperties,0);

$assetsUrl = $modx->getOption('migx.assets_url');
if (empty($assetsUrl)) {
    $assetsUrl = $modx->getOption('assets_url') . 'components/migx/' ;    
}

$output= '
<a href="#">
<img title="unchecked" alt="unchecked" src="' . $assetsUrl . 'style/images/cb_empty.png" class="controlBtn unchecked this.handleColumnSwitch familymember:1">
</a>
';

if (!empty($o_family_id) && $m_family_id == $o_family_id){
$output= '
<a href="#">
<img title="checked" alt="checked" src="' . $assetsUrl . 'style/images/cb_ticked.png" class="controlBtn checked this.handleColumnSwitch familymember:0">
</a>
';
}


return $output ;