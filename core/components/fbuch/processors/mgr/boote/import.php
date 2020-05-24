<?php

$uploadpath = $modx->getOption('base_path') . 'csvimport/';

function cleartable($classname) {
    global $modx;

    $c = $modx->newQuery($classname);

    if ($collection = $modx->getIterator($classname, $c)) {
        foreach ($collection as $object) {
            $object->remove();
        }
    }
    $tablename = $modx->getTableName($classname);
    $modx->exec("alter table {$tablename} AUTO_INCREMENT =1");
}


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

$filename = 'boote.csv';
$idx = 1;
if (($handle = fopen($uploadpath . $filename, "r")) !== false) {

    $classname = $config['classname'];

    cleartable($classname);

    $gattung_classname = 'fbuchBootsGattung';
    cleartable($gattung_classname);

    $gruppe_classname = 'fbuchBootsNutzergruppe';
    cleartable($gruppe_classname);


    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        $row = array();
        if ($idx == 1) {
            $fields = $data;
        } else {
            $num = count($data);

            for ($c = 0; $c < $num; $c++) {
                $field = $fields[$c];
                //$row[$field] = mb_convert_encoding($data[$c], "UTF-8", "ISO-8859-1");
                $row[$field] = $data[$c];
            }

            $data = array();

            //print_r($row);
            $gattung_ids = array();
            $shortnames = explode(',', $row['TypeVariant']);
            if ($row['LastVariant'] > count($shortnames)) {
                $row['LastVariant'] = count($shortnames);
            }
            if (is_array($shortnames) && count($shortnames) > 0) {
                $idx = 1;
                foreach ($shortnames as $shortname) {
                    if ($gattung = $modx->getObject($gattung_classname, array('shortname' => $shortname))) {

                    } else {
                        $gattung = $modx->newObject($gattung_classname);
                        $gattung->set('shortname', $shortname);
                        $gattung->save();
                    }
                    if ($row['LastVariant'] == $idx) {
                        $gattung_id = $gattung->get('id');
                    }

                    $gattung_ids[] = $gattung->get('id');
                    $idx++;
                }

            }

            if (!empty($row['Gruppe'])) {
                if ($gruppe = $modx->getObject($gruppe_classname, array('name' => $row['Gruppe'], 'color_name' => $row['Farbe']))) {

                } else {
                    $gruppe = $modx->newObject($gruppe_classname);
                    $gruppe->set('name', $row['Gruppe']);
                    $gruppe->set('color_name', $row['Farbe']);
                    $gruppe->save();
                }
                $data['nutzergruppe'] = $gruppe->get('id');
            }
           
            $data['gattung_ids'] = implode('||', $gattung_ids);
            $data['gattung_id'] = $gattung_id;
            $data['name'] = $row['Name'];
            $data['import_typevariant'] = $row['TypeVariant'];
            $data['import_lastvariant'] = $row['LastVariant'];
            $data['type'] = $row['Type'];
            $data['owner'] = $row['Owner'];
            $data['serial_no'] = $row['SerialNo'];
            $data['manufacturer'] = $row['Manufacturer'];
            $data['model'] = $row['Model'];
            $data['manufaction_year'] = $row['ManufactionDate'];
            $data['purchase_date'] = $row['PurchaseDate'];
            $data['purchase_price'] = $row['PurchasePrice'];
            $data['versicherungs_summe'] = $row['Versicherungssumme'];
            $data['efa_id'] = $row['Id'];
            $data['import_gruppe'] = $row['Gruppe'];
            $data['import_farbe'] = $row['Farbe'];

            //print_r($data);


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
