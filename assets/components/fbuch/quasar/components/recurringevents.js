import eventlist from './dayevents.js'
//import api_select from '../../components/api_select.js'
//import { useGetWeekStart } from "../../composables/dateHelpers.js";
import { useLoadPermissions,useHasPermission } from "../composables/helpers.js";
import { useRecurrenciesStore } from '../stores/eventform.js';

export default {

    props:{
      parent:null
    }, 
      components: {
      eventlist:eventlist,
      //api_select:api_select  
    },

    setup(props) {

      const store = useRecurrenciesStore(); 
    
      const {onMounted, ref, watch, $forceUpdate } = Vue;
      const title = 'Wochenansicht';
      const params = Vue.$router.currentRoute._value.params;
      //const date = params.year + '-' + params.month + '-' +params.day;
      const dates = ref([]);
      const view = params.view;
      const checkPermissions = 'fbuch_edit_termin,fbuch_create_termin,fbuch_delete_termin';
      const recurrences_dialog = ref(false);
      const now = new Date();
      const year_month_now = Quasar.date.formatDate(now, 'YYYY/MM');
      const recurre = ref({
        action:'create_recurrencies',
        parent:props.parent,
        allow_multiple_same_day:false,
        days:[]
      });
     
      /*
      watch(store, (newVal) => {
        console.log('watch',newVal);
      },{deep:true})
      */

      onMounted(() => {
        useLoadPermissions(checkPermissions);
        loadEvents();
      })

      function loadEvents(){
        store.monthlyEvents = {};        
        store.loadEvents(props);
      }

      function getPossibleDates(date){
          //console.log(date);
          const today = Quasar.date.formatDate(now, 'YYYY/MM/DD');
          return date >= today && !store.recurrencesDates.includes(date);    
      }

      function create_recurrencies(){
          const ajaxUrl = modx_options.rest_url + 'Recurrences';
          axios.post(ajaxUrl,recurre.value)
          .then(function (response) {
              //event.value = response.data.object;
              recurrences_dialog.value = false;
              recurre.value.days = [];
              loadEvents();
          })
          .catch(function (error) {
              console.log(error);
          }); 
      } 
      
      function onNavigation(view){
        //console.log('onNavigation',view);
      }

      return {
        useHasPermission,
        create_recurrencies,
        loadEvents,
        onNavigation,
        recurrences_dialog,
        recurre,
        year_month_now,
        getPossibleDates,
        store
      }
    },
    template: `
      <q-btn v-if="useHasPermission('fbuch_create_termin')" icon="add" @click="recurrences_dialog=true" >
      Wiederholungen erstellen
      </q-btn> 
      <template v-for="month in store.monthlyEvents">
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