import entry from '../../components/fahrtenbuch/entry.js';
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import year_select from '../../components/year_select.js';


export default {

    components: {
        entry: entry,
        year_select: year_select 
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const mailinglists = ref([]);
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new'; 
        const fahrten = ref({}); 
        const state = ref({});

        onMounted(() => {
            useLoadPermissions();
            initFilterselects();
            loadFahrten();
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

        function initFilterselects(){
            const newDate = new Date(); 
            state.value.start_date = Quasar.date.formatDate(newDate, 'YYYY-01-01');
            state.value.end_date = Quasar.date.formatDate(newDate, 'YYYY-12-31');
            state.value.querytype = 'boats';
            state.value.group = 'alle';
            state.value.gattung = 'Ruderboot';
            state.value.monthrange = { "value": 12, "label": "1 Jahr" }
            state.value.year = parseInt(Quasar.date.formatDate(newDate, 'YYYY'));
        } 

        function onSelectYear(){
            const newDate = new Date(String(state.value.year)); 
            state.value.start_date = Quasar.date.formatDate(newDate, 'YYYY-01-01');
            state.value.end_date = Quasar.date.formatDate(newDate, 'YYYY-12-31');
            loadFahrten();            
        }
        
        function loadFahrten() {
            fahrten.value = [];
            const ajaxUrl = modx_options.rest_url + 'Fahrten/';
            const data = {};
            data.returntype = 'member_fahrten';
            data.start_date = state.value.start_date + ' 00:00:00';
            data.end_date = state.value.end_date + ' 23:59:59';
            data.gattung = state.value.gattung;
            data.group = state.value.group;
            data.querytype = state.value.querytype;
            data.member_id = id;            
            return axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const result = response.data.results;
                    fahrten.value = prepareEvents(result);
                })
                .catch(function (error) {
                    console.log(error);
                });
        } 

        return {
            state,
            fahrten,
            useHasPermission,
            onSelectYear
        }
    },

    template: '#fahrten'
}