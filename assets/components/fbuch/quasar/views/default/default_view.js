import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js'

export default {

    components: {
        valuesetter: valuesetter
    },
    setup() {

        const { onMounted, ref } = Vue;
        const mm_expanded = ref(false);
        const state = ref({});

        onMounted(() => {
            useLoadPermissions();
        })
        return {
            mm_expanded,
            state,
            useHasPermission 
        }
    },

    template: '#default_view'
}