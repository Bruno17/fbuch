import api_select from '../../components/api_select.js'
import timeinput from '../../components/timeinput.js'
import datepicker from '../../components/datepicker.js'

export default {

    components : {
      api_select : api_select,
      datepicker : datepicker,
      timeinput : timeinput
    },

    setup() {
    
      const {onMounted, ref } = Vue;
      const params = Vue.$router.currentRoute._value.params;
      const id = params.id || 'new';
      const date = params.year + '-' + params.month + '-' +params.day;
      const event = ref({});
      const state = ref({});

      onMounted(() => {
        loadEvent();
        state.value.days = 0;
        state.value.hours = 0;
        state.value.minutes = 0;
        state.value.minutes_total = 0;
        state.value.duration_valid = true;
      })
      
      function loadEvent(){
        const data = {};
        if (id == 'new'){
          data.date = date;
        }
        const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
            event.value = response.data.object;
        })
        .catch(function (error) {
            console.log(error);
        }); 
    } 

    function onSubmit(){
        if (id == 'new'){
          const ajaxUrl = modx_options.rest_url + 'Dates';
          axios.post(ajaxUrl,event.value)
          .then(function (response) {
              //event.value = response.data.object;
              Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
          })
          .catch(function (error) {
              console.log(error);
          }); 
        } else {
          const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
          axios.put(ajaxUrl,event.value)
          .then(function (response) {
              //event.value = response.data.object;
              Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
          })
          .catch(function (error) {
              console.log(error);
          }); 
        }

    
    }


    function onReset(){
      console.log('reset');
      Vue.$router.go(-1);
    }
  
      return { event, onSubmit, state, onReset }
    },
    template: `
    <div class="q-pa-md full-width" style="height: 400px;">
      <div class="text-h4 text-center"> {{ event.title }} </div>
      <div class="q-pa-md" >

      <q-form
        @submit="onSubmit"
        @reset="onReset"
        class="q-col-gutter-md q-gutter-md row"
      >

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
      label="Gruppe/Ziel"
    />

    <api_select
      class="col-md-4 col-sm-4 col-xs-12"
      v-model="event.type"
      label="Termin Art"
      controller="Datetypes?limit=100000&returntype=options"
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
          <q-btn label="Speichern" type="submit" color="primary"/>
          <q-btn label="Abbrechen" type="reset" color="primary" flat class="q-ml-sm" />
        </div>
      </q-form>
  
      {{ eventx }} <br>
      {{ statex }}

    </div>      
    </div>
    `
    // or `template: '#my-template-element'`
  }