import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import skillscategorytree from '../../components/kompetenzstufenskills/skills_category_tree.js';

export default {

    components: {
        skillscategorytree: skillscategorytree
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
 
        onMounted(() => {
            useLoadPermissions();
            //loadBootsnutzergruppen();
        })

        
        return {
            state,
            useHasPermission,
        }
    },

    template: '#view'
}