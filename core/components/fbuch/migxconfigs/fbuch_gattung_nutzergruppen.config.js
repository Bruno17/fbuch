{
  "id":41,
  "name":"fbuch_gattung_nutzergruppen",
  "formtabs":[
    {
      "MIGX_id":67,
      "caption":"Allgemein",
      "print_before_tabs":"0",
      "fields":[
        {
          "MIGX_id":273,
          "field":"gattung_id",
          "caption":"Riggerung",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"listbox",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"@CHUNK input_options_gattung_id",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":1
        },
        {
          "MIGX_id":274,
          "field":"nutzergruppe",
          "caption":"Nutzergruppe",
          "description":"",
          "description_is_code":"0",
          "inputTV":"",
          "inputTVtype":"listbox",
          "validation":"",
          "configs":"",
          "restrictive_condition":"",
          "display":"",
          "sourceFrom":"config",
          "sources":"",
          "inputOptionValues":"@CHUNK input_options_bootsnutzergruppen",
          "default":"",
          "useDefaultIfEmpty":"0",
          "pos":2
        }
      ],
      "pos":1
    }
  ],
  "contextmenus":"",
  "actionbuttons":"",
  "columnbuttons":"",
  "filters":"",
  "extended":{
    "migx_add":"",
    "disable_add_item":"",
    "add_items_directly":"",
    "formcaption":"",
    "update_win_title":"",
    "win_id":"",
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
    "classname":"",
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
    "getlistselectfields":"",
    "getlistspecialfields":"",
    "getlistwhere":"",
    "getlistgroupby":"",
    "joins":"",
    "hooksnippets":"",
    "cmpmaincaption":"",
    "cmptabcaption":"",
    "cmptabdescription":"",
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
      "header":"Gattung",
      "dataIndex":"gattung",
      "width":"",
      "sortable":"false",
      "show_in_grid":1,
      "customrenderer":"",
      "renderer":"this.renderChunk",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"[[!migxLoopCollection? &packageName=`fbuch` &classname=`fbuchBootsGattung` &where=`{\"id\":\"[[+gattung_id]]\"}`&tpl=`@CODE:{{+shortname}}`]]",
      "renderoptions":"",
      "editor":""
    },
    {
      "MIGX_id":1,
      "header":"Nutzergruppe",
      "dataIndex":"nutzergroup",
      "width":"",
      "sortable":"false",
      "show_in_grid":1,
      "customrenderer":"",
      "renderer":"this.renderChunk",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"[[migxLoopCollection? &classname=`fbuchBootsNutzergruppe` &where=`{\"id\":\"[[+nutzergruppe]]\"}`&tpl=`@CODE:{{+name}}`]]",
      "renderoptions":"",
      "editor":""
    }
  ],
  "createdby":1,
  "createdon":"2024-11-27 06:58:12",
  "editedby":1,
  "editedon":"2024-11-27 08:12:08",
  "deleted":0,
  "deletedon":null,
  "deletedby":0,
  "published":1,
  "publishedon":null,
  "publishedby":0,
  "category":"fbuch"
}