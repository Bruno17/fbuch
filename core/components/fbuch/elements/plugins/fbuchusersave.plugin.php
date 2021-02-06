<?php
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

$message = $modx->getChunk($fbuch->getChunkName('fbuch_benutzerpasswort_mail'),$scriptProperties);

$modx->setOption('signupemail_message',$message);

return;