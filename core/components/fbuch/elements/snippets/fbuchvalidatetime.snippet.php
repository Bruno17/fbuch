<?php

$input = explode(':',str_replace('.',':',$input));
$output = '';
if (!empty($input[0])){
    $value = (int) $input[0];
    $value = $value<10 ? '0' . $value : $value;
    $output = $value;
    if (isset($input[1])){
    $value = (int) $input[1];
    $value = $value<10 ? '0' . $value : $value;        
        $output .= ':' . $value;
    }else{
        $output .= ':00';
    }
}


return $output;