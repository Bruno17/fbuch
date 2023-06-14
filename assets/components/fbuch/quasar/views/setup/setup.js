import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {

    },
    setup() {

        const { onMounted, ref } = Vue;
        const tab = ref('acls');

        onMounted(() => {
            useLoadPermissions();
        })

        function setupAcls(){
            const ajaxUrl = modx_options.rest_url + 'setup/Acls';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function prefill(classname){
            const ajaxUrl = modx_options.rest_url + 'setup/PrefillTable';
            axios.post(ajaxUrl,{'classname':classname})
            .then(function (response) {
 
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        return {
            tab,
            setupAcls,
            prefill
        }
    },

    template: '#setup-view'
}