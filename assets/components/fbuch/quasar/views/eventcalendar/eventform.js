import eventlist from '../../components/recurringevents.js'
import eventform from '../../components/eventform.js'


export default {

    components : {
      eventlist : eventlist,
      eventform : eventform
    },

    setup() {

      const {onMounted, ref } = Vue;
      const params = Vue.$router.currentRoute._value.params;
      let id = params.id || 'new';
      const date = params.year + '-' + params.month + '-' +params.day;
      const event = ref({});
      const recurrence_event = ref({});
      const tab = ref('');

      onMounted(() => {
        loadEvent();
      })
      
      function loadEvent(){
        const data = {};
        if (id == 'new'){
          data.date = date;
        }
        const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
            const object = response.data.object;
            if (object.parent > 0){
                id = object.parent;
                recurrence_event.value = object;
                tab.value = 'this recurrence';
                loadEvent();
                return;      
            }
            event.value = object;
            tab.value = tab.value=='' ?'date':tab.value;
            //eventform.value.resetValidation();
            //eventtype.value.resetValidation();
            //console.log(eventtype.value);
        })
        .catch(function (error) {
            console.log(error);
        }); 
    } 

      return { 
        tab,
        event,
        recurrence_event
     }
    },
    template: `
    <div class="q-pa-md full-width" style="height: 400px;">
      <div class="text-h4 text-center"> {{ event.title }} </div>
      <div class="col-12">
      <q-tabs
        v-model="tab"
        align="left"
        no-caps
        outside-arrows
        mobile-arrows
        class=""
      >
        <q-tab name="date" label="Haupttermin" />
        <q-tab name="recurrences" label="Wiederholungen" />
        <q-tab v-if="recurrence_event.parent>0" name="this recurrence" label="Diese Wiederholung" />
      </q-tabs>
      </div>

      <q-tab-panels
      v-model="tab"
      >
      <q-tab-panel name="date">
      <eventform :event="event" />
      </q-tab-panel>

      <q-tab-panel name="recurrences">
        <eventlist v-if="event.id !=null " showhidden="1" :parent="event.id" date="2022-11-05" :view="view" :type="selectedType"/>
        <q-banner v-if="event.id ==null " inline-actions class="text-white bg-red">
        Um Wiederholungen erstellen zu können, muß der Haupttermin erst gespeichert werden.
      </q-banner>        
      </q-tab-panel>
      <q-tab-panel name="this recurrence">
        <h4>Diese Wiederholung</h4>

        <eventform :event="recurrence_event" />
      </q-tab-panel>      
    </q-tab-panels>

      </div>  
      </q-form>
  
       <br>


    </div>
    `
    // or `template: '#my-template-element'`
  }