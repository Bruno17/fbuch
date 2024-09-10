<?php
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchGetAssetsFiles']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuchGetAssetsFiles']);
$folder = $modx->getOption('folder',$scriptProperties,'');
$pattern = $modx->getOption('pattern',$scriptProperties,'*.html');
$wrapper = $modx->getOption('wrapper',$scriptProperties,'[[+output]]');

$assets_path = $modx->getOption('fbuch.assets_path',null,$modx->getOption('assets_path').'components/fbuch/');

$output = '';
$files = glob($assets_path . $folder . "/" . $pattern);

foreach ($files as $filename) {
    $fileinfo = pathinfo($filename);
    $newwrapper = str_replace('[[+filename]]',$fileinfo['filename'],$wrapper);
    $output .= str_replace('[[+output]]',file_get_contents($filename),$newwrapper) . "\n";
}

return $output;