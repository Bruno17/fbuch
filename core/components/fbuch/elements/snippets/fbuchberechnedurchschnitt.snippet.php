<?php
$values = explode(',',$input);
$sum = 0;
$output = 0;
if (count($values)>0){
    foreach ($values as $value){
        $sum += $value;
    }
    $output = $sum / count($values);
}
return $output;