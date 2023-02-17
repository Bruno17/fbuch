<?php
$folder = $modx->getOption('folder',$scriptProperties,'');
$pattern = $modx->getOption('pattern',$scriptProperties,'*.html');
$wrapper = $modx->getOption('wrapper',$scriptProperties,'[[+output]]');

$output = '';
foreach (glob($folder . "/" . $pattern) as $filename) {
    $fileinfo = pathinfo($filename);
    $newwrapper = str_replace('[[+filename]]',$fileinfo['filename'],$wrapper);
    $output .= str_replace('[[+output]]',file_get_contents($filename),$newwrapper) . "\n";
}

return $output;