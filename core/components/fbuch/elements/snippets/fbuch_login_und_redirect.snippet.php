<?php
$target = $modx->getOption('target',$_REQUEST,'');
$route = $modx->getOption('route',$_REQUEST,'');


//print_r($_REQUEST);

if (!empty($target)){
    $target = !empty($route) ? $target . '/#/' . $route : $target;
    $modx->sendRedirect($target);     
}