import dayevents from '../../components/dayevents.js'
import api_select from '../../components/api_select.js'
import { useGetWeekStart } from "../../composables/dateHelpers.js";
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
      dayevents:dayevents,
      api_select:api_select  
    },

    setup() {
    
      const {onMounted, ref } = Vue;
      const title = 'Wochenansicht';
      const params = Vue.$router.currentRoute._value.params;
      const date = params.year + '-' + params.month + '-' +params.day;
      const dates = ref([]);
      const selectedType = ref(Vue.$router.currentRoute._value.query.type||null);
      const view = params.view;
      const checkPermissions = 'fbuch_edit_termin,fbuch_create_termin,fbuch_delete_termin';

      onMounted(() => {
        setWeekDates();
        useLoadPermissions(checkPermissions);
      })

      function prepareDate(date){
          return {
              date: Quasar.date.formatDate(date,'YYYY-MM-DD'),
              year: Quasar.date.formatDate(date, 'YYYY'),
              month: Quasar.date.formatDate(date, 'MM'),
              day: Quasar.date.formatDate(date, 'DD')               
          };
      }

      function setWeekDates(){
        if (view == 'week'){
          let daydate = null;
          const startOfWeek = useGetWeekStart(date);
          dates.value.push(prepareDate(startOfWeek));
          for (let i=1;i<=6;i++){
            daydate = Quasar.date.addToDate(startOfWeek,{days:i});
            dates.value.push(prepareDate(daydate));
          }

          const endOfWeek = Quasar.date.addToDate(startOfWeek,{days:6});
           
        }  else {
            dates.value.push(prepareDate(date));
        }
      }    
      function setTypeFilter () {
        //console.log(selectedType.value);
        Vue.$router.push(prepareRoute(date));
      }
      function prepareRoute(date){
        const query = {}
        if (selectedType.value){
            query.type = selectedType.value;
        }
        return {name:'events',
            params:{
                view:view,
                year:Quasar.date.formatDate(date, 'YYYY'),
                month:Quasar.date.formatDate(date, 'MM'),
                day:Quasar.date.formatDate(date, 'DD')
            },
            query: query    
            
        }      
      }      
      function onPrev() {
        let days = 1;
        if (view == 'week'){
          days = 7;
        }
        const newDate = Quasar.date.subtractFromDate(date,{days:days});
        Vue.$router.push(prepareRoute(newDate));
      }
      function onNext() {
        let days = 1;
        if (view == 'week'){
          days = 7;
        }
        const newDate = Quasar.date.addToDate(date,{days:days});
        Vue.$router.push(prepareRoute(newDate));
      }
      function onToday() {
        const newDate = new Date;
        Vue.$router.push(prepareRoute(newDate));
      }

      return {
        params, 
        title, 
        dates,
        view,
        selectedType,
        useHasPermission,
        setTypeFilter,
        onPrev,
        onNext,
        onToday }
    },
    template: `
    <div class="q-pa-md q-gutter-sm row">
      <q-btn outline label="< Früher" @click="onPrev"></q-btn>
      <q-btn outline label="Heute" @click="onToday"></q-btn>
      <q-btn outline label="Später >" @click="onNext"></q-btn>

      <api_select
      clearable
      v-model="selectedType"
      label="Termin Art filtern"
      controller="Datetypes?limit=100000&returntype=options"
      @update:model-value="setTypeFilter"
      >
      </api_select>  
      <q-space/>
      <q-avatar color="blue" class="text-white" icon="question_mark"/>
    </div>    
      <template v-for="date in dates">
        <dayevents :date="date.date" :view="view" :type="selectedType">
        <template v-slot:buttons>
        <q-btn v-if="useHasPermission('fbuch_create_termin')" icon="add" :to="'/event-create/' +date.year+'/'+date.month+'/'+date.day" >
        Termin erstellen
        </q-btn>
        <q-btn v-if="view=='day'" label="Wochenansicht" :to="'/events/week/'+date.year+'/'+date.month+'/'+date.day"></q-btn>
        <q-btn v-if="view=='week'" label="Tagesansicht" :to="'/events/day/'+date.year+'/'+date.month+'/'+date.day"></q-btn>      
        <q-btn label="Kalenderansicht" :to="'/'+date.year+'/'+date.month" ></q-btn>  
        <q-btn label="Zum Fahrtenbuch" :href="'/?offset='+date.date+'&type=dragdrop&dir=none'" />      
        </templae>
        </dayevents>
      </template>
    `
    // or `template: '#my-template-element'`
  }