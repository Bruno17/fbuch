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
    entry.value.members = [];

    onMounted(() => {
      useLoadPermissions();
      loadEntry();
    
    })

    function loadEntry() {
      let data = {};
      let ajaxUrl = modx_options.rest_url + 'DoorcodesMembers/' + id;

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;
          const timeStamp = Date.now();
          if (routeValue.name=='codeform_add'){
            entry.value = {
                code:object.code,
                member_id:0,
                other_person:'',
                Code_blocked:0,
                Code_time_setting:0,
                members:object.members,
                assignedon:Quasar.date.formatDate(timeStamp, 'YYYY-MM-DD 00:00:00')
            } 

          } else {
            entry.value = object;
          }          

          if (entry.value.member_id == 0){
            state.value.enable_personSelect = true;
            if (entry.value.other_person == ''){
              state.value.enable_other_person = true;
              entry.value.assignedon = Quasar.date.formatDate(timeStamp, 'YYYY-MM-DD 00:00:00');
            }            
          }


         
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function onSubmitClick() {
      submitclicked.value = true;
    }

    function onSubmit() {
       save();
    }

    function inArray(needle, haystack) {
      var length = haystack.length;
      for(var i = 0; i < length; i++) {
          if(haystack[i] == needle) return true;
      }
      return false;
  }    

    function checkExistingMemberCodes(member_id){
      let data = {};
      data.member_id = member_id;
      let ajaxUrl = modx_options.rest_url + 'DoorcodesMembers';

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
        if(response.data.results.length>0){
          let codes = '';
          let comma = '';
          for (let i=0;i<response.data.results.length;i++){
              codes = codes + comma + response.data.results[i].code;  
              comma = ',';
          }
          $q.dialog({
            title: 'Achtung!',
            message: 'Der ausgewählten Person wurden bereits Codes ('+codes+') zugewiesen.',
            options: {
              type: 'checkbox',
              model: [],
              // inline: true
              items: [
                { label: 'diesen Code zusätzlich vergeben', value: 'add_code' },
              ]
            },
            cancel: true,
            persistent: true
          }).onOk(data => {
             if(inArray('add_code',data)){
               state.value.enable_save_button=true;
               entry.value.other_person='';
               state.value.enable_other_person = false;
             } else {
               entry.value.member_id = 0;    
             }
          }).onCancel(() => {
            entry.value.member_id = 0;
          }).onDismiss(() => {
            //console.log('I am triggered on both OK and Cancel')            
          })            
        } else {
          entry.value.other_person='';
          state.value.enable_other_person = false;
          state.value.enable_save_button=true;           
        }
         
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function onSelectPerson(value){
      state.value.enable_save_button=false; 
      checkExistingMemberCodes(value.id);
    }

    function save() {
      let data = entry.value;
      if (typeof(entry.value.member_id=='object')){
        data.member_id = entry.value.member_id.id;        
      }

      if (routeValue.name=='codeform_add'){
        const ajaxUrl = modx_options.rest_url + 'DoorcodesMembers';

        axios.post(ajaxUrl, data)
          .then(function (response) {
            const success = response.data.success;
            let message = response.data.message;
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
        const ajaxUrl = modx_options.rest_url + 'DoorcodesMembers/' + id;
        axios.put(ajaxUrl, data)
          .then(function (response) {
            const success = response.data.success;
            let message = response.data.message;
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
      onSelectPerson,
      submitclicked,
      useHasPermission,
      routeValue
    }
  },
  template: '#codeform-view'
  // or `template: '#my-template-element'`
}