//import { useSetDateDiff, useSetEndDate } from "../composables/dateHelpers.js";

export default {
    props: {
      modelValue: ''
    },
  
    setup(props, context) {
      const { onMounted, ref, watch } = Vue;
      const { useQuasar } = Quasar;
      const options = ref([]);
      const $q = useQuasar();
      const fieldRef = ref(null);
      const popupstate = ref(false);
  
      onMounted(() => {
        const newDate = new Date(); 
        let year = parseInt(Quasar.date.formatDate(newDate, 'YYYY'));
        for (let step = 1; step < 11; step++) {
             options.value.push(year);
             year--;
        }        
      })
  
      const updateValue = (value) => {
        if (value != null) {
          context.emit('update:modelValue', value);
        }
  
        return;
      }
  
      return {
        updateValue,
        options,
        popupstate,
        fieldRef
      }
  
    },
    template: `
        <q-select :options="options" v-model="modelValue" outlined @update:model-value="updateValue" 
        @popup-show="popupstate=true"
        @popup-hide="popupstate=false"
        ref="fieldRef"
        >
        <template v-if="($q.screen.width <= 760) && popupstate" v-slot:prepend>
        <q-btn  flat @click="$refs.fieldRef.hidePopup()" icon="close" />
        </template>      
        </q-select>
        `
    // or `template: '#my-template-element'`
  }