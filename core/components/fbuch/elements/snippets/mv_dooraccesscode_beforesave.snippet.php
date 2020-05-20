<?php

$properties = $modx->getOption('scriptProperties',$scriptProperties);
$data = $modx->getOption('data',$properties);

$import = $modx->getOption('import',json_decode($data,1));



$errors = array();
if (!empty($import)){
    //$array_csv = array();
    $lines = explode("\n", $import);
    $count = 0;
    foreach ($lines as $idx=>$line) {
        $idx++;
        $array_csv = str_getcsv($line,';');
        if (empty($array_csv[0])){
            $errors[] = 'Fehler in Zeile ' . $idx . '. kein Code angegeben'; 
            continue;            
        }
        $code = intval($array_csv[0]) ;
        if (!ctype_digit($array_csv[0])){
            $errors[] = 'Fehler in Zeile ' . $idx . '. Code ist keine Zahl'; 
            continue;
        }
        $c = $modx->newQuery('mvDoorAccesscode');
        $c->where(array('code'=>$code));
        if ($object = $modx->getObject('mvDoorAccesscode',$c)){
            $errors[] = 'Fehler in Zeile ' . $idx . '. Code bereits vorhanden'; 
            continue;            
        }
        $member_id = $array_csv[1];
        if (!empty($member_id)){
            $c = $modx->newQuery('mvMember');
            $c->where(array('id'=>$member_id));
            if ($member = $modx->getObject('mvMember',$c)){
          
            } else {
                $errors[] = 'Fehler in Zeile ' . $idx . '. ungültige member_id'; 
                continue;              
            }
            $c = $modx->newQuery('mvDoorAccesscode');
            $c->where(array('member_id'=>$member_id));
            if ($object = $modx->getObject('mvDoorAccesscode',$c)){
                $errors[] = 'Fehler in Zeile ' . $idx . '. member_id bereits vorhanden'; 
                continue;            
            }            
        }
        $other_person = $array_csv[2];
        if (!empty($other_person)){
            $c = $modx->newQuery('mvDoorAccesscode');
            $c->where(array('other_person'=>$other_person));
            if ($object = $modx->getObject('mvDoorAccesscode',$c)){
                $errors[] = 'Fehler in Zeile ' . $idx . '. Nichtmitglied mit gleichem Namen bereits vorhanden'; 
                continue;            
            }            
        }        
        $assignedon = $array_csv[3];
        $blocked = $array_csv[4];
        if (!in_array($blocked,array('0','1','2'))){
            $errors[] = 'Fehler in Zeile ' . $idx . '. ungültiger Wert für blocked'; 
            continue;            
        }

        $object = $modx->newObject('mvDoorAccesscode');
        $object->set('code',$code);
        $object->set('member_id',$member_id);
        $object->set('other_person',$other_person);
        $object->save();
        $count++;
        
    }

    $errors[] = $count . ' Codes erfolgreich importiert';
    $result['error']= implode('<br>' , $errors); 

    return json_encode($result);    
    
}

return '';