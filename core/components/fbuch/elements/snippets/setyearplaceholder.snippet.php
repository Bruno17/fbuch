<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'setYearPlaceholder']);
$modx->setPlaceholder('year',strftime('%Y'));

if (isset($_REQUEST['year'])){
    $year = (int) $_REQUEST['year'];
    $modx->setPlaceholder('year',$year);
}

return '';