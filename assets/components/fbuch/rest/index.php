<?php
// Boot up MODX
$working_context = 'fbuch';

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize($working_context);
$modx->getService('error','error.modError', '', '');
// Boot up any service classes or packages (models) you will need


$packageCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $packageCorePath . 'model/fbuch/');
$mvCorePath = realpath($modx->getOption('mv.core_path', null, $modx->getOption('core_path') . 'components/mv')) . '/';
$mv = $modx->getService('mv', 'Mv', $mvCorePath . 'model/mv/');

$migxCorePath = realpath($modx->getOption('migx.core_path', null, $modx->getOption('core_path') . 'components/migx')) . '/';
$migx = $modx->getService('migx', 'Migx', $migxCorePath . 'model/migx/');

// Load the modRestService class and pass it some basic configuration
$rest = $modx->getService('rest', 'rest.modRestService', '', array(
    'basePath' => $packageCorePath . 'rest/Controllers/',
    'controllerClassSeparator' => '',
    'controllerClassPrefix' => 'MyController',
    'xmlRootNode' => 'response',
));

//print_r($modx->user->toArray());

// Prepare the request
$rest->prepare();
// Make sure the user has the proper permissions, send the user a 401 error if not
if (!$rest->checkPermissions()) {
    $rest->sendUnauthorized(true);
}
// Run the request
$rest->process();