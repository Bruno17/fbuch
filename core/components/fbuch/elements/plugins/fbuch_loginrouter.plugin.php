<?php
$route=$modx->getOption('r',$_GET,'');

if (!empty($route)){
   
    $uri=$modx->getOption('q',$_GET,''); 
    if (!empty($uri)) {
        if($modx->getObject('modResource',["uri"=>$uri])){
            $modx->sendRedirect($uri.'/#/'.$route);
        } else {
             $modx->sendRedirect('login?target='.$uri.'&route='.$route);
        }
    }
}