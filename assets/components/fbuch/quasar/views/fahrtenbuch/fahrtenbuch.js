import dayevent from '../../components/dayevent.js';
import entry from '../../components/fahrtenbuch/entry.js';
import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        dayevent: dayevent,
        entry: entry
    },
    setup() {

        const { onMounted, ref } = Vue;
        const selectedDate = ref(getDateFromRoute())
        const formattedDate = Quasar.date.formatDate(selectedDate.value, 'dd, DD.MM.YYYY');
        const sheduled = ref([]);
        const open = ref([]);
        const finished = ref([]);
        const loadedEvents = ref([]);

        onMounted(() => {
            useLoadPermissions();
            loadAll();
        })

        function getDateFromRoute() {
            let date = today();
            const params = Vue.$router.currentRoute._value.params;
            if (typeof params.year !== 'undefined' && typeof params.month !== 'undefined') {
                date = params.year + '-' + params.month + '-' + params.day
            }
            return date;
        }

        function today() {
            const timeStamp = Date.now()
            return Quasar.date.formatDate(timeStamp, 'YYYY-MM-DD')
        }

        function loadAll() {
            loadSheduled();
            loadOpen();
            loadFinished();
            loadEventsToday();
        }

        function loadSheduled() {
            const data = {};
            data.returntype = 'sheduled';
            loadFahrten(data).then(function (result) {
                sheduled.value = prepareEvents(result);
            });
        }
        function loadOpen() {
            const data = {};
            data.returntype = 'open';
            loadFahrten(data).then(function (result) {
                open.value = prepareEvents(result);
            });
        }
        function loadFinished() {
            const data = {};
            data.returntype = 'finished';
            data.start_date = selectedDate.value + ' 00:00:00';
            data.end_date = selectedDate.value + ' 23:59:59';
            loadFahrten(data).then(function (result) {
                finished.value = prepareEvents(result);
            });
        }

        function loadFahrten(data) {
            const ajaxUrl = modx_options.rest_url + 'Fahrten/';
            return axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    return response.data.results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function prepareEvents(events) {
            const preparedEvents = [];
            events.forEach((event, id) => {
                event['formattedDate'] = Quasar.date.formatDate(event.date, 'dd DD. MM. YYYY ' + event.start_time + ' - ');
                if (Quasar.date.isSameDate(event.date, event.date_end, 'days')) {
                    event['formattedDate'] += event.end_time;
                    event['formattedEndDate'] = '';
                } else {
                    event['formattedEndDate'] = Quasar.date.formatDate(event.date_end, 'dd DD. MM. YYYY ' + event.end_time);
                }
                preparedEvents.push(event);
            })
            return preparedEvents;
        }

        function loadEventsToday() {
            const data = {};
            data.start_date = selectedDate.value + ' 00:00:00';
            data.end_date = selectedDate.value + ' 23:59:59';
            loadEvents(data).then(function (result) {
                loadedEvents.value = prepareEvents(result);
            });
        }

        function getSelectedNames(){
            let names = [];
            loadedEvents.value.forEach((date,id) => {
                date.names.forEach((name,name_id) => {
                    if (name.selected) {
                       names.push(name);
                    }
                })
            })
            console.log(names);
        }

        function loadEvents(data) {
            data.which_page = 'rowinglogbook';
            const ajaxUrl = modx_options.rest_url + 'Dates';

            return axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    return response.data.results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function prepareRoute(date) {

            return {
                name: 'fahrtenbuch',
                params: {
                    year: Quasar.date.formatDate(date, 'YYYY'),
                    month: Quasar.date.formatDate(date, 'MM'),
                    day: Quasar.date.formatDate(date, 'DD')
                }
            }
        }
        function onPrev() {
            const timeStamp = Quasar.date.subtractFromDate(selectedDate.value, { days: 1 });
            Vue.$router.push(prepareRoute(timeStamp));
        }
        function onNext() {
            const timeStamp = Quasar.date.addToDate(selectedDate.value, { days: 1 });
            Vue.$router.push(prepareRoute(timeStamp));
        }
        function onToday() {
            const newDate = new Date;
            Vue.$router.push(prepareRoute(newDate));
        }

        return {
            selectedDate,
            formattedDate,
            finished,
            open,
            sheduled,
            loadedEvents,
            getSelectedNames,
            onNext,
            onPrev,
            onToday
        }
    },

    template: '#fahrtenbuch-view'
}