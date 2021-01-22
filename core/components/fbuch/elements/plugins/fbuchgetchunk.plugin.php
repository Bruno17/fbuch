<?php
switch ($modx->event->name) {
    case 'OnElementNotFound':
        $class = $modx->getOption('class',$scriptProperties,'');
        $name = $modx->getOption('name',$scriptProperties,'');

        $fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
        $fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

        if ($class == 'modChunk'){
            if (substr($name,0,1) == '$'){
                $name = substr($name,1);
                $name = $fbuch->getChunkName($name);
                $element = $modx->getObject($class,array('name'=>$name));  
                $modx->event->output($element); 
            }
        }
        break;
    case 'OnChunkSave':
        $chunk = & $modx->getOption('chunk',$scriptProperties,null);
        $name = '';
        if ($chunk){
            $name = $chunk->get('name');
        
            if (substr($name,0,7) == 'custom_'){
                if ($category = $modx->getObject('modCategory',array('category'=>'fbuchcustom'))){
                
                } else {
                    $category = $modx->newObject('modCategory');
                    $category->set('category','fbuchcustom');
                    $category->save();
                }
                if ($category->get('id') != $chunk->get('category')){
                    $chunk->set('category',$category->get('id'));        
                    $chunk->save();
                }
            }
        }
        break;
}


return;