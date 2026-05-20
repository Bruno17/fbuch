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
        const events = ref([]);
 
        onMounted(() => {
            const newDate = new Date(); 
            useLoadPermissions();
            console.log('modx_options:',modx_options);
            state.value.year = parseInt(Quasar.date.formatDate(newDate, 'YYYY'));
            loadEvents();
        })

        function prepareEvents(events) {
            const preparedEvents = [];
            events.forEach((event, id) => {
                event['formattedDate'] = Quasar.date.formatDate(event.date, 'dd DD.MM.YYYY ' + event.start_time + ' - ');
                if (Quasar.date.isSameDate(event.date, event.date_end, 'days')) {
                    event['formattedDate'] += event.end_time;
                    event['formattedEndDate'] = '';
                } else {
                    event['formattedEndDate'] = Quasar.date.formatDate(event.date_end, 'dd DD.MM.YYYY ' + event.end_time);
                }
                preparedEvents.push(event);
            })
            return preparedEvents;
        } 

        function loadEvents(){

            const data = {};
            data.start = state.value.year + '/01/01 00:00:00';
            data.end = state.value.year + '/12/31 23:59:59';
            data.types = modx_options.datetype;

            const ajaxUrl = modx_options.rest_url + 'Dates';

            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
            events.value = prepareEvents(response.data.results);
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function onSelectYear(){
            loadEvents();            
        }

        function showFahrten(event){
            state.value.date_id=event.id;
            state.value.returntype='date_fahrten';
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
            events 
        }
    },

    template: '#view'
}