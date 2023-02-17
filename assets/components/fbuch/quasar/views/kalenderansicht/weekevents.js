import dayevents from '../../components/kalenderansicht/dayevents.js'
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

      onMounted(() => {
        setWeekDates();
        useLoadPermissions();
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
    template: '#weekevents-view'
    // or `template: '#my-template-element'`
  }