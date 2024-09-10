<?php
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuch_login_und_redirect']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuch_login_und_redirect']);
$target = $modx->getOption('target',$_REQUEST,'');
$route = $modx->getOption('route',$_REQUEST,'');


//print_r($_REQUEST);

if (!empty($target)){
    $target = !empty($route) ? $target . '/#/' . $route : $target;
    $modx->sendRedirect($target);     
}