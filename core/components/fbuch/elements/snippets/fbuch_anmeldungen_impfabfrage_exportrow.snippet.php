<?php
$scriptProperties['exportFields']['genesen'] = 'genesen';
$scriptProperties['exportFields']['geimpft'] = 'geimpft';

$row = $modx->getOption('row',$scriptProperties,[]);
$extended = json_decode($modx->getOption('extended',$row,[]),true);

if (is_array($extended)){
    $scriptProperties['row']['genesen'] = $modx->getOption('genesen',$extended,'');
    $scriptProperties['row']['geimpft'] = $modx->getOption('geimpft',$extended,'');
}