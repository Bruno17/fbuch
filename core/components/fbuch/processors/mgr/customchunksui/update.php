<?php

$data = json_decode($modx->getOption('data',$scriptProperties),1);

foreach ($data as $name => $value){
    if ($chunk = $modx->getObject('modChunk',array('name'=>$name))){
        $chunk->set('content',$value);
        $chunk->save();
    }
}

//clear cache for all contexts
$collection = $modx->getCollection('modContext');
foreach ($collection as $context) {
    $contexts[] = $context->get('key');
}
$modx->cacheManager->refresh(array(
    'db' => array(),
    'auto_publish' => array('contexts' => $contexts),
    'context_settings' => array('contexts' => $contexts),
    'resource' => array('contexts' => $contexts),
    ));