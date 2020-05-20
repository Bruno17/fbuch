<?php

$query = $modx->getOption('query', $scriptProperties, '');
$results = $modx->query($query);
$i = 1;
$km = 0;

if ($results) {
    while ($r = $results->fetch(PDO::FETCH_ASSOC)) {
        if ($i == 1) {
            $output = '<thead><tr>';
            $output .= '<th></th>';
            foreach ($r as $field => $value) {
                $output .= '<th>' . $field . '</th>';
            }
            $output .= '</tr></thead>';
            $output .= '<tbody>';
        }
        $output .= '<tr>';
        $output .= '<td>' . $i . '</td>';
        foreach ($r as $field => $value) {
            $output .= '<td>' . $value . '</td>';
            if ($field == 'km'){
                $km += $value;
            }
        }
        $output .= '</tr>';

        $i++;
    }
    $output .= '</tbody>';
}

$modx->setPlaceholder('km_total',$km);

return $output;