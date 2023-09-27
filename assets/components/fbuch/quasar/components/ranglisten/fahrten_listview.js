import entry from '../../components/fahrtenbuch/entry.js';
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    props: {
        state: {},
        member: {}
    },
    components: {
        entry: entry
    },

    setup(props) {

        const {onMounted, ref, watch } = Vue;
        const state = props.state;
        const member = props.member;
        const fahrten = ref({});

        onMounted(() => {
            useLoadPermissions();
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
        
        function loadFahrten() {
            fahrten.value = [];
            const ajaxUrl = modx_options.rest_url + 'Fahrten/';
            const data = {};
            data.returntype = 'member_fahrten';
            data.start_date = state.start_date + ' 00:00:00';
            data.end_date = state.end_date + ' 23:59:59';
            data.gattung = state.gattung;
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
            useHasPermission
        }
    },
    template: '#fahrten_listview'
    // or `template: '#my-template-element'`
}