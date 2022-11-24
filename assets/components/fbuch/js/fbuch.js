var active_grid = 'fbuch';
var stickcss = null;
var cssobject = {};
var sticked_id = 0;

function isTouchDevice() {
    return 'ontouchstart' in document.documentElement;
}

var fbuch_getfahrten = function(dir,active_id,data){
    var offset = $('.fahrten').data('offset');
    var type = $('.fahrten').data('type');
    var formdata = data || [];
    var week_tab = $('.weektabs').find('.active').data('tab');
    formdata.push({'name':'week_tab','value':week_tab});
    formdata.push({'name':'offset','value':offset});
    formdata.push({'name':'type','value':type});
    formdata.push({'name':'active_id','value':active_id});
    formdata.push({'name':'dir','value':dir});
    $( "#fahrten" ).load( getfahrten_ajax_url,formdata, function(){
        fbuch_init_rowclick();
        fbuch_getteam();
    } );            
}

var fbuch_getteam = function(id){
    if(typeof getfahrtnamen_ajax_url == 'undefined'){
        return;
    }
    var fahrt_id = id || $('.fahrten').find('.active').data('id');
    $( "#team" ).load( getfahrtnamen_ajax_url,{"fahrt_id":fahrt_id}, function(){
        fbuch_init_removenameclick();
    } );         
}

var fbuch_handleDragDrop = function(source,target,target_id,fahrtNames_id,dateNames_id){
  var url = updateteam_ajax_url;
  var formdata = {"source":source,"target":target,"target_id":target_id,"fahrtNames_id":fahrtNames_id,"dateNames_id":dateNames_id,"processaction":"dragdrop"};       
  var posting = $.post( url, formdata );
  posting.done(function( data ) {
    fbuch_getfahrten('none',0,[{'name':'type','value':'dragdrop'}]);
  });         
            
}

var fbuch_init_removenameclick = function(){
    $('.remove_name').click(function(){
        var id = $(this).data('id');
        var process = $(this).data('process') || 'remove_datename';
        var url = removedatepersons_ajax_url;
        $( "#fbuchModal" ).load( url,{"object_id":id,"process":process}, function(){
            $("#fbuchModal").modal();
        });      
    }); 

    $('.invite_persons').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var data = {"fahrt_id":id,"process":"invite"};
        var ajax_url = invite_ajax_url;
        $( "#fbuchModal" ).load( ajax_url,data, function(){
            $("#fbuchModal").modal();
            active_grid = '';
            $('#add_person').focus();
        });
    });     
    
    $('.mail_invite').click(function(){
        var id = $(this).data('id');
        var process = $(this).data('process');
        var url = mailinvite_ajax_url;
        $( "#fbuchModal" ).load( url,{"object_id":id,"process":process}, function(){
            $("#fbuchModal").modal();
        });      
    });  
    $('.mail_invites').click(function(){
        var id = $(this).data('id');
        var process = $(this).data('process');
        var url = mailinvite_ajax_url;
        $( "#fbuchModal" ).load( url,{"object_id":id,"process":process}, function(){
            $("#fbuchModal").modal();
        });      
    }); 
    $('.add_commentxx').click(function(){
        var id = $(this).data('id');
        var process = $(this).data('process');
        var url = mailinvite_ajax_url;
        $( "#fbuchModal" ).load( url,{"object_id":id,"process":process}, function(){
            $("#fbuchModal").modal();
        });      
    });                         
}

