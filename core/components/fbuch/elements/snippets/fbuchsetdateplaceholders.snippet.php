<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchSetDatePlaceholders']);
$mode = $modx->getOption('mode', $scriptProperties, 'offset_ltf');
$total = $modx->getOption('total', $scriptProperties, 0);
$limit = $modx->getOption('limit', $scriptProperties, 0);
$grid_id = $modx->getOption('grid_id', $scriptProperties, '');
$lastDate = $modx->getOption('lastDate', $scriptProperties, '');
$maxoffset = $offset = $total - $limit;

$dir = $modx->getOption('dir', $_REQUEST, '');
$active_id = $modx->getOption('active_id', $_REQUEST, '');
$current_offset = $modx->getOption('offset', $_REQUEST, '');


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
    case 'offset':
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
    case 'date':
        $date_where = array();
        $date_where['date:>='] = strftime('%Y-%m-%d 00:00:00', strtotime($lastDate));
        $date_where['date:<='] = strftime('%Y-%m-%d 23:59:59', strtotime($lastDate));

        $modx->setPlaceholder('date_where', json_encode($date_where));
        break;
}


return '';