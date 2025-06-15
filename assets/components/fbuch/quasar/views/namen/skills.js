import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import skillscategorytree from '../../components/kompetenzstufenskills/skills_category_tree.js';

export default {

    components: {
        skillscategorytree: skillscategorytree
    },
    setup() {

        const { onMounted, ref } = Vue;
        const routeValue = Vue.$router.currentRoute._value;
        const params = routeValue.params;
        let id = params.id || 'new';
        const entry = ref({});        
        const state = ref({});
 
        onMounted(() => {
            useLoadPermissions();
            loadEntry();
        })

        function loadEntry() {
        let data = {};
        let ajaxUrl = modx_options.rest_url + 'Names/' + id;
        if (routeValue.name == 'memberform_create') {
            return;
        }

        axios.get(ajaxUrl, { params: data })
            .then(function (response) {
            const object = response.data.object;
            entry.value = object;
            
            })
            .catch(function (error) {
            console.log(error);
            });
        }        

        
        return {
            state,
            useHasPermission,
            entry,
            modx_options
        }
    },

    template: '#skills'
}