{
  "id":6,
  "name":"mv_mailfilters",
  "formtabs":[
    {
      "MIGX_id":12,
      "caption":"Filter",
      "print_before_tabs":"0",
      "fields":[
        {
          "MIGX_id":64,
          "field":"name",
          "caption":"Name des Filters",
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
          "MIGX_id":65,
          "field":"where",
          "caption":"where-statement",
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
    "migx_add":"Filter erstellen",
    "disable_add_item":"",
    "add_items_directly":"",
    "formcaption":"",
    "update_win_title":"",
    "win_id":"mv_memberfilters",
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
    "classname":"mvMemberFilter",
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
    "cmptabcaption":"Mail Filter",
    "cmptabdescription":"Mailfilter verwalten",
    "cmptabcontroller":"",
    "winbuttons":"",
    "onsubmitsuccess":"",
    "submitparams":""
  },
  "columns":[
    {
      "MIGX_id":1,
      "header":"ID",
      "dataIndex":"id",
      "width":10,
      "sortable":"false",
      "show_in_grid":1,
      "renderer":"",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"",
      "renderoptions":"",
      "editor":""
    },
    {
      "MIGX_id":2,
      "header":"Name",
      "dataIndex":"name",
      "width":40,
      "sortable":"false",
      "show_in_grid":1,
      "renderer":"this.renderRowActions",
      "clickaction":"",
      "selectorconfig":"",
      "renderchunktpl":"",
      "renderoptions":"",
      "editor":""
    }
  ],
  "createdby":1,
  "createdon":"2015-02-26 15:30:58",
  "editedby":1,
  "editedon":"2020-05-15 15:57:16",
  "deleted":0,
  "deletedon":"-1-11-30 00:00:00",
  "deletedby":0,
  "published":1,
  "publishedon":"-1-11-30 00:00:00",
  "publishedby":0,
  "category":"mv"
}