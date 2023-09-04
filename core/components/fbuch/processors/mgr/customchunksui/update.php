<?php

$data = json_decode($modx->getOption('data',$scriptProperties),1);

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$file = $fbuchCorePath . 'customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

if ($category = $modx->getObject('modCategory', array('category' => 'fbuchcustom'))) {

} else {
    $category = $modx->newObject('modCategory');
    $category->set('category', 'fbuchcustom');
    $category->save();
}

foreach ($input as $name => $row){
    if (isset($data[$name])){
        if ($chunk = $modx->getObject('modChunk',array('name'=>$name))){
            $chunk->set('content',$data[$name]);
            $chunk->save();  
        } else {
            $chunk = $modx->newObject('modChunk');   
            $chunk->set('category', $category->get('id')); 
            $chunk->set('name',$name);
            $chunk->set('content',$data[$name]);
            $chunk->save();  
        }        
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