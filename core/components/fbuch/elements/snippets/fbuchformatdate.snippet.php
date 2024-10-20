<?php 
$start_date = $modx->getOption('start_date',$scriptProperties,'');
$start_time = $modx->getOption('start_time',$scriptProperties,'');
$end_date = $modx->getOption('end_date',$scriptProperties,'');
$end_time = $modx->getOption('end_time',$scriptProperties,'');

$sd = strftime('%Y%m%d',strtotime($start_date));
$ed = strftime('%Y%m%d',strtotime($end_date));


$output = strftime('%a,%d.%m.%Y',strtotime($start_date)) . ' ' . $start_time;
if ($ed>$sd){
    $output .= '<br>bis<br>' . strftime('%a,%d.%m.%Y',strtotime($end_date)) . ' ' . $end_time;
} else {
    if (!empty($end_time)){
        $output .= ' bis ' . $end_time;
    }
}

return $output;