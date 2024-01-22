{
  "id":206,
  "name":"mv_member_states",
  "formtabs":[
    {
      "MIGX_id":306,
      "caption":"",
      "print_before_tabs":"0",
      "fields":[
        {
          "MIGX_id":1330,
          "field":"state",
          "caption":"Mitglieder Status",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":1
        },
        {
          "MIGX_id":1331,
          "field":"description",
          "caption":"Beschreibung",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"textarea",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":2
        },
        {
          "MIGX_id":1332,
          "field":"add_to_usergroups",
          "caption":"mit folgenden Benutzergruppen verbinden (getrennt durch Komma)",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":3
        },
        {
          "MIGX_id":1333,
          "field":"remove_from_usergroups",
          "caption":"aus folgenden Benutzergruppen entfernen",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":4
        },
        {
          "MIGX_id":1334,
          "field":"can_be_added_to_entry",
          "caption":"can_be_added_to_entry",
          "pos":5
        },
        {
          "MIGX_id":1335,
          "field":"can_be_invited",
          "caption":"can_be_invited",
          "pos":6
        },
        {
          "MIGX_id":1336,
          "field":"can_self_register",
          "caption":"can_self_register",
          "description":"Kann sich selbst \u00fcber die Email Login Funktion als Benutzer anlegen.",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":7
        },
        {
          "MIGX_id":1346,
          "field":"needed_set_permission",
          "caption":"needed_set_permission",
          "pos":8
        }
      ],
      "pos":1
    }
  ],
  "contextmenus":"update||remove",
  "actionbuttons":"addItem",
  "columnbuttons":"",
  "filters":"",
  "extended":{
    "migx_add":"",
    "disable_add_item":"",
    "add_items_directly":"",
    "formcaption":"",
    "update_win_title":"",
    "win_id":"mv_member_states",
    "maxRecords":"",
    "addNewItemAt":"bottom",
    "media_source_id":"",
    "multiple_formtabs":"",
    "multiple_formtabs_label":"",
    "multiple_formtabs_field":"",
    "multiple_formtabs_optionstext":"",
    "multiple_formtabs_optionsvalue":"",
    "actionbuttonsperrow":4,
    "winbuttonslist":"",
    "extrahandlers":"",
    "filtersperrow":4,
    "packageName":"fbuch",
    "classname":"mvMemberState",
    "task":"",
    "getlistsort":"",
    "getlistsortdir":"",
    "sortconfig":"",
    "gridpagesize":"",
    "use_custom_prefix":"0",
    "prefix":"",
    "grid":"",
    "gridload_mode":1,
    "check_resid":1,
    "check_resid_TV":"",
    "join_alias":"",
    "has_jointable":"yes",
    "getlistwhere":"",
    "joins":"",
    "hooksnippets":"",
    "cmpmaincaption":"",
    "cmptabcaption":"Mitglieder Status",
    "cmptabdescription":"Mitglieder Status",
    "cmptabcontroller":"",
    "winbuttons":"",
    "onsubmitsuccess":"",
    "submitparams":""
  },
  "permissions":{
    "apiaccess":"",
    "view":"",
    "list":"",
    "save":"",
    "create":"",
    "remove":"",
    "delete":"",
    "publish":"",
    "unpublish":"",
    "viewdeleted":"",
    "viewunpublished":""
  },
  "fieldpermissions":"",
  "columns":[
    {
      "MIGX_id":2,
      "header":"id",
      "dataIndex":"id",
      "width":"",
      "sortable":true,
      "show_in_grid":"0",
      "customrenderer":"",
      "renderer":"",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"",
      "renderoptions":"",
      "editor":""
    },
    {
      "MIGX_id":3,
      "dataIndex":"state",
      "header":"state"
    },
    {
      "MIGX_id":4,
      "dataIndex":"add_to_usergroups",
      "header":"add_to_usergroups"
    },
    {
      "MIGX_id":6,
      "dataIndex":"can_be_added_to_entry",
      "header":"can_be_added_to_entry"
    },
    {
      "MIGX_id":7,
      "dataIndex":"can_be_invited",
      "header":"can_be_invited"
    },
    {
      "MIGX_id":9,
      "dataIndex":"can_self_register",
      "header":"can_self_register"
    }
  ],
  "createdby":1,
  "createdon":"2023-04-09 19:03:40",
  "editedby":1,
  "editedon":"2024-01-20 18:19:46",
  "deleted":0,
  "deletedon":null,
  "deletedby":0,
  "published":1,
  "publishedon":null,
  "publishedby":0,
  "category":""
}