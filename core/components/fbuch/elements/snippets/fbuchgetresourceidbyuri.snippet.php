<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchGetResourceIdByUri']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuchGetResourceIdByUri']);
$uri = $modx->getOption('uri',$scriptProperties,'');
if (!empty($uri)){
    if ($resource = $modx->getObject('modResource',['uri'=>$uri])){
        return $resource->get('id');
    }
}
return 0;