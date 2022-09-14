import dayevent from '../../components/dayevent.js'

export default {

    props:{
      date:'',
      view:'',
      type:null
    }, 
    components: {
      dayevent:dayevent    
    },

    setup(props) {
    
      const {onMounted, ref } = Vue;
      const title = 'Tagesansicht';
      //const params = Vue.$router.currentRoute._value.params;
      const date = props.date;
      const year = Quasar.date.formatDate(date, 'YYYY');
      const month = Quasar.date.formatDate(date, 'MM');
      const day = Quasar.date.formatDate(date, 'DD');            
      const formattedDate = Quasar.date.formatDate(date, 'dd DD. MMMM YYYY');
      const loadedEvents = ref([]);
      const checkPermissions = 'fbuch_edit_termin,fbuch_create_termin,fbuch_delete_termin';
      const userPermissions = ref([]);

      onMounted(() => {
        loadDayEvents();
        getPermissions();
      })

      function getPermissions(){
        const data = {};
        const ajaxUrl = fbuch_options.assets_url + 'components/fbuch/rest/Permissions';
        data.permissions = checkPermissions;
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           userPermissions.value = response.data.results;
           //console.log(loadedEvents.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }

      function hasPermission(permission){
        return userPermissions.value.includes(permission); 
      }
      
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

      function loadDayEvents(){
        const data = {};
        data.start = date + ' 00:00:00';
        data.end = date + ' 23:59:59';
        if (props.type){
          data.types = props.type;
        }        

        const ajaxUrl = fbuch_options.assets_url + 'components/fbuch/rest/Dates';

        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           loadedEvents.value = prepareEvents(response.data.results);
           //console.log(loadedEvents.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
    } 

      return {year,month,day, title, formattedDate, loadedEvents, hasPermission, loadDayEvents }
    },
    template: `
    <div class="q-pa-md full-width" >
      <div class="text-h4 text-center"> {{ formattedDate }} </div>
      <div class="q-pa-md q-gutter-sm">
      <q-btn v-if="hasPermission('fbuch_create_termin')" icon="add" :to="'/event-create/' +year+'/'+month+'/'+day" >
      Termin erstellen
      </q-btn>
      <q-btn v-if="view=='day'" label="Wochenansicht" :to="'/events/week/'+year+'/'+month+'/'+day"></q-btn>
      <q-btn v-if="view=='week'" label="Tagesansicht" :to="'/events/day/'+year+'/'+month+'/'+day"></q-btn>      
      <q-btn label="Kalenderansicht" :to="'/'+year+'/'+month" ></q-btn>
      </div>
      <div class="row q-col-gutter-sm">
        <template v-for="event in loadedEvents">
          <dayevent 
          :hasPermission="hasPermission" 
          :event="event"
          :loadDayEvents="loadDayEvents"
          ></dayevent>
        </template>
      </div>
    </div>
    `
    // or `template: '#my-template-element'`
  }