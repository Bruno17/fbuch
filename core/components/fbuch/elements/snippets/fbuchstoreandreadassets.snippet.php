<?php
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchStoreAndReadAssets']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuchStoreAndReadAssets']);
$url = $modx->getOption('url',$scriptProperties,'');
$filename = $modx->getOption('filename',$scriptProperties,'');
$path = $modx->getOption('assets_path') . 'stored_assets/';
if (!file_exists($path.$filename)){
    $content = file_get_contents($url);
    $cacheManager = $modx->getCacheManager();
    $cacheManager->writeFile($path.$filename,$content);    
    //file_put_contents($path.$filename,$content);
}
$modx->setPlaceholder($ph,$modx->getOption('assets_url').'stored_assets/'.$filename);
return ;

//echo $content;