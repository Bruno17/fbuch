import api_select from './api_select.js'
import timeinput from './timeinput.js'
import datepicker from './datepicker.js'
import { useLoadPermissions,useHasPermission } from "../composables/helpers.js";

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
    
      const {onMounted, ref } = Vue;      
      const event = props.event;
      const params = Vue.$router.currentRoute._value.params;
      const id = event.id || 'new';
      const date = params.year + '-' + params.month + '-' +params.day;
      const state = ref({});
      const save_dialog = ref(false);
      const submitclicked = ref(false);
      const eventform = ref(null);
      const eventtype = ref(null);
      const checkPermissions = 'fbuch_edit_termin,fbuch_create_termin,fbuch_delete_termin';
      const urls = {
        day : '/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD'),
        kalender : '/' + Quasar.date.formatDate(event.date, 'YYYY/MM')
      }

      onMounted(() => {
        //console.log('eventform mounted');
        useLoadPermissions(checkPermissions);
        state.value.days = state.value.days || 0;
        state.value.hours = state.value.hours || 0;
        state.value.minutes = state.value.minutes || 0;
        state.value.minutes_total = state.value.minutes_total || 0;
        state.value.duration_valid = state.value.duration_valid || true;
      })
      
    function onSubmitClick(){
      console.log('submitclick');
    }

    function save(){
        //console.log('submit');
        if (id == 'new'){
          const ajaxUrl = modx_options.rest_url + 'Dates';
          axios.post(ajaxUrl,event)
          .then(function (response) {
              //event.value = response.data.object;
              Vue.$router.push(urls.day); 
          })
          .catch(function (error) {
              console.log(error);
          }); 
        } else {
          const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
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

    function onSubmit(){
        console.log('submit');
        save_dialog.value = true;
    }

    function onReset(){
      console.log('reset');
      //Vue.$router.go(-1);
    }

      return { 
        event,
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
        save
    }
    },
    template: `

      <q-form
        @submit="onSubmit"
        @reset="onReset"
        ref="eventform"
      >

      <div class="q-col-gutter-md q-gutter-md row">
      <div class="col-md-4 col-sm-12 q-col-gutter-md content-start row">
      <datepicker
      label="Startdatum" 
      class="col-md-12 col-sm-6 col-xs-12" 
      v-model="event.date"
      :event="event"
      :state="state"
      startfield="date"
      endfield="date_end"
      timestartfield="start_time"
      timeendfield="end_time"        
      which="start"
      > 
      </datepicker>

      <timeinput
      label="Uhrzeit Beginn"
      class="col-md-12 col-sm-6 col-xs-12" 
      v-model="event.start_time"
      :event="event"
      :state="state"
      startfield="date"
      endfield="date_end"
      timestartfield="start_time"
      timeendfield="end_time" 
      which="start"        
      >
      </timeinput>

      <datepicker
      label="Datum bis" 
      class="col-md-12 col-sm-6 col-xs-12" 
      v-model="event.date_end"
      :event="event"
      :state="state"
      startfield="date"
      endfield="date_end"
      timestartfield="start_time"
      timeendfield="end_time" 
      which="end"
      > 
      </datepicker>

      <timeinput
      label="Uhrzeit Ende"
      class="col-md-12 col-sm-6 col-xs-12" 
      v-model="event.end_time"
      :event="event"
      :state="state"
      startfield="date"
      endfield="date_end"
      timestartfield="start_time"
      timeendfield="end_time" 
      which="end" 
      >
      </timeinput>        

      <q-input
      class="col-4"
      outlined
      readonly
      v-model="state.days"
      label="Tage"
      mask="###"
    />  
    
    <q-input
    class="col-4"
    outlined
    readonly
    v-model="state.hours"
    label="Stunden"
    mask="######"
    />  

      <q-input
      class="col-4"
      outlined
      readonly
      v-model="state.minutes"
      label="Minuten"
      /> 
      </div>
      

      <div class="col-md-8 col-sm-12 q-col-gutter-md content-start row">
    
      <api_select
      class="col-md-4 col-sm-4 col-xs-12"
      v-model="event.instructor_member_id"
      label="Betreuer"
      controller="Names?limit=100000&returntype=options" 
    ></api_select>

    <q-input
      class="col-md-8 col-sm-4 col-xs-12"
      outlined
      v-model="event.title"
      label="Titel/Gruppe/Zweck"
      :rules="[val => (!submitclicked || !!val) || 'Bitte einen Text eintragen!']"
    />

    <api_select
      class="col-md-4 col-sm-4 col-xs-12"
      ref="eventtype"
      v-model="event.type"
      label="Termin Art"
      controller="Datetypes?limit=100000&returntype=options"
      :rules="[val => (!submitclicked || !!val) || 'Bitte eine Termin Art wählen!']"
    ></api_select> 
    
    <api_select
      class="col-md-6 col-sm-8 col-xs-12"
      v-model="event.mailinglist_id"
      label="Einladungs Liste"
      controller="Mailinglists?limit=100000&returntype=options"
    ></api_select> 

    <q-input
    class="col-md-2 col-sm-4 col-xs-12"
    outlined
    v-model="event.max_reservations"
    label="Plätze max."
    mask="###"
    />     

    <q-input
    class="col-12"
    outlined
    v-model="event.description"
    label="Beschreibung"
    type="textarea"
    /> 

      </div>
      <div class="col-12">
      <q-btn label="Speichern" type="submit" @click="onSubmitClick" color="primary"/>
      <q-btn label="Zur Tagesansicht" :to="urls.day" color="primary" flat class="q-ml-sm" />
      <q-btn label="Zur Kalenderansicht" :to="urls.kalender" color="primary" flat class="q-ml-sm" />
      </div>      


      </div>  
      </q-form>

      <q-dialog v-model="save_dialog">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Alles speichern</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>

          <div class="text-h6">Zu ändernde Wiederholungen auswählen</div>
 
        </q-card-section>
        <q-card-actions vertical align="right"> 
        <q-btn color="primary" v-if="useHasPermission('fbuch_edit_termin')" @click="save" >
        Speichern
        </q-btn>  
        </q-card-actions>         
      </q-card>
    </q-dialog>      

    `
    // or `template: '#my-template-element'`
  }