<?php 
$days = array('monday'=>'Mo','tuesday'=>'Di','wednesday'=>'Mi','thursday'=>'Do','friday'=>'Fr','saturday'=>'Sa','sunday'=>'So');
$dayidx = 0;
foreach ($days as $dayname=>$dayshort){
    $scriptProperties['dayidx'] = $dayidx;
    $scriptProperties['dayname'] = $dayname;
    $scriptProperties['dayshort'] = $dayshort;
    $output[] = $modx->getChunk($tpl,$scriptProperties);
    $dayidx++;
}

return implode('',$output);