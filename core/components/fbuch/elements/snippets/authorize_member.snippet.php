<?php

$packageName = 'fbuch';
$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
if (is_dir($modelpath)) {
    $modx->addPackage($packageName, $modelpath, $prefix);
}

$formTpl = $modx->getOption('formTpl',$scriptProperties,'mv_mitgliederDatenFormular2');
$datenerfassung = $code = $modx->getOption('datenerfassung', $_REQUEST, '');
$pw = $code = $modx->getOption('code', $_REQUEST, '');
$code = explode('-',$code);
$id = $code[0];
$code = $code[1];

$classname = 'mvMember';
$output = '[[$mv_mitgliederLoginFormular]]';
if (!empty($id) && $object = $modx->getObject($classname,$id)){
    $hash = $object->get('id').$object->get('firstname').$object->get('id');
    $hash = md5($hash);
    $hash = substr($hash,0,5);
    if ($hash == $code){
        $data = $object->toArray();
        $data['code'] = $pw;
        $output = $modx->getChunk($formTpl,$data);
    }
}

if (!empty($datenerfassung)){
    $output = '';
}

return $output;