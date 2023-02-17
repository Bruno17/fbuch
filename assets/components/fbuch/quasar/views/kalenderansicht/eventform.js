import eventlist from '../../components/kalenderansicht/recurringevents.js'
import eventform from '../../components/kalenderansicht/eventform.js'


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
    template:'#eventform-view'
    // or `template: '#my-template-element'`
  }