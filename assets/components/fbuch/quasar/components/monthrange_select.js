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
      options.value.push({ 'value': 12, 'label': '1 Jahr' });
      options.value.push({ 'value': 1, 'label': '1 Monat' });
      for (let i = 2; i <= 11; i++) {
        options.value.push({ 'value': i, 'label': i + ' Monate' });
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