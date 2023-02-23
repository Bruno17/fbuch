import { useHasPermission } from "../../composables/helpers.js";

export default {

    props:{
        view:'',
        entry:{},
        moveMembers:Function,
        loadAll:Function
    },

    setup(props) {
    
      const {onMounted, ref } = Vue;
      const modx = modx_options;
      const { useQuasar } = Quasar;
      const $q = useQuasar(); 

      function pullMembers(){
          const properties = {
              target : 'fahrten',
              target_id : props.entry.id
          };  
          props.moveMembers(properties);
      }

      function removeName(name){
          const id = name.id || false;
          const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
          if (id) {
            axios.delete(ajaxUrl)
            .then(function (response) {
              props.loadAll();      
            })
            .catch(function (error) {
                console.log(error);
            });             
          }
      }

      function confirmRemoveName(name){
        $q.dialog({
            title: 'Person entfernen',
            message: 'Soll ' + name.Member_firstname + ' ' + name.Member_name + ' wirklich aus dem Eintrag entfernt werden?',
            ok: {label:'Entfernen'},
            cancel: true,
            persistent: true
          }).onOk(() => {
             removeName(name);
          })
      }      
            
      function confirmDelete(){
        $q.dialog({
            title: 'Termin löschen',
            message: 'Soll der Termin wirklich gelöscht werden?',
            ok: {label:'Löschen'},
            cancel: true,
            persistent: true
          }).onOk(() => {
             deleteEvent();
          })
      }

      function confirmHide(){
        $q.dialog({
            title: 'Termin verbergen',
            message: 'Soll der Termin wirklich deaktiviert werden? Termin kann über die Wiederholungen wieder sichtbar gemacht werden.',
            ok: {label:'Verbergen'},
            cancel: true,
            persistent: true
          }).onOk(() => {
             hideEvent();
          })
      }      

      function deleteEvent(){
        const ajaxUrl = modx_options.rest_url + 'Dates/' + props.event.id;
        const event = {deleted:1};
        axios.put(ajaxUrl,event)
        .then(function (response) {
          props.loadDayEvents();      
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }

      function hideEvent(){
        const ajaxUrl = modx_options.rest_url + 'Dates/' + props.event.id;
        const event = {hidden:props.event.hidden==1?0:1};
        axios.put(ajaxUrl,event)
        .then(function (response) {
          props.loadDayEvents();      
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }      

      return {
        expanded: ref(false),
        modx, 
        confirmDelete,
        confirmRemoveName,
        confirmHide,
        hideEvent,
        useHasPermission,
        pullMembers
      }
    },
    template: '#entry-component'
    // or `template: '#my-template-element'`
  }