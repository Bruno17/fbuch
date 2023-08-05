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

$update_uris = [
    ['uri'=>'danke-für-deine-anfrage.html',
    'new_uri'=>'danke-fuer-deine-anfrage.html'
    ],
    ['uri'=>'termine/',
    'new_uri'=>'termine'
    ]    
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
     'parent_uri'=>'termine',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'hidemenu'=>1,
     'resource_groups'=>['fbuch','fbuch_guest']
    ],
    ['pagetitle'=>'Termine/Reservierung',
     'alias'=>'termine',
     'context_key' => 'fbuch',
     'uri' => 'termine',
     'uri_override' => 1,
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch','fbuch_guest']
    ],
    ['pagetitle'=>'Meine Einladungslisten Einträge',
     'alias'=>'meine-einladungslisten-eintraege',
     'context_key' => 'fbuch',
     'uri' => 'termine/meine-einladungslisten-eintraege.html',
     'uri_override' => 0,
     'template_name'=>'fbuch Quasar',
     'published'=>1,
     'hidemenu'=>0,
     'parent_uri'=>'termine',
     'content'=>'[[$fbuch_Persoenliche_Mailinggruppen]]',
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'[[$fbuchAppMailinggruppen]]']]
    ],    
    ['pagetitle'=>'Danke für Deine Anfrage',
     'alias'=>'danke-fuer-deine-anfrage',
     'context_key' => 'fbuch',
     'uri' => 'danke-fuer-deine-anfrage.html',
     'uri_override' => 0,
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'hidemenu'=>1,
     'content'=>'[[$fbuch_danke_fuer_deine_anfrage]]',
     'richtext'=>0
    ]       

];