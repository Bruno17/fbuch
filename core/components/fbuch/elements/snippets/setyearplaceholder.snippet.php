<?php 
$modx->setPlaceholder('year',strftime('%Y'));

if (isset($_REQUEST['year'])){
    $year = (int) $_REQUEST['year'];
    $modx->setPlaceholder('year',$year);
}

return '';