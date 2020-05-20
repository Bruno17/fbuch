<?php

$mode = $modx->getOption('mode', $scriptProperties, 'offset_ltf');
$total = explode(',', $modx->getOption('total', $scriptProperties, 0));
$limit = $modx->getOption('limit', $scriptProperties, 0);
$grid_id = $modx->getOption('grid_id', $scriptProperties, '');
$lastDate = explode(',', $modx->getOption('lastDate', $scriptProperties, ''));
$firstDate = explode(',', $modx->getOption('firstDate', $scriptProperties, ''));
$nextDate = explode(',', $modx->getOption('nextDate', $scriptProperties, ''));
$charfield = $modx->getOption('charfield', $scriptProperties, '');
$currentChar = $modx->getOption('currentChar', $scriptProperties, '');
$default_dir = $modx->getOption('defaultDir', $scriptProperties, 'DESC');
$maxoffset = $offset = $total[0] - $limit;

$dir = $modx->getOption('dir', $_REQUEST, '');
$active_id = $modx->getOption('active_id', $_REQUEST, '');
$current_offset = $modx->getOption('offset', $_REQUEST, '');
$type = $modx->getOption('type', $_REQUEST, '');

switch ($mode) {
    case 'offset_ltf':
        $modx->setPlaceholder($grid_id . '_lastactive', 'active');
        if (!empty($dir) && !empty($current_offset)) {
            switch ($dir) {
                case 'up':
                    $offset = $current_offset - $limit;
                    //$modx->setPlaceholder($grid_id . '_lastactive','');
                    //$modx->setPlaceholder($grid_id . '_firstactive','active');
                    break;
                case 'down':
                    $offset = $current_offset + $limit;
                    $modx->setPlaceholder($grid_id . '_lastactive', '');
                    $modx->setPlaceholder($grid_id . '_firstactive', 'active');
                    break;
                case 'none':
                    if (!empty($active_id)) {
                        $offset = $current_offset;
                        $modx->setPlaceholder($grid_id . '_lastactive', '');
                        $modx->setPlaceholder($grid_id . '_firstactive', '');
                        $modx->setPlaceholder($grid_id . '_' . $active_id . '_active', 'active');
                    }
                    break;
            }
        }
        break;
    default:
        $modx->setPlaceholder($grid_id . '_firstactive', 'active');
        break;
}

switch ($mode) {
    case 'offset_ltf':
        if ($offset > $maxoffset) {
            $modx->setPlaceholder($grid_id . '_firstlast_offset', 'lastoffset');
            $modx->setPlaceholder($grid_id . '_lastactive', 'active');
            $modx->setPlaceholder($grid_id . '_firstactive', '');
            $offset = $maxoffset;
        }

        if ($offset < 0) {
            $offset = 0;
        }
        $modx->setPlaceholder($grid_id . '_offset', $offset);
        break;
    case 'char':
        $char = $currentChar;
        if ($dir == 'none' && !empty($active_id)) {
            $value = $modx->getOption($charfield, $_REQUEST, '');
            $char = substr($value, 0, 1);
            $modx->setPlaceholder($grid_id . '_lastactive', '');
            $modx->setPlaceholder($grid_id . '_firstactive', '');
            $modx->setPlaceholder($grid_id . '_' . $active_id . '_active', 'active');
        }

        $modx->setPlaceholder('atoz_char', $char);
        $modx->setPlaceholder($grid_id . '_offset', $char);
        break;
    case 'date':
        $date = '';

        foreach ($nextDate as $nd) {
            if ($nd > $date) {
                $date = $nd;
            }
        }

        if ($dir == 'none' && !empty($active_id)) {
            $date = $modx->getOption('date', $_REQUEST, '');
            $modx->setPlaceholder($grid_id . '_lastactive', '');
            $modx->setPlaceholder($grid_id . '_firstactive', '');
            $modx->setPlaceholder($grid_id . '_' . $active_id . '_active', 'active');
        }

        $today = strftime('%Y-%m-%d 00:00:00');
        if (!empty($dir) && !empty($current_offset)) {
            $date_where = array();
            switch ($dir) {
                case 'up':
                    $date = '';
                    foreach ($nextDate as $nd) {
                        if (!empty($nd) && $nd > $date) {
                            $date = $nd;
                        }
                    }
                    break;
                case 'down':
                    $date = '9999-99-99 99:99:99';
                    //echo 'nextdate:';print_r($nextDate);echo '<br>';
                    foreach ($nextDate as $nd) {
                        if (!empty($nd) && $nd < $date) {
                            $date = $nd;
                        }
                    }
                    if ($date == '9999-99-99 99:99:99') {
                        //echo 'lastdate:';print_r($lastDate);echo '<br>';
                        $date='';
                        foreach ($lastDate as $nd) {
                            if (!empty($nd) && $nd > $date) {
                                $date = $nd;
                            }
                        }
                        if ($type == 'dragdrop' && $today > $date) {
                            $date = $today;
                        }
                    }

                    break;
                case 'none':
                    //if we are on dragdrop-page, use the offset as current date
                    if ($type == 'dragdrop') {
                        $date = $current_offset;
                    }
                    break;
            }
        }
        
        if ($grid_id=='FahrtenDragdrop' && empty($dir)){
            $date = $today;
        }

        $date_where = array();
        $date_where['date:>='] = strftime('%Y-%m-%d 00:00:00', strtotime($date));
        $date_where['date:<='] = strftime('%Y-%m-%d 23:59:59', strtotime($date));

        $modx->setPlaceholder('date_where', json_encode($date_where));
        $modx->setPlaceholder($grid_id . '_offset', $date);
        break;
    case 'nextdate':
        $date = strftime('%Y-%m-%d 23:59:59');

        $date_where = array();
        if ($default_dir == 'DESC') {
            $date_where['date:<='] = $date;
        } else {
            $date_where['date:>='] = $date;
        }

        $modx->setPlaceholder('nextdate_dir', $default_dir);

        if (!empty($dir) && !empty($current_offset)) {
            $date_where = array();
            switch ($dir) {
                case 'up':
                    $date_where['date:<'] = strftime('%Y-%m-%d 00:00:00', strtotime($current_offset));
                    $modx->setPlaceholder('nextdate_dir', 'DESC');
                    break;
                case 'down':
                    $date_where['date:>'] = strftime('%Y-%m-%d 23:59:59', strtotime($current_offset));
                    $modx->setPlaceholder('nextdate_dir', 'ASC');
                    break;
                case 'none':
                    if (!empty($active_id)) {
                        $date = $modx->getOption('active_id', $_REQUEST, '');
                        $date_where['date:>'] = strftime('%Y-%m-%d 23:59:59', strtotime($date));
                        $modx->setPlaceholder($grid_id . '_lastactive', '');
                        $modx->setPlaceholder($grid_id . '_firstactive', '');
                        $modx->setPlaceholder($grid_id . '_' . $active_id . '_active', 'active');
                    }
                    break;
            }
        }

        $modx->setPlaceholder('nextdate_where', json_encode($date_where));
        $modx->setPlaceholder($grid_id . '_offset', $date);
        break;
}


return '';