<?php
$url = $modx->getOption('url',$scriptProperties,'');
$filename = $modx->getOption('filename',$scriptProperties,'');
$path = $modx->getOption('assets_path') . 'stored_assets/';
if (!file_exists($path.$filename)){
    $content = file_get_contents($url);
    file_put_contents($path.$filename,$content);
}
$modx->setPlaceholder($ph,$modx->getOption('assets_url').'stored_assets/'.$filename);
return ;

//echo $content;