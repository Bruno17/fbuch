<?php

$file = $modx->getOption('core_path') . 'components/fbuch/customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

$modx->log(modX::LOG_LEVEL_INFO, 'resolve custom chunks');

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        foreach ($input as $name => $item) {
            if (empty($item['deleted'])) {
                $original_name = str_replace('custom_', '', $name);
                if ($original_chunk = $modx->getObject('modChunk', array('name' => $original_name))) {
                    if ($custom_chunk = $modx->getObject('modChunk', array('name' => $name))) {

                    } else {

                        if (substr($name, 0, 7) == 'custom_') {
                            if ($category = $modx->getObject('modCategory', array('category' => 'fbuchcustom'))) {

                            } else {
                                $category = $modx->newObject('modCategory');
                                $category->set('category', 'fbuchcustom');
                                $category->save();
                            }
                            $custom_chunk = $modx->newObject('modChunk');
                            $custom_chunk->fromArray($original_chunk->toArray());
                            $custom_chunk->set('name', $name);
                            $custom_chunk->set('category', $category->get('id'));
                            $custom_chunk->save();
                        }
                    }
                }
            }
        }
        break;

}


return true;
