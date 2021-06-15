<?php
$corona_geimpft = $modx->getOption('corona_geimpft',$_POST,'');
$action = $modx->getOption('action',$scriptProperties,'');
$index = $modx->getOption('index',$scriptProperties,'');

if (!empty($corona_geimpft)){
    if ($action=='addperson' && is_integer($index) && is_array($corona_geimpft)){
        $corona_geimpft = $corona_geimpft[$index];
    }

    $extended = [];
    $extended['ungeimpft'] = $corona_geimpft == 'none' ? '1' : '';
    $extended['geimpft'] = $corona_geimpft == 'geimpft' ? '1' : '';
    $extended['genesen'] = $corona_geimpft == 'genesen' ? '1' : '';
    $object->set('extended',json_encode($extended));
    $object->save();
}

//echo '<pre>' . print_r($_POST,1);
//echo '<pre>' . print_r($object->toArray(),1);

//die();