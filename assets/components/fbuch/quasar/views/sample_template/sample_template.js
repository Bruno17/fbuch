import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {

    },
    setup() {

        const { onMounted, ref } = Vue;

        onMounted(() => {
            useLoadPermissions();
        })
        return {
 
        }
    },

    template: '#sample_template'
}