<?php


$object = & $modx->getOption('object',$scriptProperties,null);

if ($object){
    $object->set('hash',md5($object->get('id') . '_' . $object->get('createdon')));
    $object->save();
}