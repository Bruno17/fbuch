<?php

$delete_resources = [
    ['uri'=>'fahrtenbuch/rudern-dd/',
     'context_key' => 'fbuch'    
    ],
    ['uri'=>'fahrtenbuch/rudern-dd/ruderverbot.html',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'fahrtenbuch/rudern/',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'fahrtenbuch/fahrtenbuch-fuhrpark.html',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'fahrtenbuch/ergotrack.html',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'fahrtenbuch/fahrtenbuch-ergometer-neu/',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'termine/rudern.html',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'termine/personen-anmelden.html',
     'context_key' => 'fbuch'
    ],
    ['uri'=>'termine/terminanmeldung.html',
     'context_key' => 'fbuch'
    ]

];

$update_uris = [
    ['uri'=>'danke-für-deine-anfrage.html',
     'context_key' => 'fbuch',
     'new_uri'=>'danke-fuer-deine-anfrage.html'
    ],
    ['uri'=>'termine/',
     'context_key' => 'fbuch',
     'new_uri'=>'termine'
    ]    
];

$update_resources = [
    ['pagetitle'=>'Login',
     'longtitle'=>'[[$$fbuch_login_pagetitle]]',
     'menutitle'=>'Login/Fragen',
     'alias'=>'login',
     'context_key' => 'fbuch',
     'uri' => 'login/',
     'uri_override' => 0,
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'content'=>'[[$fbuch_Login]]',
     'richtext'=>0
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
    ['pagetitle'=>'Fahrtenbuch',
     'alias'=>'fahrtenbuch',
     'context_key' => 'fbuch',
     'uri' => 'fahrtenbuch/fahrtenbuch.html',
     'parent_uri'=>'fahrtenbuch/',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'resource_groups'=>['fbuch']
    ],    
    ['pagetitle'=>'Meine Einladungslisten Einträge',
     'alias'=>'meine-einladungslisten-eintraege',
     'context_key' => 'fbuch',
     'uri' => 'termine/meine-einladungslisten-eintraege.html',
     'uri_override' => 0,
     'parent_uri'=>'termine',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],
    ['pagetitle'=>'Einladungslisten',
     'alias'=>'einladungslisten',
     'context_key' => 'fbuch',
     'uri' => 'listen/einladungslisten.html',
     'uri_override' => 0,
     'parent_uri'=>'termine',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch_instructor'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],                                       
    ['pagetitle'=>'LoginByCode',
     'alias'=>'anmelden',
     'context_key' => 'fbuch',
     'uri' => 'termine/anmelden.html',
     'uri_override' => 0,
     'parent_uri'=>'termine',
     'template_name'=>'fahrtenbuch',
     'content'=>'[[!fbuchLoginByCode]]',
     'published'=>1,
     'hidemenu'=>1
    ],
    ['pagetitle'=>'Login und Redirect',
     'alias'=>'login-und-redirect',
     'context_key' => 'fbuch',
     'uri' => 'login/login-und-redirect.html',
     'parent_uri'=>'login/',
     'content'=>'[[$fbuch_login_und_redirect]]',
     'published'=>1,
     'hidemenu'=>1,
     'richtext'=>0
    ]
];