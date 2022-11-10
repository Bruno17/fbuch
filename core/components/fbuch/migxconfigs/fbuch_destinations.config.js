{
  "id":1,
  "name":"fbuch_destinations",
  "formtabs":[
    {
      "MIGX_id":1,
      "caption":"",
      "print_before_tabs":"0",
      "fields":[
        {
          "MIGX_id":1,
          "field":"destination",
          "caption":"Fahrtenziel",
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
          "MIGX_id":2,
          "field":"km",
          "caption":"km",
          "pos":2
        }
      ],
      "pos":1
    }
  ],
  "contextmenus":"",
  "actionbuttons":"addItem",
  "columnbuttons":"update||remove",
  "filters":"",
  "extended":{
    "migx_add":"Fahrtenziel erstellen",
    "disable_add_item":"",
    "add_items_directly":"",
    "formcaption":"",
    "update_win_title":"",
    "win_id":"fbuch_destinations",
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
    "classname":"fbuchDestination",
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
    "cmptabcaption":"Fahrtenziele",
    "cmptabdescription":"Verwaltung der Fahrtenziele",
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
      "width":10,
      "sortable":true,
      "show_in_grid":1,
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
      "header":"destination",
      "dataIndex":"destination",
      "width":40,
      "sortable":true,
      "show_in_grid":1,
      "customrenderer":"",
      "renderer":"this.renderRowActions",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"",
      "renderoptions":"",
      "editor":""
    },
    {
      "MIGX_id":4,
      "header":"km",
      "dataIndex":"km",
      "width":10,
      "sortable":true,
      "show_in_grid":1,
      "customrenderer":"",
      "renderer":"",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"",
      "renderoptions":"",
      "editor":""
    }
  ],
  "createdby":1,
  "createdon":"2022-04-25 13:47:32",
  "editedby":1,
  "editedon":"2022-04-25 13:52:26",
  "deleted":0,
  "deletedon":null,
  "deletedby":0,
  "published":1,
  "publishedon":null,
  "publishedby":0,
  "category":"fbuch"
}