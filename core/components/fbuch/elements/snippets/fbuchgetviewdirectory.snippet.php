<?php
$alias = $modx->resource->get('alias');
$path = 'quasar/views/' . $alias;
$defaultpath = 'quasar/views/default';

$assets_path = $modx->getOption('fbuch.assets_path',null,$modx->getOption('assets_path').'components/fbuch/');

$fullpath = $assets_path . $path;

$output = file_exists($fullpath) ? $path : $defaultpath; 

return $output;