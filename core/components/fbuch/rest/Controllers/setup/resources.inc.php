<?php

$delete_resources = [
    'fahrtenbuch/rudern-dd/',
     'fahrtenbuch/rudern-dd/ruderverbot.html',
     'fahrtenbuch/ergometer/',
     'fahrtenbuch/rudern/',
     'fahrtenbuch/fahrtenbuch-fuhrpark.html',
     'fahrtenbuch/ergotrack.html',
     'fahrtenbuch/fahrtenbuch-ergometer-neu/'
];

$update_resources = [
    ['pagetitle'=>'Fahrtenbuch',
     'alias'=>'fahrtenbuch',
     'context_key' => 'fbuch',
     'uri' => 'fahrtenbuch/fahrtenbuch.html',
     'parent_uri'=>'fahrtenbuch/',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'resource_groups'=>['fbuch']
    ],
    ['pagetitle'=>'Terminanmeldung',
     'alias'=>'terminanmeldung',
     'context_key' => 'fbuch',
     'uri' => 'termine/terminanmeldung.html',
     'parent_uri'=>'termine/',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'hidemenu'=>1,
     'resource_groups'=>['fbuch','fbuch_guest']
    ]    

];