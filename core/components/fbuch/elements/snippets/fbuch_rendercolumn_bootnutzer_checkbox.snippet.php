<?php

$boot_id = $modx->getOption('object_id', $_REQUEST, 0);
$req_configs = $modx->getOption('reqConfigs', $_REQUEST, 0);
$member_id = $modx->getOption('member_id', $scriptProperties, 0);

$output = '
<a href="#">
<img title="unchecked" alt="unchecked" src="/assets/components/migx/style/images/cb_empty.png" class="controlBtn unchecked this.handleColumnSwitch allowed:1">
</a>
';

if ($modx->getObject('fbuchBootAllowedNames', array('member_id' => $member_id, 'boot_id' => $boot_id))) {
$output = '
<a href="#">
<img title="checked" alt="checked" src="/assets/components/migx/style/images/cb_ticked.png" class="controlBtn checked this.handleColumnSwitch allowed:0">
</a>
';
}
return $output;