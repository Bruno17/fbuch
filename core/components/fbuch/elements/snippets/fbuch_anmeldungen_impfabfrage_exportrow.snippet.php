<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuch_anmeldungen_impfabfrage_exportrow']);
$scriptProperties['exportFields']['genesen'] = 'genesen';
$scriptProperties['exportFields']['geimpft'] = 'geimpft';

$row = $modx->getOption('row',$scriptProperties,[]);
$extended = json_decode($modx->getOption('extended',$row,[]),true);

if (is_array($extended)){
    $scriptProperties['row']['genesen'] = $modx->getOption('genesen',$extended,'');
    $scriptProperties['row']['geimpft'] = $modx->getOption('geimpft',$extended,'');
}