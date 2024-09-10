<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'mv_datenerfassung_report_newvalues']);
$output = array();

$output[] = '<table>';

foreach ($_REQUEST as $field=>$value){
    if (isset($_REQUEST[$field.'_old']) && trim($_REQUEST[$field.'_old']) != trim($value) ){
        $output[] = '<tr><td>' . $field . ': </td><td>' . $value . '</td></tr>'; 
    }
}

$output[] = '</table>';

return implode('',$output);