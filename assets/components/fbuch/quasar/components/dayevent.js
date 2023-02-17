import { useHasPermission } from "../composables/helpers.js";

export default {

    props:{
        view:'',
        event:{},
        loadDayEvents:Function
    },

    setup(props) {
    
      const {onMounted, ref } = Vue;
      const modx = modx_options;
      const { useQuasar } = Quasar;
      const $q = useQuasar(); 
            
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

      return {modx, confirmDelete,confirmHide,hideEvent,useHasPermission }
    },
    template: '#dayevent-component'
    // or `template: '#my-template-element'`
  }