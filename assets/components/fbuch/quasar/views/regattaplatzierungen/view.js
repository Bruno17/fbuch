import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js';
import year_select from '../../components/year_select.js';
import dayevent from '../../components/dayevent.js';
import fahrten_listview from '../../components/ranglisten/fahrten_listview.js';

export default {

    components: {
        valuesetter: valuesetter,
        year_select: year_select,
        dayevent: dayevent,
        fahrten_listview: fahrten_listview,
        
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const summaries = ref([]);
 
        onMounted(() => {
            const newDate = new Date(); 
            useLoadPermissions();
            state.value.resource_alias = modx_options.resource_alias;
            state.value.year = parseInt(Quasar.date.formatDate(newDate, 'YYYY'));
            loadEvents();
        })

        function loadEvents(){

            const data = {};
            data.start = state.value.year + '/01/01 00:00:00';
            data.end = state.value.year + '/12/31 23:59:59';
            data.types = modx_options.datetype;

            const ajaxUrl = modx_options.rest_url + 'RegattaRankings';

            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
            summaries.value = response.data.results;    
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function onSelectYear(){
            loadEvents();            
        }

        function showFahrten(fahrt_ids){
            state.value.fahrt_ids=fahrt_ids;
            state.value.returntype='fahrt_ids';
            //current_member.value.Vorname=row.firstname;
            //current_member.value.Nachname=row.name;
            state.value.showfahrten=true; 
            state.value.showfilter=false; 
            //state.value.querytype='allemembereintraege';            
        }        

        return {
            state,
            useHasPermission,
            onSelectYear,
            loadEvents,
            showFahrten,
            summaries
        }
    },

    template: '#view'
}