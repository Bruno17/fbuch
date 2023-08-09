<?php

$permissions = [
    ['name'=>'fbuch_create_fahrten',
        'description'=>'Berechtigung, neue Fahrten zu erstellen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_create_mailinglists',
        'description'=>'Berechtigung, Mailinglisten zu erstellen',
        'fbuch_guest'=>'',        
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'                
    ],
    ['name'=>'fbuch_create_termin',
        'description'=>'Berechtigung, Fahrtenbuch - Termine zu erstellen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_edit_fahrten',
        'description'=>'Berechtigung, Fahrten zu bearbeiten',
        'fbuch_guest'=>'',        
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_edit_mailinglists',
        'description'=>'Berechtigung, Mailinglisten zu bearbeiten und Personen hinzuzufügen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_edit_names',
        'description'=>'Berechtigung Namen zu bearbeiten',
        'fbuch_guest'=>'',  
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>''
    ],
    ['name'=>'fbuch_edit_old_entries',
        'description'=>'Berechtigung abgeschlossene Fahrten vom Vortag und Älter zu bearbeiten',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'',
        'fbuch_clubhome'=>''
    ],
    ['name'=>'fbuch_edit_termin',
        'description'=>'Berechtigung Termine zu bearbeiten',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>''
    ],
    ['name'=>'fbuch_view_birthdate',
        'description'=>'User can view Birthdate and Age in fbuch',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'',
        'fbuch_clubhome'=>''
    ],
    ['name'=>'fbuch_view_fahrten',
        'description'=>'Zugriff auf Fahrten im Fahrtenbuch',
        'fbuch_guest'=>'',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_view_names',
        'description'=>'Berechtigung Namen aus Mitgliederliste zu sehen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_view_datenames',
        'description'=>'Berechtigung Anmeldungen zu Terminen zu sehen',
        'fbuch_guest'=>'1',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],    
    ['name'=>'fbuch_add_persons_to_dates',
        'description'=>'Berechtigung, andere Personen in Termine einzutragen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ],
    ['name'=>'fbuch_accept_invite',
        'description'=>'Berechtigung, Termineinladungen anzunehmen',
        'fbuch_guest'=>'1',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>''
    ],
    ['name'=>'fbuch_read_datecomments',
        'description'=>'Berechtigung, Terminkommentare zu lesen',
        'fbuch_guest'=>'1',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ]
    
];

