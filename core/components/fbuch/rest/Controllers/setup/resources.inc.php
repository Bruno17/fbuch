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
    ],
    ['uri'=>'termine/einladungslisten.html',
     'context_key' => 'fbuch',
     'new_uri'=>'listen/einladungslisten.html'
    ],
    ['uri'=>'listen/bootsliste/',
     'context_key' => 'fbuch',
     'new_uri'=>'listen/bootsliste'
    ],    
    ['uri'=>'listen/bootsliste/boots-details/',
     'context_key' => 'fbuch',
     'new_uri'=>'listen/bootsliste/boots-details'
    ],
    ['uri'=>'listen/namen/',
    'context_key' => 'fbuch',
    'new_uri'=>'listen/namen'
    ],
    ['uri'=>'listen/bootsschäden.html',
    'context_key' => 'fbuch',
    'new_uri'=>'listen/bootsschaeden'
    ]       
];

$update_resources = [
    ['pagetitle'=>'Fahrtenbuch',
     'alias'=>'fahrtenbuch',
     'context_key' => 'fbuch',
     'uri' => 'fahrtenbuch/',
     'uri_override' => 0,
     'isfolder' => 1,
     'template_name'=>'',
     'published'=>1,
     'richtext'=>0,
     'resource_groups'=>['fbuch']
    ], 
    ['pagetitle'=>'Termine/Reservierung',
     'alias'=>'termine',
     'context_key' => 'fbuch',
     'uri' => 'termine',
     'uri_override' => 1,
     'isfolder' => 1,
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch','fbuch_guest']
    ],
    ['pagetitle'=>'Listen',
     'alias'=>'listen',
     'context_key' => 'fbuch',
     'uri' => 'listen/',
     'uri_override' => 0,
     'isfolder' => 1,
     'template_name'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch']
    ], 
    ['pagetitle'=>'Auswertungen',
     'alias'=>'auswertungen',
     'context_key' => 'fbuch',
     'uri' => 'auswertungen/',
     'uri_override' => 0,
     'isfolder' => 1,
     'template_name'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch']
    ],                                   
    ['pagetitle'=>'Login',
     'longtitle'=>'[[$$fbuch_login_pagetitle]]',
     'menutitle'=>'Login/Fragen',
     'alias'=>'login',
     'context_key' => 'fbuch',
     'uri' => 'login/',
     'uri_override' => 0,
     'isfolder' => 1,
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'content'=>'[[$fbuch_Login]]',
     'richtext'=>0
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
    ['pagetitle'=>'Kalenderansicht',
     'alias'=>'kalenderansicht',
     'context_key' => 'fbuch',
     'uri' => 'termine/kalenderansicht.html',
     'parent_uri'=>'termine',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'resource_groups'=>['fbuch'],
     'content'=>'[[$fbuch_kalenderansicht]]',
     'tvs'=>[
        ['name'=>'scripts','value'=>'[[$fbuch_kalenderansicht_scripts]]'],
        ['name'=>'headscripts','value'=>'[[$fbuch_kalenderansicht_headscripts]]']
     ]
    ],
    ['pagetitle'=>'Ranglisten',
     'alias'=>'ranglisten',
     'context_key' => 'fbuch',
     'uri' => 'auswertungen/ranglisten.html',
     'parent_uri'=>'auswertungen/',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'resource_groups'=>['fbuch'],
     'content'=>'',
     'tvs'=>[
        ['name'=>'scripts','value'=>'[[fbuchGetAssetsFiles? &folder=`quasar/components/fahrtenbuch` &wrapper=`<script type="x-template" id="[[+filename]]">[[+output]]</script>`]]  ']
     ]
    ],
    ['pagetitle'=>'Boote',
     'alias'=>'ranglistenboote',
     'context_key' => 'fbuch',
     'uri' => 'auswertungen/boote.html',
     'uri_override' => 1,
     'parent_uri'=>'auswertungen/',
     'template_name'=>'fbuch Quasar 2',
     'published'=>1,
     'resource_groups'=>['fbuch'],
     'content'=>'[[-$fbuch_auswertung_boote]]',
     'tvs'=>[
        ['name'=>'scripts','value'=>'
        [[fbuchGetAssetsFiles? &folder=`quasar/components/fahrtenbuch` &wrapper=`<script type="x-template" id="[[+filename]]">[[+output]]</script>`]]  
        [[fbuchGetAssetsFiles? &folder=`quasar/components/ranglisten` &wrapper=`<script type="x-template" id="[[+filename]]">[[+output]]</script>`]]
        ']
     ]
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
    ['pagetitle'=>'Namensliste',
     'alias'=>'namen',
     'context_key' => 'fbuch',
     'uri' => 'listen/namen',
     'uri_override' => 1,
     'isfolder' => 1,
     'parent_uri'=>'listen/',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ], 
    ['pagetitle'=>'Bootsliste',
     'alias'=>'bootsliste',
     'context_key' => 'fbuch',
     'uri' => 'listen/bootsliste',
     'uri_override' => 1,
     'isfolder' => 1,     
     'parent_uri'=>'listen/',
     'template_name'=>'fbuch Quasar 2',
     'hidemenu'=>0,
     'published'=>1,
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],
    ['pagetitle'=>'Boots Details',
     'alias'=>'boots-details',
     'context_key' => 'fbuch',
     'uri' => 'listen/bootsliste/boots-details',
     'uri_override' => 1,
     'isfolder' => 1,     
     'parent_uri'=>'listen/bootsliste',
     'template_name'=>'fbuch Quasar 2',
     'hidemenu'=>0,
     'published'=>1,
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'
     [[fbuchGetAssetsFiles? &folder=`quasar/components/fahrtenbuch` &wrapper=`<script type="x-template" id="[[+filename]]">[[+output]]</script>`]]  
     ']]
    ],                          
    ['pagetitle'=>'Kompetenzstufen',
     'alias'=>'kompetenzstufen',
     'context_key' => 'fbuch',
     'uri' => 'listen/kompetenzstufen.html',
     'uri_override' => 0,
     'parent_uri'=>'listen/',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'[[*content]]',
     'hidemenu'=>0,
     'resource_groups'=>[],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],      
    ['pagetitle'=>'Einladungslisten',
     'alias'=>'einladungslisten',
     'context_key' => 'fbuch',
     'uri' => 'listen/einladungslisten.html',
     'uri_override' => 0,
     'parent_uri'=>'listen/',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'',
     'published'=>1,
     'hidemenu'=>0,
     'resource_groups'=>['fbuch_instructor'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],
    ['pagetitle'=>'Bootsschäden',
     'alias'=>'bootsschaeden',
     'context_key' => 'fbuch',
     'uri' => 'listen/bootsschaeden',
     'uri_override' => 1,
     'isfolder' => 1,     
     'parent_uri'=>'listen/',
     'template_name'=>'fbuch Quasar 2',
     'hidemenu'=>0,
     'published'=>1,
     'content'=>'',
     'resource_groups'=>['fbuch'],
     'tvs'=>[['name'=>'scripts','value'=>'']]
    ],                                           
    ['pagetitle'=>'LoginByCode',
     'alias'=>'anmelden',
     'context_key' => 'fbuch',
     'uri' => 'termine/anmelden.html',
     'uri_override' => 0,
     'parent_uri'=>'termine',
     'template_name'=>'fbuch Quasar 2',
     'content'=>'',
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