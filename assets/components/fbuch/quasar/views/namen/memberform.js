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
    const { useQuasar } = Quasar;
    const $q = useQuasar();     
    const routeValue = Vue.$router.currentRoute._value;
    const params = routeValue.params;
    let id = params.id || 'new';
    const entry = ref({});
    const state = ref({});
    const submitclicked = ref(false);

    onMounted(() => {
      useLoadPermissions();
      loadEntry();
    
    })

    function loadEntry() {
      let data = {};
      let ajaxUrl = modx_options.rest_url + 'Names/' + id;
      if (routeValue.name == 'memberform_create') {
          return;
      }

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;
          entry.value = object;
         
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function onSubmitClick() {
      submitclicked.value = true;
    }

    function onSubmit() {
       console.log('test');
       save();
    }

    function save() {

      if (id == 'new') {
        const ajaxUrl = modx_options.rest_url + 'Names';
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
            Vue.$router.push('/');
          })
          .catch(function (error) {
            console.log(error);
          });
      } else {
        const ajaxUrl = modx_options.rest_url + 'Names/' + id;
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
            Vue.$router.push('/');
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }    

 
    return {
      entry,
      state,
      onSubmit,
      onSubmitClick,
      submitclicked,
      useHasPermission,
      routeValue
    }
  },
  template: '#memberform-view'
  // or `template: '#my-template-element'`
}