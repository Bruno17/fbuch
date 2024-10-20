<?php 
$object = &$modx->getOption('object', $scriptProperties, null);
$properties = $modx->getOption('scriptProperties', $scriptProperties, array());
$record = $object->get('record_fields');

$configs = $modx->getOption('configs', $properties, '');

if (isset($record['name'])){
    $original_name = substr($record['name'],7);
    if ($chunk = $modx->getObject('modChunk',array('name'=>$original_name))){
        $record['original_snippet'] = $chunk->get('snippet');
        $record['original_name'] = $original_name;
    }
}

$object->set('record_fields',$record);
return;