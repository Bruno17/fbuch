<?php

$uploadpath = $modx->getOption('base_path') . 'csvimport/';

$config = $modx->migx->customconfigs;

$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);
    $packageName = isset($packageNames[0]) ? $packageNames[0] : '';

    if (count($packageNames) == '1') {
        //for now connecting also to foreign databases, only with one package by default possible
        $xpdo = $modx->migx->getXpdoInstanceAndAddPackage($config);
    } else {
        //all packages must have the same prefix for now!
        foreach ($packageNames as $packageName) {
            $packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
            $modelpath = $packagepath . 'model/';
            if (is_dir($modelpath)) {
                $modx->addPackage($packageName, $modelpath, $prefix);
            }

        }
        $xpdo = &$modx;
    }
} else {
    $xpdo = &$modx;
}


set_time_limit(1000);

$filename = 'fahrten.csv';
$idx = 1;
if (($handle = fopen($uploadpath . $filename, "r")) !== false) {
    
    $classname = $config['classname'];
    $c = $modx->newQuery($classname);
    
    if ($collection = $modx->getIterator($classname, $c)) {
        foreach ($collection as $object) {
            $object->remove();
        }
    }
    $tablename = $modx->getTableName($classname);
    $modx->exec("alter table {$tablename} AUTO_INCREMENT =1");
    
    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        $row = array();
        if ($idx == 1) {
            $fields = $data;
        } else {
            $num = count($data);

            for ($c = 0; $c < $num; $c++) {
                $field = $fields[$c];
                $row[$field] = mb_convert_encoding($data[$c], "UTF-8", "ISO-8859-1");
            }
            
            //print_r($row);

            
            $data = array();
            $data['date'] = strftime('%Y-%m-%d', strtotime($row['DATUM']));
            $data['date_end'] = strftime('%Y-%m-%d', strtotime($row['BIS']));
            $data['km'] = $row['KM'];
            $data['wfahrt'] = $row['WFAHRT'] == 'J' ? 1 : 0;
            $data['direction'] = $row['RICHTUNG'];
            $data['start_time'] = $row['START'];
            $data['end_time'] = $row['ANKUNFT'];
            $data['fahrtid_old'] = $row['fahrtid'];
            $data['bootid_old'] = $row['bootid'];
            if ($boot_o = $modx->getObject('fbuchBoot',array('bootid_old' => $row['bootid']))){
                $data['boot_id'] = $boot_o->get('id');    
            }            

            if ($object = $modx->newObject($classname)) {
                $object->fromArray($data);
                $object->save();
            }
            

        }
        $idx++;
    }
    fclose($handle);
}

return $modx->error->success();
