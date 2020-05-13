<?php

$reqConfigs = $modx->getOption('reqConfigs', $_REQUEST);
$configs = $modx->getOption('configs', $_REQUEST);
$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}
$packageName = $config['packageName'];
$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
$modx->addPackage($packageName, $modelpath, $prefix);
$classname = $config['classname'];
$c = $modx->newQuery($classname);
$c->where(array('id:>' => 10, 'class_key' => 'modUser'));
if ($collection = $modx->getIterator($classname, $c)) {
    foreach ($collection as $object) {
        $object->remove();
    }
} //$modx->exec("delete from {$tablename} where deleted = 1"); return $modx->error->success();
