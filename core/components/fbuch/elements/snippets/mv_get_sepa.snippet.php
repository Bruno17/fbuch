<?php 
/*
getXpdoInstanceAndAddPackage - properties

$prefix
$usecustomprefix
$packageName


prepareQuery - properties:

$limit
$offset
$totalVar
$where
$queries
$sortConfig
$groupby
$joins
$selectfields
$classname
$debug

renderOutput - properties:

$tpl
$wrapperTpl
$toSeparatePlaceholders
$toPlaceholder
$outputSeparator
$placeholdersKeyField
$toJsonPlaceholder
$jsonVarKey
$addfields

*/


$migx = $modx->getService('migx', 'Migx', $modx->getOption('migx.core_path', null, $modx->getOption('core_path') . 'components/migx/') . 'model/migx/', $scriptProperties);
if (!($migx instanceof Migx))
    return '';
//$modx->migx = &$migx;

$xpdo = $migx->getXpdoInstanceAndAddPackage($scriptProperties);

$defaultcontext = 'web';
$migx->working_context = isset($modx->resource) ? $modx->resource->get('context_key') : $defaultcontext;

$getFamily = $modx->getOption('getFamily', $scriptProperties, '1');
$getOthers = $modx->getOption('getOthers', $scriptProperties, '1');

$rows1 = array();
$rows2 = array();

if (!empty($getFamily)) {
    $scriptProperties['where'] = '{"deleted":"0","zahlweise":"lastschrift","member_status":"Mitglied","Beitragstyp.name":"Familienmitgliedschaft","Family.id:!=":""}';
    $c = $migx->prepareQuery($xpdo, $scriptProperties);
    $rows1 = $migx->getCollection($c);
}

if (!empty($getOthers)) {
    $scriptProperties['where'] = '{"deleted":"0","zahlweise":"lastschrift","member_status":"Mitglied","Beitragstyp.name:!=":"Familienmitgliedschaft","Beitragstyp.beitrag:!=":"0"}';
    $c = $migx->prepareQuery($xpdo, $scriptProperties);
    $rows2 = $migx->getCollection($c);
}

$last_collect_date = $modx->getOption('last_collect_date', $scriptProperties, '0');

$rows = array_merge($rows1, $rows2);

$sum = 0;
foreach ($rows as &$row) {
    $row['debitor_name'] = $row['name'] . ' ' . $row['firstname'];
    $props = array();
    $props['default'] = $row['Beitragstyp_beitrag'];
    $props['typ'] = $row['Beitragstyp_id'];
    $props['when'] = $scriptProperties['collect_date'];
    $props['birthdate'] = $row['birthdate'];
    $row['betrag'] = $modx->runSnippet('mv_berechne_beitrag', $props);
    $row['signature_date'] = strftime('%Y-%m-%d', strtotime($row['eintritt']));
    $sum += $row['betrag'];
}

$count = count($rows);
$modx->setPlaceholder('transactions_count', $count);
$modx->setPlaceholder('time', time());
$modx->setPlaceholder('sum', $sum);
$modx->setPlaceholder('FRST_RCUR', 'RCUR');

$output = $migx->renderOutput($rows, $scriptProperties);

return $output;