<?php


$birthdate = strtotime($modx->getOption('birthdate', $scriptProperties, 0));
$when = $modx->getOption('when', $scriptProperties, '');
$alter = '';

if (!empty($birthdate)) {
    switch ($when) {
        case 'thisyear':
            $alter = strftime('%Y') - strftime('%Y', $birthdate);
            break;
        default:
            $when = strtotime($when);
            
            $day = date("d", $birthdate);
            $month = date("m", $birthdate);
            $year = date("Y", $birthdate);

            $cur_day = date("d", $when);
            $cur_month = date("m", $when);
            $cur_year = date("Y", $when);

            $calc_year = $cur_year - $year;

            if ($month > $cur_month)
                $alter = $calc_year - 1;
            elseif ($month == $cur_month && $day > $cur_day)
                $alter = $calc_year - 1;
            else
                $alter = $calc_year;

            break;

    }
}

return $alter;