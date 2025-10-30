<?php
if ($chunk = $modx->getObject('modChunk',array('name'=>'debug'))){

} else {
    $chunk = $modx->newObject('modChunk');
    $chunk->set('name','debug');
}

$chunk->set('content',$chunk->get('content').print_r($_REQUEST,1));
$chunk->save();