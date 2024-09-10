<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuch_stripwhitespaces']);
$modx->runSnippet('fbuch_is_element_used' , ['type' => 'chunks','name' => 'fbuch_stripwhitespaces']);
$input = preg_replace('/\s+/', '', $input);
$input = strip_tags($input);
$input = substr($input,0,3);
return $input;