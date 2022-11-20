import dayevent from './dayevent.js'

export default {

    props:{
      date:'',
      view:'',
      type:null,
      formattedDate:null,
      events:null,
      loadEvents:Function
    }, 
    components: {
      dayevent:dayevent    
    },

    setup(props) {
    
      const {onMounted, ref, computed } = Vue;
      //const params = Vue.$router.currentRoute._value.params;
      const date = props.date;
      const formattedDate = props.formattedDate || Quasar.date.formatDate(date, 'dd DD. MMMM YYYY');
      const loadedEvents = ref([]);
      const propsEvents = ref(props.events);
      //const propsEvents = computed(()=>props.events);
      

      onMounted(() => {
        loadDayEvents();
        //getPermissions();
      })

      function prepareEvents(events){
        const preparedEvents = [];
        events.forEach((event, id) => {
          event['formattedDate'] = Quasar.date.formatDate(event.date, 'dd DD. MM. YYYY ' + event.start_time + ' - ');
          if (Quasar.date.isSameDate(event.date, event.date_end,'days')) {
            event['formattedDate'] += event.end_time;
            event['formattedEndDate'] = '';     
          } else {
            event['formattedEndDate'] = Quasar.date.formatDate(event.date_end, 'dd DD. MM. YYYY ' + event.end_time);  
          }
          preparedEvents.push(event);      
        })
        return preparedEvents;
      }

      function reloadEvents(){
        if (props.loadEvents){
          props.loadEvents();
          return;
        }
        loadDayEvents();
      }

      function loadDayEvents(){
        if (propsEvents.value){
          loadedEvents.value = prepareEvents(propsEvents.value);
          return;  
        }

        const data = {};
        data.start = date + ' 00:00:00';
        data.end = date + ' 23:59:59';
        if (props.type){
          data.types = props.type;
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

      return {formattedDate, loadedEvents, reloadEvents }
    },
    template: `
      <div class="q-pa-md full-width" >
      <div class="text-h4 text-center"> {{ formattedDate }} </div>
      <div class="q-pa-md q-gutter-sm">
      <slot name="buttons"></slot>
      </div>
      <div class="row q-col-gutter-sm">
        <template v-for="event in loadedEvents">
          <dayevent 
          :event="event"
          :loadDayEvents="reloadEvents"
          ></dayevent>
        </template>
      </div>
    </div>
    `
    // or `template: '#my-template-element'`
  }