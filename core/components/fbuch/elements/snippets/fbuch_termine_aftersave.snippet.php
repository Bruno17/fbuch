<?php
$object = & $modx->getOption('object',$scriptProperties);
$docopy = $object->get(copy_names_to_mailinglist);
if ($docopy && $names = $object->getMany('Invited')){
    //remove all existing names from mailinglist
    $classname = 'fbuchMailinglistNames';
    $list_id = $object->get('mailinglist_id');
    $c = $modx->newQuery($classname);
    $c->where(array('list_id'=>$list_id));
    $existing = array();
    if ($ml_names = $modx->getCollection($classname,$c)){
        foreach ($ml_names as $ml_name){
            $subscribed = $ml_name->get('subscribed');
            $unsubscribed = $ml_name->get('unsubscribed');
            
            //Todo: don't remove names, who has allready subscribed or unsubscribed to that list
            if (empty($subscribed) && empty($unsubscribed)){
                $ml_name->remove();    
            } else {
                $existing[] = $ml_name->get('name_id');
            }
            
        }
    }
    
    foreach ($names as $name){
        $name_id = $name->get('name_id');
        if (in_array($name_id,$existing)){
            continue;
        }
        
        $new_object = $modx->newObject($classname);
        $new_object->set('list_id',$list_id);
        $new_object->set('name_id',$name_id);
        $new_object->save();
    }
}