<?php
$code = $modx->getOption('code', $_REQUEST, '');
$iid = (int) $modx->getOption('iid', $_REQUEST, '');
$mid = (int) $modx->getOption('mid', $_REQUEST, '');
$target = $modx->getOption('target', $_REQUEST, '');
$route = $modx->getOption('route', $_REQUEST, '');
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

if (!empty($code) && !empty($iid)){
    $result = $modx->fbuch->loginByInvite($iid,$code,$route);
    if ($result){
        return $result;
    }
}

if (!empty($code) && !empty($mid)){
    $result = $modx->fbuch->loginByOtp($mid,$code,$target,$route);
    $url_params = '';
    if (!empty($target)){
        $url_params = '?target=' . $target;
        if (!empty($route)){
            $url_params .= '&route=' . $route;
        }
        $modx->setPlaceholder('url_params',$url_params);
    }

    if ($result){
        return $result;
    }    
}
return 'code_not_valid';