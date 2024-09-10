<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'fbuchGetColor']);
$colors = [
'red' => '#f44336',
'pink' => '#e91e63',
'purple' => '#9c27b0',
'deep-purple' => '#673ab7',
'indigo' => '#3f51b5',
'blue' => '#2196f3',
'light-blue' => '#03a9f4',
'cyan' => '#00bcd4',
'teal' => '#009688',
'green' => '#4caf50'
];

return $colors[$input];