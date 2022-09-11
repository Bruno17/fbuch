<?php
$chars = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
$chars = explode(',',$chars);
$currentchar = $modx->getOption('char',$_REQUEST,'A');

foreach ($chars as $char){
    $ph = array();
    $ph['char'] = $char;
    $ph['active'] = $char==$currentchar ? 'active' : '';
    $output[] = $modx->getChunk($tpl,$ph);    
}

$modx->setPlaceholder('atoz_char',$currentchar);

return implode('',$output);