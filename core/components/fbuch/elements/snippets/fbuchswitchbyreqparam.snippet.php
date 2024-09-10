<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchSwitchByReqParam']);
$name = $modx->getOption('name',$scriptProperties,'');
$default = $modx->getOption('default',$scriptProperties,'');
$options = $modx->getOption('options',$scriptProperties,'');

$options = json_decode($options,true);

$output = $options[$default];

if (isset($_REQUEST[$name]) && isset($options[$_REQUEST[$name]])){
    $output = $options[$_REQUEST[$name]];
}

return $output;