<?php
$modx = &$this->modx;

$id = $modx->getOption('object_id',$_REQUEST);

//echo '<pre>' . print_r($_REQUEST,1) . '</pre>';

//echo '<pre>' . print_r(json_decode($this->customconfigs['tabs'],1),1) . '</pre>';

$file = $modx->getOption('core_path') . 'components/fbuch/customchunks/customchunks.js';
$input = '';
if (file_exists($file)) {
    $input = json_decode(file_get_contents($file), true);
}

$input = is_array($input) ? $input : array();

$groups = array();
$groups[] = array();

$i=1;
foreach ($input as $name => $row){
    $group = $modx->getOption('group',$row);
    $group = empty($group) ? $name : $group;
    if (!isset($groups[$group])){
        $groups[$group] = array();
    }
    $row['field'] = $name;
    $groups[$group][] = $row;
}

$numgroups = array();
foreach ($groups as $group){
    $numgroups[] = $group;    
}

$currentgroup = $numgroups[$id];
//echo '<pre>' . print_r($currentgroup,1) . '</pre>';
$tabs = array();
foreach ($currentgroup as $row){
    $field = array();
    $field['field'] = $row['field'];
    $field['caption'] = $row['field'];
    $field['inputTVtype'] = $row['inputTVtype'];
    
    if ($chunk = $modx->getObject('modChunk',array('name'=>$row['field']))){
        //$field['default'] = $chunk->get('snippet');
        $field['description'] = $chunk->get('description');
    }
    
    $caption = $modx->getOption('tab',$row); 
    $caption = empty($caption) ? 'Individualisierungen' : $caption;
    if (!isset($tabs[$caption])){
        $tabs[$caption] = array();
        $tabs[$caption]['caption'] = $caption;
    }    
    if (!isset($tabs[$caption]['fields'])){
        $tabs[$caption]['fields'] = array();
    }
    $tabs[$caption]['fields'][] = $field;    
       
}


$formtabs = array();
foreach ($tabs as $tab){
    $formtabs[] = $tab;
}

//echo '<pre>' . print_r($formtabs,1) . '</pre>';

$this->customconfigs['tabs'] = $formtabs; 