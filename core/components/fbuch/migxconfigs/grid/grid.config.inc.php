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


$gridactionbuttons['fb_sendmails']['text'] = "'Mailversand'";
$gridactionbuttons['fb_sendmails']['handler'] = 'this.fb_sendmails';
$gridactionbuttons['fb_sendmails']['scope'] = 'this';
$gridactionbuttons['fb_sendmails']['standalone'] = '1';

$gridactionbuttons['customchunks_tojson']['text'] = "'Custom Chunks to Json'";
$gridactionbuttons['customchunks_tojson']['handler'] = 'this.customchunks_tojson';
$gridactionbuttons['customchunks_tojson']['scope'] = 'this';
$gridactionbuttons['customchunks_tojson']['standalone'] = '1';

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



$gridfunctions['this.fb_sendmails'] = "
fb_sendmails: function(btn,e) {
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


//ab hier aus mv importiert

$gridcontextmenus['sendmails']['code']="
        m.push({
            className : 'sendmails', 
            text: 'Serienmail senden',
            handler: 'this.sendmails'
        });
        m.push('-');
";
$gridcontextmenus['sendmails']['handler'] = 'this.sendmails';

$gridfunctions['this.sendmails'] = "
sendmails: function(btn,e) {
      params = {
          sendmails: '1'
      }          
      this.loadWin(btn,e,'d',Ext.util.JSON.encode(params));
    }
";

$gridcontextmenus['sendsinglemail']['code']="
        m.push({
            className : 'sendsinglemail', 
            text: 'Einzelmail senden',
            handler: 'this.sendsinglemail'
        });
        m.push('-');
";
$gridcontextmenus['sendsinglemail']['handler'] = 'this.sendsinglemail';

$gridfunctions['this.sendsinglemail'] = "
sendsinglemail: function(btn,e) {
		var s = this.getStore();
		var code, type, category, study_type, ebs_state;
		var box = Ext.MessageBox.wait('Preparing ...', 'Mail wird erstellt');
        var params = s.baseParams;
        var o_action = params.action || '';
        var o_processaction = params.processaction || '';
        var configs = this.config.configs;
        
        params.action = 'mgr/migxdb/process';
        params.processaction = 'sendmails';
        params.singlemail = '1';
        params.configs = this.config.configs;
        params.resource_id = '[[+config.resource_id]]'; 
        params.member_id = this.menu.record.id;    

		MODx.Ajax.request({
			url : this.config.url,
			params: params,
			listeners: {
				'success': {fn:function(r) {
					 box.hide();
				},scope:this}
				,'failure': {fn:function(r) {
					 box.hide();
				},scope:this}                
			}
		});
        
        params.action = o_action;
        params.processaction = o_processaction;
        
		return true;
    }
";


$gridactionbuttons['do_sendmails']['text'] = "'Serienmails senden'";
$gridactionbuttons['do_sendmails']['handler'] = 'this.doSendmails';
$gridactionbuttons['do_sendmails']['scope'] = 'this';
$gridactionbuttons['do_sendmails']['enableToggle'] = 'true';

$gridfunctions['this.doSendmails'] = "
	doSendmails: function(btn,e) {
		var s = this.getStore();
		var code, type, category, study_type, ebs_state;
		var box = Ext.MessageBox.wait('Preparing ...', 'Serienmails werden erstellt');
        var params = s.baseParams;
        var o_action = params.action || '';
        var o_processaction = params.processaction || '';
        var configs = this.config.configs;
        
        params.action = 'mgr/migxdb/process';
        params.processaction = 'sendmails';
        params.configs = this.config.configs;
        params.resource_id = '[[+config.resource_id]]';     

		MODx.Ajax.request({
			url : this.config.url,
			params: params,
			listeners: {
				'success': {fn:function(r) {
					 box.hide();
				},scope:this}
				,'failure': {fn:function(r) {
					 box.hide();
				},scope:this}                
			}
		});
        
        params.action = o_action;
        params.processaction = o_processaction;
        
		return true;
	}
";


$gridfunctions['this.createUser'] = "
createUser: function(btn,e) {
		var s = this.getStore();
		//var box = Ext.MessageBox.wait('Preparing ...', 'Mail wird erstellt');
        var params = s.baseParams;
        var o_action = params.action || '';
        var o_processaction = params.processaction || '';
        var configs = this.config.configs;
        
        params.action = 'mgr/migxdb/process';
        params.processaction = 'createuser';
        params.configs = this.config.configs;
        params.resource_id = '[[+config.resource_id]]'; 
        params.object_id = this.menu.record.id;
        
		MODx.Ajax.request({
			url : this.config.url,
			params: params,
			listeners: {
				'success': {fn:function(r) {
					 //box.hide();
                     this.refresh();
				},scope:this}
				,'failure': {fn:function(r) {
					 //box.hide();
                     this.refresh();
				},scope:this}                
			}
		});
        
        params.action = o_action;
        params.processaction = o_processaction;
        
		return true;
    }
";

$gridfunctions['this.customchunks_tojson'] = "
customchunks_tojson: function(btn,e) {
		var s = this.getStore();
		//var box = Ext.MessageBox.wait('Preparing ...', 'Mail wird erstellt');
        var params = s.baseParams;
        var o_action = params.action || '';
        var o_processaction = params.processaction || '';
        var configs = this.config.configs;
        
        params.action = 'mgr/migxdb/process';
        params.processaction = 'customChunksToJson';
        params.configs = this.config.configs;
        params.resource_id = '[[+config.resource_id]]'; 
                
		MODx.Ajax.request({
			url : this.config.url,
			params: params,
			listeners: {
				'success': {fn:function(r) {
					 //box.hide();
                     this.refresh();
				},scope:this}
				,'failure': {fn:function(r) {
					 //box.hide();
                     this.refresh();
				},scope:this}                
			}
		});
        
        params.action = o_action;
        params.processaction = o_processaction;
        
		return true;
    }
";