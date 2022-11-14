import eventlist from './dayevents.js'
//import api_select from '../../components/api_select.js'
//import { useGetWeekStart } from "../../composables/dateHelpers.js";

export default {

    props:{
      parent:null
    }, 
      components: {
      eventlist:eventlist,
      //api_select:api_select  
    },

    setup(props) {
    
      const {onMounted, ref } = Vue;
      const title = 'Wochenansicht';
      const params = Vue.$router.currentRoute._value.params;
      //const date = params.year + '-' + params.month + '-' +params.day;
      const dates = ref([]);
      const view = params.view;

      const loadedEvents = ref({});

      onMounted(() => {
        loadEvents();
      })

      function prepareEvents(events){
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
    
        })
        return preparedEvents;
      }      

      function loadEvents(){
        const data = {};
        const startdate = '2022-01-01';
        const enddate = '2022-12-31';
        data.start = startdate + ' 00:00:00';
        data.end = enddate + ' 23:59:59';
        if (props.parent){
          data.parent = props.parent;
        }        

        const ajaxUrl = modx_options.rest_url + 'Dates';

        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           loadedEvents.value = prepareEvents(response.data.results);
           //console.log(loadedEvents.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
    } 

      return {loadedEvents}
    },
    template: `
      <template v-for="month in loadedEvents">
        <eventlist :formattedDate="month.formattedmonth" :events="month.events" view="recurrencies" />  
      </template>
    `
    // or `template: '#my-template-element'`
  }