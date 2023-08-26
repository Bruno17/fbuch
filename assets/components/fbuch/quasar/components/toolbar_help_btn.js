import { useHasPermission } from "../composables/helpers.js";
import valuesetter from './valuesetter.js'

export default {

  components: {
    valuesetter: valuesetter
  },    

  setup() {

    const { onMounted, ref } = Vue;
    const modx = modx_options;
    const { useQuasar } = Quasar;
    const $q = useQuasar();
    const state = ref({});
    const qr_code = ref(null);
    //const router_route = ref(Vue.$router.currentRoute._value); 

    function createCode(){
        const container = qr_code.value;
        state.value.route = modx.site_url + modx.resource_uri + '?r=' + Vue.$router.currentRoute.value.fullPath;
        QrCreator.render({
            text: state.value.route,
            radius: 0, // 0.0 to 0.5
            ecLevel: 'H', // L, M, Q, H
            fill: '#000000', // foreground color
            background: '#ffffff', // color or null for transparent
            size: 250 // in pixels
          }, container);        
    }

    return {
        state,
        createCode,
        qr_code
    }
  },
  template: '#toolbar-help-btn'

}