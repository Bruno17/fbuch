<?php
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');
$email = '';
if ($object = $modx->getObject('mvMember',$input)){
    $email = $fbuch->getNameEmail($object);
}
return empty($email) ? 'no' : $email;