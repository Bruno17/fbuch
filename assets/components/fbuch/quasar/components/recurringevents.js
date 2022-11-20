import eventlist from './dayevents.js'
//import api_select from '../../components/api_select.js'
//import { useGetWeekStart } from "../../composables/dateHelpers.js";
import { useLoadPermissions,useHasPermission } from "../composables/helpers.js";

export default {

    props:{
      parent:null
    }, 
      components: {
      eventlist:eventlist,
      //api_select:api_select  
    },

    setup(props) {
    
      const {onMounted, ref, $forceUpdate } = Vue;
      const title = 'Wochenansicht';
      const params = Vue.$router.currentRoute._value.params;
      //const date = params.year + '-' + params.month + '-' +params.day;
      const dates = ref([]);
      const view = params.view;
      const checkPermissions = 'fbuch_edit_termin,fbuch_create_termin,fbuch_delete_termin';
      const monthlyEvents = ref({});
      const recurrencesDates = ref({});
      const recurrences_dialog = ref(false);
      const now = new Date();
      const year_month_now = Quasar.date.formatDate(now, 'YYYY/MM');
      const recurre = ref({
        action:'create_recurrencies',
        parent:props.parent,
        allow_multiple_same_day:false,
        days:[]
      });
            
      onMounted(() => {
        useLoadPermissions(checkPermissions);
        loadEvents();
      })

      function prepareEvents(events){
        monthlyEvents.value=[];
        recurrencesDates.value=[];
        const preparedEvents = {};
        let prevmonth = '0';
        let month = '0';
        events.forEach((event, id) => {
          month = Quasar.date.formatDate(event.date, 'YYYY-MM');
          if (month != prevmonth){
            preparedEvents[month] = {};
            preparedEvents[month] = {};
            preparedEvents[month]['events'] = [];
            preparedEvents[month]['month'] = month;
            preparedEvents[month]['formattedmonth'] = Quasar.date.formatDate(event.date, 'MMMM YYYY');
            prevmonth = month;
          }  
          preparedEvents[month]['events'].push(event);
          recurrencesDates.value.push(Quasar.date.formatDate(event.date, 'YYYY/MM/DD'));
    
        })
        monthlyEvents.value = preparedEvents;
      }
      
      function getPossibleDates(date){
          console.log(date);
          const today = Quasar.date.formatDate(now, 'YYYY/MM/DD');
          return date >= today && !recurrencesDates.value.includes(date);    
      }

      function loadEvents(){
        const data = {};
        if (props.parent){
          data.parent = props.parent;
        }        
        const ajaxUrl = modx_options.rest_url + 'Dates';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           prepareEvents(response.data.results);
           //console.log(monthlyEvents.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }
      
      function create_recurrencies(){
          const ajaxUrl = modx_options.rest_url + 'Recurrences';
          axios.post(ajaxUrl,recurre.value)
          .then(function (response) {
              //event.value = response.data.object;
              recurrences_dialog.value = false;
              recurre.value.days = [];
              loadEvents();
              //$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          }); 
      } 
      
      function onNavigation(view){
        console.log('onNavigation',view);
      }

      return {
        monthlyEvents,
        useHasPermission,
        create_recurrencies,
        loadEvents,
        onNavigation,
        recurrences_dialog,
        recurre,
        year_month_now,
        getPossibleDates
      }
    },
    template: `
      <q-btn v-if="useHasPermission('fbuch_create_termin')" icon="add" @click="recurrences_dialog=true" >
      Wiederholungen erstellen
      </q-btn> 
      <template v-for="month in monthlyEvents">
        <eventlist :loadEvents="loadEvents" :formattedDate="month.formattedmonth" :events="month.events" view="recurrencies" />  
      </template>

      <q-dialog v-model="recurrences_dialog">
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6">Wiederholungen erstellen</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>

        {{recurre}}

          <div class="text-h6">Individuelle Wiederholungen auswählen</div>
          <q-date 
          @navigation="onNavigation" 
          v-model="recurre.days" 
          mask="YYYY-MM-DD" 
          multiple 
          :navigation-min-year-month="year_month_now"
          :options="getPossibleDates"
          >
          </q-date>
 
        </q-card-section>
        <q-card-actions vertical align="right"> 
        <q-btn color="primary" v-if="useHasPermission('fbuch_create_termin')" @click="create_recurrencies()" >
        Wiederholungen erstellen
        </q-btn>  
        </q-card-actions>         
      </q-card>
    </q-dialog>

    `
    // or `template: '#my-template-element'`
  }