var fbuch_init_rowclick = function(){
    var fahrt_id = $('.fahrten').find('.active').data('id');
    $('#datarow-buttons-' + fahrt_id).show();
    
    fbuch_init_removenameclick();
    
    $('.datarow').click(function(){
        $('.datarow-buttons').hide();
        active_grid = 'fbuch';
        var current = $('.fahrten').find('.active');
        var id = $(this).data('id');
        $('#datarow-buttons-' + id).show();
        current.removeClass('active');
        $(this).addClass('active');
        fbuch_getteam();
    });
    $('.create_fahrt').click(function(){
        active_grid = '';
        var type = $(this).data('type');
        var ajax_url = $(this).data('ajax_url')||updatefahrt_ajax_url;        
        $( "#fbuchModal" ).load( ajax_url,{"type":type,"object_id":0}, function(){
            $("#fbuchModal").modal();
        });                    
    });

    $('.update_fahrt').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');
        var ajax_url = $(this).data('ajax_url')||updatefahrt_ajax_url;
        active_grid = '';
        var type = $(this).data('type');
        $( "#fbuchModal" ).load( ajax_url,{"type":type,"object_id":id,"process":""}, function(){
            $("#fbuchModal").modal();
        });
    });
    $('.delete_fahrt').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');
        var type = $(this).data('type');
        $( "#fbuchModal" ).load( updatefahrt_ajax_url,{"type":type,"object_id":id,"process":"delete"}, function(){
            $("#fbuchModal").modal();
        });
    });    
    
    
    $('.close_fahrt').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var type = $(this).data('type');
        var data = {"type":type,"object_id":id,"process":"close"};
        $( "#fbuchModal" ).load( updatefahrt_ajax_url,data, function(){
            $("#fbuchModal").modal();
        });
    });

    $('.close_datefahrt').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');
        active_grid = '';
        var type = $(this).data('type');
        var data = {"grid_id":"Ergofahrten","type":type,"object_id":id,"process":"closedatefahrt"};
        $( "#fbuchModal" ).load( updatefahrt_ajax_url,data, function(){
            $("#fbuchModal").modal();
        });
    });
    
    $('.set_obmann').click(function(){
        var id = $(this).data('id');        
        var url = updateteam_ajax_url;
        var formdata = {"return":"message","fahrtnames_id":id,"processaction":"setobmann"};       
        var posting = $.post( url, formdata );
        posting.done(function( data ) {
            fbuch_getfahrten('none',0,[{'name':'type','value':'dragdrop'}]);
        });         
    });
    
    $('.set_cox').click(function(){
        var id = $(this).data('id');        
        var url = updateteam_ajax_url;
        var formdata = {"return":"message","fahrtnames_id":id,"processaction":"setcox"};       
        var posting = $.post( url, formdata );
        posting.done(function( data ) {
            fbuch_getfahrten('none',0,[{'name':'type','value':'dragdrop'}]);
        });         
    });      
    
    $('.lock_fahrt').click(function(){
        var id = $(this).data('id');        
        var url = updateteam_ajax_url;
        var lockaction = $(this).hasClass('locked') ? 'unlock' : 'lock';
        var formdata = {"return":"message","fahrt_id":id,"processaction":"lockfahrt","lockaction":lockaction};       
        var posting = $.post( url, formdata );
        posting.done(function( data ) {
            fbuch_getfahrten('none',0,[{'name':'type','value':'dragdrop'}]);
        });         
    });             
    
    $('.update_team').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var data = {"fahrt_id":id,"process":""};
        var ajax_url = $(this).data('ajax_url')||updateteam_ajax_url;
        $( "#fbuchModal" ).load( ajax_url,data, function(){
            $("#fbuchModal").modal();
            active_grid = '';
            $('#add_person').focus();
        });
    });
    $('.invite_persons').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var data = {"fahrt_id":id,"process":"invite"};
        var ajax_url = invite_ajax_url;
        $( "#fbuchModal" ).load( ajax_url,data, function(){
            $("#fbuchModal").modal();
            active_grid = '';
            $('#add_person').focus();
        });
    }); 
    $('.import_invites').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var data = {"date_id":id};
        var ajax_url = 'ajax/importinvites.html';
        $( "#fbuchModal" ).load( ajax_url,data, function(){
            $("#fbuchModal").modal();
            active_grid = '';
            //$('#add_person').focus();
        });
    });
    
    $('.import_members').click(function(){
        var current = $('.fahrten').find('.active');
        if (current.length == 0){
            current = $(this);
        }
        var id = current.data('id');        
        active_grid = '';
        var data = {"date_id":id};
        var ajax_url = 'ajax/importmembers.html';
        $( "#fbuchModal" ).load( ajax_url,data, function(){
            $("#fbuchModal").modal();
            active_grid = '';
            //$('#add_person').focus();
        });
    });     
    
    $('#prev_items').click(function(event){
        event.preventDefault();
        fbuch_getfahrten('up');    
    });
    $('#next_items').click(function(event){
        event.preventDefault();
        fbuch_getfahrten('down');    
    });
    $('#current_items').click(function(event){
        event.preventDefault();
        fbuch_getfahrten();    
    }); 
    $('.stick_to_top').click(function(event){
        
// remove <style> tag
if (stickcss){
    //stickcss.cssdom.parentNode.removeChild(el);
    $(stickcss.cssdom).remove();    
}

// GC result
stickcss = null;        

        if (sticked_id == $(this).data('id')){
            sticked_id = 0; 
        }else{
        sticked_id = $(this).data('id'); 
        var date_id = '#date-' + $(this).data('id');
        var width = $(date_id).css('width');
          
        cssobject = {};
        cssobject[date_id] = {
                $id: 'date',
                position: 'fixed',
                top: '10px',
                'z-index': '10000',
                width: width,
                'max-height':'60%',
                overflow:'auto'
            }
        stickcss = cssobj(cssobject);              
        }
      
    });     
    
    
    
