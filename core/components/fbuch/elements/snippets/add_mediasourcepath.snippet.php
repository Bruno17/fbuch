<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'add_mediasourcepath']);
$output = str_replace('./','',$input);
if ($mediasource = $modx->getObject('sources.modMediaSource',$options)){
    $output = $mediasource->prepareOutputUrl($output);
}
return '/' . $output;