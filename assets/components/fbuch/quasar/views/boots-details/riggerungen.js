import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";


export default {

    components: {
 
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const { useQuasar,LocalStorage,SessionStorage } = Quasar;
        const entries = ref([]);
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const state = ref({});        

        onMounted(() => {
            useLoadPermissions();
            loadEntries();
            SessionStorage.set('last_href', window.location.href);
        })

        function prepareEntries(entries) {
            const preparedEvents = [];
            entries.forEach((entry, id) => {
                entry['formattedDate'] = Quasar.date.formatDate(entry.createdon, 'DD.MM.YYYY');
                preparedEvents.push(entry);
            })
            return preparedEvents;
        }        

        function loadEntries(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Bootriggerungen';

            data.boot_id = id; 

            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                const result = response.data.results;
                entries.value = prepareEntries(result);                
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        return {
            entries,
            useHasPermission,
            loadEntries,
            id,
            state
        }
    },

    template: '#riggerungen'
}