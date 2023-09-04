<?php

$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$file = $fbuchCorePath . 'customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

$output = array();

$c = $modx->newQuery('modChunk');
$c->where(array('name:LIKE' => 'custom_%'));
$c->sortby('name');
if ($chunks = $modx->getCollection('modChunk', $c)) {
    foreach ($chunks as $chunk) {
        $name = $chunk->get('name');
        $item = array();
        $inputitem = array();
        if (isset($input[$name])) {
            $inputitem = $input[$name];
        }
        $item['deleted'] = '0';
        $item['group'] = $modx->getOption('group', $inputitem, '');
        $item['tab'] = $modx->getOption('tab', $inputitem, '');
        $item['caption'] = $modx->getOption('caption', $inputitem, '');
        $item['inputTVtype'] = $modx->getOption('inputTVtype', $inputitem, '');
        $output[$name] = $item;
        unset($input[$name]);
    }
}

foreach ($input as $name => $item) {
    $item['deleted'] = '1';
    $output[$name] = $item;
}

$cacheManager = $modx->getCacheManager();
$cacheManager->writeFile($file, json_encode($output, JSON_PRETTY_PRINT));

return $modx->error->success(basename($file));
