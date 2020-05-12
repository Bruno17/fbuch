<?php

$gridactionbuttons['pullmvids']['text'] = "'Ausgewählte, MV ID holen'";
$gridactionbuttons['pullmvids']['handler'] = 'this.pullmvids,this.pull';
$gridactionbuttons['pullmvids']['scope'] = 'this';
//$gridactionbuttons['pullmvids']['standalone'] = '1';

$gridactionbuttons['pullstatus']['text'] = "'Ausgewählte, MV Status holen'";
$gridactionbuttons['pullstatus']['handler'] = 'this.pullstatus,this.pull';
$gridactionbuttons['pullstatus']['scope'] = 'this';
//$gridactionbuttons['pullstatus']['standalone'] = '1';

$gridactionbuttons['pullmemail']['text'] = "'Ausgewählte, MV email holen'";
$gridactionbuttons['pullmemail']['handler'] = 'this.pullemail,this.pull';
$gridactionbuttons['pullmemail']['scope'] = 'this';
//$gridactionbuttons['pullmemail']['standalone'] = '1';

$gridactionbuttons['pulluserid']['text'] = "'Ausgewählte, User ID holen'";
$gridactionbuttons['pulluserid']['handler'] = 'this.pulluserid,this.pull';
$gridactionbuttons['pulluserid']['scope'] = 'this';
//$gridactionbuttons['pulluserid']['standalone'] = '1';

$gridactionbuttons['uploadfiles']['text'] = "'1. Dateien hochladen'";
$gridactionbuttons['uploadfiles']['handler'] = 'this.uploadFiles';
$gridactionbuttons['uploadfiles']['scope'] = 'this';
$gridactionbuttons['uploadfiles']['standalone'] = '1';

$gridactionbuttons['emptytable']['text'] = "'2. Benutzertabelle leeren'";
$gridactionbuttons['emptytable']['handler'] = 'this.emptytable';
$gridactionbuttons['emptytable']['scope'] = 'this';
$gridactionbuttons['emptytable']['standalone'] = '1';

$gridactionbuttons['importusers']['text'] = "'2. Importieren'";
$gridactionbuttons['importusers']['handler'] = 'this.importusers';
$gridactionbuttons['importusers']['scope'] = 'this';
$gridactionbuttons['importusers']['standalone'] = '1';

$gridactionbuttons['syncmembers']['text'] = "'Mit Mitgliederliste abgleichen'";
$gridactionbuttons['syncmembers']['handler'] = 'this.syncmembers';
$gridactionbuttons['syncmembers']['scope'] = 'this';
$gridactionbuttons['syncmembers']['standalone'] = '1';


$gridactionbuttons['sendmails']['text'] = "'Mailversand'";
$gridactionbuttons['sendmails']['handler'] = 'this.sendmails';
$gridactionbuttons['sendmails']['scope'] = 'this';
$gridactionbuttons['sendmails']['standalone'] = '1';

$winbuttons['send']['text'] = "'Rundmail senden'";
$winbuttons['send']['handler'] = 'this.submit';
$winbuttons['send']['scope'] = 'this';

$gridfunctions['this.pulluserid'] = "
pulluserid: function(btn,e) {
    this.pull(btn,e,'pulluserid');
    }
";

$gridfunctions['this.pullemail'] = "
pullemail: function(btn,e) {
    this.pull(btn,e,'pullemail');
    }
";

$gridfunctions['this.pullmvids'] = "
pullmvids: function(btn,e) {
    this.pull(btn,e,'pullmvids');
    }
";

$gridfunctions['this.pullstatus'] = "
pullstatus: function(btn,e) {
    this.pull(btn,e,'pullstatus');
    }
";

$gridfunctions['this.pull'] = "
pull: function(btn,e,task) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;
        
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/migxdb/process'
                ,processaction: 'pullmvids'                     
				,configs: this.config.configs
                ,resource_id: this.config.resource_id
                ,co_id: '[[+config.connected_object_id]]'                
				,task: task
                ,objects: cs
                ,reqConfigs: '[[+config.req_configs]]'
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });
        return true;
    }
";



$gridfunctions['this.sendmails'] = "
sendmails: function(btn,e) {
		this.loadWin(btn,e,'a');
	}
";

$gridfunctions['this.uploadFiles'] = "
    uploadFiles: function(btn,e) {
        if (!this.uploader) {
            this.uploader = new MODx.util.MultiUploadDialog.Dialog({
                url: MODx.config.connector_url
                ,base_params: {
                    action: 'browser/file/upload'
                    ,wctx: MODx.ctx || ''
                    ,source: 1
                    ,path:'/csvimport/'
                }
                ,cls: 'ext-ux-uploaddialog-dialog modx-upload-window'
            });
            //this.uploader.on('show',this.beforeUpload,this);
            //this.uploader.on('uploadsuccess',this.uploadSuccess,this);
            //this.uploader.on('uploaderror',this.uploadError,this);
            //this.uploader.on('uploadfailed',this.uploadFailed,this);
        }
        this.uploader.base_params.source = 1;
        this.uploader.show(btn);
    } 	
";

$gridfunctions['this.syncmembers'] = "
syncmembers: function(btn,e) {
    var _this=this;
    Ext.Msg.confirm(_('warning') || '','Namen werden mit Mitgliederliste abgeglichen - Fortfahren?',function(e) {
        if (e == 'yes') {    
            MODx.Ajax.request({
                url: _this.config.url
                ,params: {
                    action: 'mgr/migxdb/process'
                    ,processaction: 'syncmembers'                     
                    ,configs: _this.config.configs
                    ,resource_id: _this.config.resource_id
                    ,co_id: '[[+config.connected_object_id]]'                
                    ,reqConfigs: '[[+config.req_configs]]'
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        _this.refresh();
                    },scope:_this}
                }
            });
        }
    }),this;           
    return true;
}
";

$gridfunctions['this.importusers'] = "
importusers: function(btn,e) {
    var _this=this;
    Ext.Msg.confirm(_('warning') || '','Benutzer werden der Tabelle hinzugefügt. Bitte Dateien vorher hochladen und Tabelle vorher leeren! - Fortfahren?',function(e) {
        if (e == 'yes') {    
            MODx.Ajax.request({
                url: _this.config.url
                ,params: {
                    action: 'mgr/migxdb/process'
                    ,processaction: 'import'                     
                    ,configs: _this.config.configs
                    ,resource_id: _this.config.resource_id
                    ,co_id: '[[+config.connected_object_id]]'                
                    ,reqConfigs: '[[+config.req_configs]]'
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        _this.refresh();
                    },scope:_this}
                }
            });
        }
    }),this;           
    return true;
}
";

$gridfunctions['this.emptytable'] = "
emptytable: function(btn,e) {
    var _this=this;
    Ext.Msg.confirm(_('warning') || '','Alle Benutzer aus der Tabelle entfernen? Benutzer mit den IDs 1-10 bleiben erhalten - Fortfahren?',function(e) {
        if (e == 'yes') {    
            MODx.Ajax.request({
                url: _this.config.url
                ,params: {
                    action: 'mgr/migxdb/process'
                    ,processaction: 'emptytable'                     
                    ,configs: _this.config.configs
                    ,resource_id: _this.config.resource_id
                    ,co_id: '[[+config.connected_object_id]]'                
                    ,reqConfigs: '[[+config.req_configs]]'
                }
                ,listeners: {
                    'success': {fn:function(r) {
                        _this.refresh();
                    },scope:_this}
                }
            });
        }
    }),this;           
    return true;
}
";