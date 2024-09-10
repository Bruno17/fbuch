<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'mv_berechne_alter']);
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

return $fbuch->berechneAlter($scriptProperties);