<?php 
$output = array();

$output[] = '<table>';

foreach ($_REQUEST as $field=>$value){
    if (isset($_REQUEST[$field.'_old']) && trim($_REQUEST[$field.'_old']) != trim($value) ){
        $output[] = '<tr><td>' . $field . ': </td><td>' . $value . '</td></tr>'; 
    }
}

$output[] = '</table>';

return implode('',$output);