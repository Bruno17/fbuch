<?php 
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuchAssetsPath = realpath($modx->getOption('fbuch.assets_path', null, $modx->getOption('assets_path') . 'components/fbuch')) . '/';

$result_folder = $fbuchCorePath . 'customchunks/';
$result_file = $result_folder . 'used_elements.json';
$used_elements = [];

if (file_exists($result_file)){
    $old_content = json_decode(file_get_contents($result_file),true);   
    if (is_array($old_content)){
        $used_elements = $old_content;        
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'fbuch_is_element_used:Inhalt von used_elements.json is kein array.');
        return '';
    }
}



if (!isset($used_elements[$type])){
    $used_elements[$type] = [];    
}
if (!isset($used_elements[$type][$name])){
    $used_elements[$type][$name] = [];    
}

$used_elements[$type][$name]['used'] = date('Y-m-d H:i');


file_put_contents($result_file,json_encode($used_elements,JSON_PRETTY_PRINT));
return '';