<?php
$alter = $modx->runSnippet('mv_berechne_alter', $scriptProperties);
$typ = $modx->getOption('typ', $scriptProperties, '');
$output = $modx->getOption('default', $scriptProperties, '');

if ($typ == '1') {
    $c = $modx->newQuery('mvBeitragstyp');

    $c->where(array(
        'max_age:!=' => '0',
        'max_age:>=' => $alter,
        ''));
    $c->sortby('max_age');
    $c->limit('1');

    if ($object = $modx->getObject('mvBeitragstyp', $c)) {
        $output = $object->get('beitrag');
    }
}


return $output/2;