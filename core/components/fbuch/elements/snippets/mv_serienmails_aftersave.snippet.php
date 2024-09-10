<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'mv_serienmails_aftersave']);
$object = & $modx->getOption('object',$scriptProperties,null);

if ($object){
    $object->set('hash',md5($object->get('id') . '_' . $object->get('createdon')));
    $object->save();
}