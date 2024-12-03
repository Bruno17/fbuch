//import eventlist from './dayevents.js'
//import api_select from '../../components/api_select.js'
//import { useGetWeekStart } from "../../composables/dateHelpers.js";
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    emits: ['onOk'], 
    props:{
      boot:null,
      entry:null
    }, 
      components: {
      //eventlist:eventlist,
      //api_select:api_select  
    },

    setup(props, { emit }) {

    
      const {onMounted, ref, watch, $forceUpdate } = Vue;
      const params = Vue.$router.currentRoute._value.params;
      const gattung_options = ref([]); 
      const form = ref({});
      const gattungselect = ref(null);
      const year = params.year || Quasar.date.formatDate(new Date(), 'YYYY');
      const month = params.month || Quasar.date.formatDate(new Date(), 'MM');
      const day = params.day || Quasar.date.formatDate(new Date(), 'DD');
      const date = year + '-' + month + '-' +day;      
     
      onMounted(() => {
        useLoadPermissions();
        //loadEvents();
      })

      function prepareGattungOptions(){
        let options = [];
        let selected = {};
        props.boot.gattungen.forEach((gattung, id) => {
           options.push({'value':gattung.id,'label':gattung.shortname});
           if (gattung.id == props.entry.gattung_id){
            form.value.gattung_id = {'value':gattung.id,'label':gattung.shortname};
           }
        })
        gattung_options.value = options; 
       
      }  
      
      function onBeforeShow(){
          prepareGattungOptions();
          form.value.permanent = "0";  
      }

      function onSubmit(){
          if (form.value.permanent == "1"){
              save();
          } else {
            emit('onOk',form);               
          }
  
      }

      function save() {
        let data = {};
        data.boot_id = props.boot.id;
        data.createdon = date;
        data.gattung_id = form.value.gattung_id.value;
        data.name = 'Fahrt:' + props.entry.id;

          const ajaxUrl = modx_options.rest_url + 'Bootriggerungen';
          axios.post(ajaxUrl, data)
            .then(function (response) {
              const success = response.data.success;
              emit('onOk',form); 
            })
            .catch(function (error) {
              console.log(error);
            });
 
      }       

 

      return {
        useHasPermission,
        props,
        form,
        gattungselect,
        gattung_options,
        onBeforeShow,
        onSubmit,
      }
    },
    template: '#riggerungform'
  }