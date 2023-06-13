
export default {
    emits: ['hasmounted'],
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
  
      setup(props,{emit}) {
        const {onMounted,onCreated, ref, watch } = Vue;
        const { useQuasar } = Quasar;
        const options = ref([]);
        const $q = useQuasar();

        onMounted(() => {
            emit('hasmounted');
        })

      return {}      
    
      },
      template: `

      `
      // or `template: '#my-template-element'`
    }