<?php
$uri = $modx->getOption('uri',$scriptProperties,'');
if (!empty($uri)){
    if ($resource = $modx->getObject('modResource',['uri'=>$uri])){
        return $resource->get('id');
    }
}
return 0;