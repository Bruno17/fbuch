<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchGetChunk']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuchGetChunk']);
$name = $modx->getOption('name',$scriptProperties,'');

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

return $fbuch->getChunkName($name);