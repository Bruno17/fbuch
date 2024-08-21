import api_select from '../../components/api_select.js'
import datepicker from '../../components/datepicker_simple.js'
import valuesetter from '../../components/valuesetter.js'

import { useLoadPermissions, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

  props: {

  },
  components: {
    api_select: api_select,
    datepicker: datepicker,
    valuesetter: valuesetter
  },

  setup(props) {

    const { onMounted, ref, watch} = Vue;
    const { useQuasar,LocalStorage,SessionStorage } = Quasar;
    const $q = useQuasar();     
    const routeValue = Vue.$router.currentRoute._value;
    const params = routeValue.params;
    let id = params.id || 'new';
    let bootid = params.bootid || 'new';
    const boat = ref({});
    const entry = ref({});
    const submitclicked = ref(false);
    const year = params.year || Quasar.date.formatDate(new Date(), 'YYYY');
    const month = params.month || Quasar.date.formatDate(new Date(), 'MM');
    const day = params.day || Quasar.date.formatDate(new Date(), 'DD');
    const date = year + '-' + month + '-' +day;
    const return_to = SessionStorage.getItem('last_href') || false; 
    let gattung_options = ref([]);   

    onMounted(() => {
      entry.value.boot_id = bootid;
      entry.value.createdon = date;
      useLoadPermissions();
      loadEntry();
    })

    function loadEntry(){
      if (id == 'new') {
        loadBoatData();
        return;
      }
      var data = {};
      var ajaxUrl = modx_options.rest_url + 'Bootriggerungen/' + id;

      axios.get(ajaxUrl,{params:data})
      .then(function (response) {
          entry.value = response.data.object;
          bootid = entry.value.boot_id;
          loadBoatData();
      })
      .catch(function (error) {
          console.log(error);
      });            
    }    

    function loadBoatData(){
        var data = {};
        var ajaxUrl = modx_options.rest_url + 'Boote/' + bootid;

        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
            boat.value = response.data.object;
            entry.value.gattung_id = boat.value.gattung_id;
            prepareGattungOptions();
        })
        .catch(function (error) {
            console.log(error);
        });            
    }

    function prepareGattungOptions(){
         boat.value.gattungen.forEach((gattung, id) => {
            gattung_options.value.push({'value':gattung.id,'label':gattung.shortname});
        })        
    }


    function onSubmitClick() {
      submitclicked.value = true;
    }

    function onSubmit() {
       save();
    }

    function save() {

      if (id == 'new') {
        const ajaxUrl = modx_options.rest_url + 'Bootriggerungen';
        axios.post(ajaxUrl, entry.value)
          .then(function (response) {
            const success = response.data.success;
            let message = response.data.message;
            message = message == 'name_exists' ? 'Ein Eintrag mit dem gleichen Namen existiert bereits.' : message;
            if (!success){
              $q.dialog({
                title: 'Warnung!',
                message: message
              })
              return;              
            }            
            window.location.href = return_to;
          })
          .catch(function (error) {
            console.log(error);
          });
      } else {
        const ajaxUrl = modx_options.rest_url + 'Bootriggerungen/' + id;
        axios.put(ajaxUrl, entry.value)
          .then(function (response) {
            const success = response.data.success;
            let message = response.data.message;
            message = message == 'name_exists' ? 'Ein Eintrag mit dem gleichen Namen existiert bereits.' : message;
            if (!success){
              $q.dialog({
                title: 'Warnung!',
                message: message
              })
              return;              
            }
            //event.value = response.data.object;
            //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
            window.location.href = return_to;
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }    

 
    return {
      boat,
      entry,
      onSubmit,
      onSubmitClick,
      submitclicked,
      useHasPermission,
      routeValue,
      return_to,
      gattung_options
    }
  },
  template: '#riggerungform'
  // or `template: '#my-template-element'`
}