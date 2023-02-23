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
        const { useQuasar } = Quasar;
        const options = ref([]);
        const $q = useQuasar();

        onMounted(() => {
            for (let h = 0; h <= 23;  h++) {
                h = h < 10 ? '0' + h : h;
                for (let m = 0; m < 60;  m+=15) {
                    var min = m < 10 ? '0' + m : m;
                    options.value.push(h + ':' + min)
                }                
            }
            options.value.push('23:59');          
        })

        const updateValue = (value) => {
            const oldvalue = props.event[props.timeendfield];
            if (value != null){
              context.emit('update:modelValue', value);
            }

            if (props.which == 'start'){
                useSetEndDate(props);
            } 

            useSetDateDiff(props);

            if (props.which == 'end'){
                if (props.state.minutes_total < 0) {
                    props.event[props.timeendfield] = oldvalue; 
                    useSetDateDiff(props);
                    $q.dialog({
                        title: 'Ungültiges Terminende angegeben',
                        message: 'Das Terminende wurde auf den alten Wert zurückgesetzt.'
                      });
                }
              }             

        } 
  
      return {updateValue, options}      
    
      },
      template: `
      <q-select :options="options" v-model="modelValue" outlined @update:model-value="updateValue" >
      </q-select>
      `
      // or `template: '#my-template-element'`
    }