<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchFixColorpicker']);
$object = &$scriptProperties['object'];
$record_fields = $object->get('record_fields');

if (is_array($record_fields)){
    $color = $record_fields['color'];
    $record_fields['color'] = '#' . str_replace('#','',$color);
    $object->set('record_fields',$record_fields);
}

if (isset($scriptProperties['postvalues'])){
    $object = $scriptProperties['object'];
    $color = $object->get('color');
    $color = str_replace('#','',$color);
    $object->set('color',$color);
    $object->save();
}