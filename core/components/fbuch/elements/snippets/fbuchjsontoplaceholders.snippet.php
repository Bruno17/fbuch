<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchJsonToPlaceholders']);
$value = $modx->getOption('value',$scriptProperties,'');
$prefix = $modx->getOption('prefix',$scriptProperties,'');

//$modx->setPlaceholders($modx->fromJson($value),$prefix,'',true);

$values = json_decode($value, true);
if (is_array($values)) {
    $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($values));

    if (is_array($values)){
        foreach ($it as $key => $value){
            $value = $value == null ? '' : $value;
            $modx->setPlaceholder($prefix . $key, $value);
        }
    }
}