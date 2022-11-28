import api_select from './api_select.js'
import timeinput from './timeinput.js'
import datepicker from './datepicker.js'
import { useLoadPermissions,useHasPermission } from "../composables/helpers.js";

import { useRecurrenciesStore } from '../stores/eventform.js';


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
    template: `

      <q-form
        @submit="onSubmit"
        @reset="onReset"
        ref="eventform"
      >

      <div class="col-12">
      <q-tabs
        v-if="event.parent>0"
        v-model="tab"
        align="left"
        no-caps
        outside-arrows
        mobile-arrows
        class=""
      >
        <q-tab name="date" label="Termin Daten" />
        <q-tab name="fields" label="Felder schützen" />
      </q-tabs>
      </div>
      <q-tab-panels
      v-model="tab"
      >
      <q-tab-panel name="date">  
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

      </q-tab-panel>  
      <q-tab-panel name="fields">
      Nachfolgend markierte Felder werden vor dem Überschreiben geschützt, wenn man beim speichern eines Termins 'auch in kommende Termine speichern' ausgewählt hat.
      <br>
      Ansonsten werden dann jeweils die Werte aller nachfolgend aufgeführter Felder in die ausgewählten Wiederholungen kopiert.
      <br>
      Dies ist sinnvoll, wenn man für diese Wiederholung zb. einen individuellen Text oder eine individuelle Uhrzeit, abweichend von den übrigen Wiederholungen, festgelegt hat.  
      <br>
      <template v-for="field in protect_fields">
      <q-checkbox v-model="event.protected_fields" :val="field.field" :label="field.label" /><br>
      </template>
      
      </q-tab-panel>     
    </q-tab-panels>

      <div class="col-12">
      <q-btn label="Speichern" type="submit" @click="onSubmitClick" color="primary"/>
      <q-btn label="Zur Tagesansicht" :to="urls.day" color="primary" flat class="q-ml-sm" />
      <q-btn label="Zur Kalenderansicht" :to="urls.kalender" color="primary" flat class="q-ml-sm" />
      <q-btn label="Zum Fahrtenbuch" :href="urls.fahrtenbuch" color="primary" flat class="q-ml-sm" />
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
        <q-option-group
        v-model="save_which"
        inline
        :options="[
          { label: 'nur diesen Termin', value: 'this_only' },
          { label: 'auch kommende Wiederholungen', value: 'recurrencies' }
        ]"
      />
          <template v-if="save_which=='recurrencies'">
          <div  class="text-h6">Markierte Wiederholungen werden überschrieben</div>
          <template v-for="month in recurrencies_store.monthlyEvents">
            <q-list bordered padding>
            <q-item-label header>{{month.formattedmonth}}</q-item-label>
            
            <template v-for="event in month.events">
            <q-item  tag="label" v-ripple>
            <q-separator />
            <q-item-section side top>
              <q-checkbox v-model="recurrencies_store.recurrenceSelections[event.id]" />
            </q-item-section>
    
            <q-item-section>
              <q-item-label>{{event.title}}
              <div class="text-subtitle2">
              {{ event.formattedDate }}
              <template v-if="event.formattedEndDate != ''">
                <br>
                {{ event.formattedEndDate }}
              </template>
              </div>              
              </q-item-label>
              <q-item-label caption>
                {{event.description}}
              </q-item-label>
            </q-item-section>
          </q-item> 
          <q-separator inset="item" />  
          </template>
                  
            </q-list>            
          </template>  
          </template>          
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