// Drag Drop 

var dd_lists = $('.dates-drag-drop');
var dragHandle = isTouchDevice() ? ".drag-handle" : null;

for (var i=0;i<dd_lists.length;i++){
    Sortable.create(dd_lists[i], { 
        filter: isTouchDevice() ? '' : '.hasfahrt',
        handle: dragHandle,
        group: {
            name: "dd_group",
            revertClone: true,
            },
    onAdd: function (/**Event*/evt) {
        if ($(evt.from).hasClass('dates-drag-drop')){
            var source = 'dates';
        }
        if ($(evt.from).hasClass('fahrten-drag-drop')){
            var source = 'fahrten';
        }        
        
        var target = 'dates';
        var target_id = $(evt.to).data('id');
        var dateNames_id = $(evt.item).data('datenames_id');
        var fahrtNames_id = $(evt.item).data('fahrtnames_id');    
        
        fbuch_handleDragDrop(source,target,target_id,fahrtNames_id,dateNames_id);
    }                
    });
}

dd_lists = $('.fahrten-drag-drop');
for (var i=0;i<dd_lists.length;i++){
    Sortable.create(dd_lists[i], { 
        group: "dd_group",
        handle: dragHandle,
    onAdd: function (/**Event*/evt) {
        if ($(evt.from).hasClass('dates-drag-drop')){
            var source = 'dates';
        }
        if ($(evt.from).hasClass('fahrten-drag-drop')){
            var source = 'fahrten';
        }        
        
        var target = 'fahrten';
        var target_id = $(evt.to).data('id');
        var dateNames_id = $(evt.item).data('datenames_id');
        var fahrtNames_id = $(evt.item).data('fahrtnames_id');    
        
        fbuch_handleDragDrop(source,target,target_id,fahrtNames_id,dateNames_id);
    }        
    });
} 

fbuch_check_date_id();
    
}

$(window).keydown(function(event){
    if (event.which == 38){
        //up

        if (active_grid == 'fbuch'){
            event.preventDefault();
            var current = $('.fahrten').find('.active');
            if (!current.hasClass('first')){
                var prev = current.prev();
                if (prev.hasClass('datarow-buttons')){
                    $('.datarow-buttons').hide();
                    prev = prev.prev();
                    var id = prev.data('id');
                    $('#datarow-buttons-' + id).show();                    
                }
                current.removeClass('active');
                prev.addClass('active');  
                fbuch_getteam();            
            }else{
                fbuch_getfahrten('up');
            }            
        }

    }
    if (event.which == 40){
        //down
        if (active_grid == 'fbuch'){
            event.preventDefault();
            var current = $('.fahrten').find('.active');
            if (!current.hasClass('last')){
                var next = current.next();
                if (next.hasClass('datarow-buttons')){
                    $('.datarow-buttons').hide();
                    next = next.next();
                    var id = next.data('id');
                    $('#datarow-buttons-' + id).show();                       
                }                
                current.removeClass('active');
                next.addClass('active');
                fbuch_getteam();  
            }else{
                fbuch_getfahrten('down');
            }
        }
    } 
    
})

var fbuch_check_date_id = function(){
    let searchParams = new URLSearchParams(window.location.search);
    let date_id = '';
    if (searchParams.has('date_id')){
        date_id = searchParams.get('date_id');   
        fbuch_getteam(date_id); 
        $('#fahrten').hide();
        $('#member-message').hide();
    } 
    
    //console.log(event_id);
}


fbuch_init_rowclick();
fbuch_getteam();
fbuch_check_date_id();

$('#fbuchModal').on('hide.bs.modal', function (e) {
  if(e.target.id == 'fbuchModal'){
     active_grid = 'fbuch';
  }
  
})

onChangeAuswertung = function(){
    var group = $('#group').val();
    var year = $('#year').val();
    var boot_type = $('#boot_type').val();
    var zeitraum = $('#zeitraum').val();
    $( "#auswertung" ).load( selfurl + ' #auswertung>*',{"zeitraum":zeitraum,"boot_type":boot_type,"group":group,'year':year}, function(html){
        var select = $(html).find("#zeitraum");
        console.log(select.html());
        $("#zeitraum").html(select.html());
    } );    
}

$('#group').change(function(){
    onChangeAuswertung();    
});

$('#year').change(function(){
    onChangeAuswertung();   
});

$('#boot_type').change(function(){
    onChangeAuswertung();   
});
$('#zeitraum').change(function(){
    onChangeAuswertung();   
});




