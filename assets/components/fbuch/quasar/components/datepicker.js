import { useSetDateDiff, useSetEndDate } from "../composables/dateHelpers.js";

export default {
  props: {
    modelValue: '',
    startfield:'',
    endfield:'',
    timestartfield:'',
    timeendfield:'',    
    which:'',
    event:{},
    state:{}
  },

    setup(props,context) {
      const {onMounted, ref, watch } = Vue;
      const showproxy = ref(false);
      const formattedDate = ref('');
      let proxyclosed = false;
      const { useQuasar } = Quasar;
      const $q = useQuasar();      

      watch(() => props.modelValue, (value) => {
        formattedDate.value = formatDate(value);
        useSetDateDiff(props);
      })

      onMounted(() => {
        formattedDate.value = formatDate(props.modelValue);
        useSetDateDiff(props);
      })

    const onFocus = () => {
       if (proxyclosed){
          proxyclosed = false; 
          return; 
       } 
       showproxy.value = true;    
    } 

    const closeProxy = () => {
      showproxy.value = false;
      proxyclosed = true;
    }
    
    
  const updateValue = (value) => {
    const oldvalue = props.event[props.endfield];
      if (value != null){
        context.emit('update:modelValue', value);
      if (props.which == 'start'){
          useSetEndDate(props);
      } 

      useSetDateDiff(props);    

      if (props.which == 'end'){
        if (props.state.minutes_total < 0) {
            props.event[props.endfield] = oldvalue; 
            useSetDateDiff(props);
            $q.dialog({
                title: 'Ungültiges Terminende angegeben',
                message: 'Das Terminende wurde auf den alten Wert zurückgesetzt.'
              });
         }
      }      

      }
      closeProxy();             

  } 
  
  const formatDate = (datetime) => {
    if (!datetime) return null;
    const [date,time] = datetime.split(' ');
    const [year, month, day] = date.split('-');
    return `${day}.${month}.${year}`;
  }

    return {closeProxy, onFocus, updateValue, formatDate, showproxy, formattedDate}      
  
    },
    template: `
    <q-input 
    v-model="formattedDate" 
    @blur="formattedDate=formatDate(modelValue)" 
    @click="showproxy=true;" 
    @focus="onFocus" 
    outlined 
    @update:model-value="updateValue" 
    mask="##.##.####"
    :rulesx="['date']">
      <template v-slot:append>
        <q-icon name="event" class="cursor-pointer">
          <q-popup-proxy v-model="showproxy" cover transition-show="scale" transition-hide="scale">
            <q-date v-model="modelValue" mask="YYYY-MM-DD" @update:model-value="updateValue">
              <div class="row items-center justify-end">
                <q-btn @click="closeProxy" label="Abbrechen" color="primary" flat />
              </div>
            </q-date>
          </q-popup-proxy>
        </q-icon>
      </template>
    </q-input>
    `
    // or `template: '#my-template-element'`
  }
  