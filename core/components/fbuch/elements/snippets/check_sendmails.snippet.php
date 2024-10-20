<?php 
$revers = $modx->getOption('revers', $scriptProperties, '');
$tempparams = $modx->fromJson($modx->getOption('tempParams', $_REQUEST, ''));
$sendmails = $modx->getOption('sendmails', $tempparams, '');

$output = '';
if (!empty($sendmails)) {
    $output = '1';
}

if (!empty($revers)) {
    $output = !empty($output) ? '' : '1';
}

return $output;