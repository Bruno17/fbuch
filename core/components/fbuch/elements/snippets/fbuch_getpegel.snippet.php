<?php

$url = 'https://www.pegelonline.wsv.de/webservices/rest-api/v2/stations/steinbach/W.json?includeCurrentMeasurement=true';

$show_pegel = $modx->getOption('show_pegel');

if (empty($show_pegel)){
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

$pegel_ok_notsteg = 200;//Oberkante Notsteg = 50%
$pegel_uk_warnschild = 240;//Unterkante Warnschild
$pegel_max = 400;//maximal angezeigter Pegel = 100%

$mm_percent = 0.25;//1cm entspricht 0,25%

$properties = array();
$properties['gp_current_pegel'] = $current_pegel;
$properties['gp_pegel_percent'] = str_replace(',','.',$current_pegel*$mm_percent);
$properties['gp_pegel_percent_rest'] = str_replace(',','.',100-($current_pegel*$mm_percent));
$properties['gp_pegel_uk_warnschild_percent'] = $pegel_uk_warnschild*$mm_percent;

return $modx->getChunk($tpl,$properties);