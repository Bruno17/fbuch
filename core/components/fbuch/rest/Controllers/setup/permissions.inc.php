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
        'fbuch_clubhome'=>'1',
        'mv_member_admin'=>'1'            
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
    ['name'=>'fbuch_mailinglist_subscribe',
        'description'=>'Berechtigung, sich in Mailinglisten einzutragen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'1',
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
        'fbuch_clubhome'=>'1',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'1'
    ],
    ['name'=>'fbuch_view_datenames',
        'description'=>'Berechtigung Anmeldungen zu Terminen zu sehen',
        'fbuch_guest'=>'1',
        'fbuch_user'=>'1',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1'
    ], 
    ['name'=>'fbuch_remove_datenames',
        'description'=>'Berechtigung eingetragene Personen aus Terminen zu entfernen',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>''
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
    ],
    ['name'=>'fbuch_view_birthdate',
        'description'=>'User can view Birthdate and Age in fbuch',
        'fbuch_guest'=>'',
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'1'
    ],    
    ['name'=>'fbuch_create_names',
        'description'=>'Basisberechtigung neue Mitglieder als Gast anzulegen',
        'fbuch_guest'=>'',  
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'1',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'1'
    ],    
    ['name'=>'fbuch_edit_names',
        'description'=>'Basisberechtigung, Mitgliederdaten bearbeiten zu dürfen',
        'fbuch_guest'=>'',  
        'fbuch_user'=>'',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'1'        
    ],    
    ['name'=>'mv_administrate_members',
        'description'=>'Berechtigung, alle Mitgliederdaten bearbeiten zu dürfen',
        'fbuch_guest'=>'0',
        'fbuch_user'=>'0',
        'fbuch_instructor'=>'0',
        'fbuch_clubhome'=>'0',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'0'
    ],
    ['name'=>'mv_edit_membercompetencies',
        'description'=>'Berechtigung, Kompetenzstufen der Mitglieder und damit zusammenhängede Daten zu ändern',
        'fbuch_guest'=>'0',
        'fbuch_user'=>'0',
        'fbuch_instructor'=>'0',
        'fbuch_clubhome'=>'0',
        'mv_member_admin'=>'1',
        'mv_competency_editor'=>'1'
    ],
    ['name'=>'fbuch_lock_boat',
        'description'=>'Berechtigung, Boote zu sperren und freizugeben',
        'fbuch_guest'=>'0',
        'fbuch_user'=>'0',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'0',
        'mv_member_admin'=>'0',
        'mv_competency_editor'=>'0'
    ],
    ['name'=>'fbuch_edit_boat',
        'description'=>'Berechtigung, Boote zu sperren und freizugeben',
        'fbuch_guest'=>'0',
        'fbuch_user'=>'0',
        'fbuch_instructor'=>'1',
        'fbuch_clubhome'=>'0',
        'mv_member_admin'=>'0',
        'mv_competency_editor'=>'0'
    ]
];

