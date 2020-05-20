<?php

$output = str_replace('./','',$input);
if ($mediasource = $modx->getObject('sources.modMediaSource',$options)){
    $output = $mediasource->prepareOutputUrl($output);
}
return '/' . $output;