<?php
$show_pegel = $modx->getOption('show_pegel',$scriptProperties,'1');
$station = $modx->getOption('station',$scriptProperties,'');
$station_name = $modx->getOption('station_name',$scriptProperties,'');
$max_pegel = (int) $modx->getOption('max_pegel',$scriptProperties,'400');//maximal angezeigter Pegel = 100%
$max_pegel_ok = (int) $modx->getOption('max_pegel_ok',$scriptProperties,'200');//Oberkante Notsteg
$pegel_danger = (int) $modx->getOption('pegel_danger',$scriptProperties,'240');//Unterkante Warnschild

$url = 'https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/' . $station . '/W.json?includeCurrentMeasurement=true';

if (empty($station) || empty($show_pegel)){
    return '';
}

$tpl = $modx->getOption('tpl',$scriptProperties,'fbuchPegel');

//  Initiate curl
$ch = curl_init();
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// Execute
$result=curl_exec($ch);
// Closing
curl_close($ch);

// Will dump a beauty json :3
$data = json_decode($result, true);

//echo '<pre>' . print_r($data['currentMeasurement'],1) . '</pre>';

$current_pegel = $data['currentMeasurement']['value'];

//$mm_percent = 0.25;//1cm entspricht 0,25%
$mm_percent = 1;

$current_pegel_percent = ($current_pegel * 100) /  $max_pegel;
$max_pegel_ok_percent = ($max_pegel_ok * 100) /  $max_pegel; 
$pegel_danger_percent = ($pegel_danger * 100) /  $max_pegel;  

$properties = $scriptProperties;
$properties['gp_current_pegel'] = $current_pegel;
$properties['gp_pegel_percent'] = str_replace(',','.',$current_pegel_percent);
$properties['gp_max_pegel_ok_percent'] = str_replace(',','.',$max_pegel_ok_percent);
$properties['gp_pegel_percent_rest'] = str_replace(',','.',100-($current_pegel*$mm_percent));
$properties['gp_pegel_danger_percent'] = str_replace(',','.',($pegel_danger_percent));
return $modx->getChunk($tpl,$properties);