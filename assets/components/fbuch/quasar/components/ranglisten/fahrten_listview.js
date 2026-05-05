import entry from '../../components/fahrtenbuch/entry.js';
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import monthrange_select from '../../components/monthrange_select.js';
import datepicker from '../../components/datepicker_simple.js';

export default {

    props: {
        state: {},
        member: {}
    },
    components: {
        entry: entry,
        datepicker: datepicker,
        monthrange_select: monthrange_select     
    },

    setup(props) {

        const {onMounted, ref, watch } = Vue;
        const state = props.state;
        const member = props.member;
        const fahrten = ref([]);

        onMounted(() => {
            useLoadPermissions();
            initFilterselects();
        }) 

        function initFilterselects(){
            const newDate = new Date(); 
            state.start_date = Quasar.date.formatDate(newDate, 'YYYY-01-01');
            state.end_date = Quasar.date.formatDate(newDate, 'YYYY-12-31');
            state.monthrange = { "value": 12, "label": "1 Jahr" }
        } 
        
        function onSelectStartdate(){
            const monthrange = state.monthrange.value;
            let end_date = Quasar.date.addToDate(state.start_date,{month:monthrange});
            end_date = Quasar.date.subtractFromDate(end_date,{day:1});
            state.end_date = Quasar.date.formatDate(end_date,'YYYY-MM-DD');
            /*
            const days_total = Quasar.date.getDateDiff(state.value.end_date, state.value.start_date , 'days'); 
            if (days_total < 0) {
                state.value.end_date = state.value.start_date;    
            }
            */
            loadFahrten();
        }        

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
        
        function loadFahrten() {
            fahrten.value = [];
            const ajaxUrl = modx_options.rest_url + 'Fahrten/';
            const data = {};
            data.returntype = 'member_fahrten';
            data.dir = 'DESC';
            data.start_date = state.start_date + ' 00:00:00';
            data.end_date = state.end_date + ' 23:59:59';
            data.gattung = state.gattung;
            data.group = state.group;
            data.querytype = state.querytype;
            data.member_id = member.id;            
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
            member,
            fahrten,
            loadFahrten,
            useHasPermission,
            onSelectStartdate
        }
    },
    template: '#fahrten_listview'
    // or `template: '#my-template-element'`
}