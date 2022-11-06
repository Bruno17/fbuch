<?php
$values = explode(',', $input);
$sum = 0;
$output = 0;
if (count($values) > 0) {
    $countedValues = 0;
    foreach ($values as $value) {
        $trimmedValue = trim($value);
        if (is_numeric($trimmedValue)) {
            ++$countedValues;
            $sum += $trimmedValue;
        }
    }

    if (0 < $countedValues) {
        $output = $sum / $countedValues;
    }
}

return $output;