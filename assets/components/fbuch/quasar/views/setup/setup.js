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
                //event.value = response.data.object;
                //recurrences_dialog.value = false;
                //recurre.value.days = [];
                //loadEvents();
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        return {
            tab,
            setupAcls
        }
    },

    template: '#setup-view'
}