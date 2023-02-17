import api_select from '../api_select.js'
import timeinput from '../timeinput.js'
import datepicker from '../datepicker.js'
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

import { useRecurrenciesStore } from '../../stores/eventform.js';


export default {

    props : {
        event:{}
    },
    components : {
      api_select : api_select,
      datepicker : datepicker,
      timeinput : timeinput,
    },

    setup(props) {

      const recurrencies_store = useRecurrenciesStore(); 
      const {onMounted, ref, watch } = Vue;      
      const event = props.event;
      const params = Vue.$router.currentRoute._value.params;
      const id = event.id || 'new';
      const date = params.year + '-' + params.month + '-' +params.day;
      const state = ref({});
      const save_dialog = ref(false);
      const save_which = ref('');
      const submitclicked = ref(false);
      const eventform = ref(null);
      const eventtype = ref(null);
      const urls = ref(setUrls());
      const tab = ref('date');
      const protect_fields=ref(
        [
            {field:'title',label:'Titel'},
            {field:'description',label:'Beschreibung'},
            {field:'start_time',label:'Uhrzeit Beginn'},
            {field:'end_time',label:'Uhrzeit Ende'},
            {field:'mailinglist_id',label:'Einladungsliste'},
            {field:'type',label:'Termin Art'},
            {field:'max_reservations',label:'Plätze max.'}
        ]
      );

      onMounted(() => {
        //console.log('eventform mounted');
        useLoadPermissions();
        state.value.days = state.value.days || 0;
        state.value.hours = state.value.hours || 0;
        state.value.minutes = state.value.minutes || 0;
        state.value.minutes_total = state.value.minutes_total || 0;
        state.value.duration_valid = state.value.duration_valid || true;
      })

      function setUrls(){
        return {
            day : '/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD'),
            kalender : '/' + Quasar.date.formatDate(event.date, 'YYYY/MM'),
            fahrtenbuch : '/?offset=' + Quasar.date.formatDate(event.date, 'YYYY-MM-DD') + '&type=dragdrop&dir=none'
          }
      }

      watch(() => event.date, (value) => {
          urls.value = setUrls();
      })

    function onSubmitClick(){
      //console.log('submitclick',id);
      submitclicked.value=true;
    }

    function save(){
        //console.log('submit');
        if (id == 'new'){
          const ajaxUrl = modx_options.rest_url + 'Dates';
          axios.post(ajaxUrl,event)
          .then(function (response) {
              //event.value = response.data.object;
              Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD')); 
          })
          .catch(function (error) {
              console.log(error);
          }); 
        } else {
          const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
          //console.log(save_which.value);
          if (save_which.value == 'recurrencies') {
            event.recurrencies = recurrencies_store.recurrenceSelections;
          }
          axios.put(ajaxUrl,event)
          .then(function (response) {
              save_dialog.value=false;
              //event.value = response.data.object;
              //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
          })
          .catch(function (error) {
              console.log(error);
          }); 
        }
    }

    function loadRecurrencies(){
        let props = {parent : event.id}
        if (event.parent > 0){
            props = {parent : event.parent}
        }
        recurrencies_store.loadEvents(props);    
    }
      

    function onSubmit(){
        //console.log('submit',id);
        save_which.value='this_only';
        if (id=='new'){
          save();
          return;
        }

        loadRecurrencies();
        save_dialog.value = true;
    }

    function onReset(){
      console.log('reset');
      //Vue.$router.go(-1);
    }

      return { 
        event,
        tab,
        eventform,
        eventtype,
        submitclicked, 
        onSubmit, 
        state, 
        onReset, 
        onSubmitClick,
        urls,
        save_dialog,
        useHasPermission,
        save,
        save_which,
        recurrencies_store,
        protect_fields
    }
    },
    template: '#eventform-component'
    // or `template: '#my-template-element'`
  }