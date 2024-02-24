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
        const { useQuasar,LocalStorage,SessionStorage } = Quasar;
        const $q = useQuasar();          
        const selectedDate = ref(getDateFromRoute())
        const formattedDate = Quasar.date.formatDate(selectedDate.value, 'dd, DD.MM.YYYY');
        const urlDate = Quasar.date.formatDate(selectedDate.value, 'YYYY/MM/DD');
        const sheduled = ref([]);
        const open = ref([]);
        const finished = ref([]);
        const loadedEvents = ref([]);
        const mm_expanded = ref(false);
        const gattungnames = ref([]);
        const showall_dates = ref(SessionStorage.getItem('showall_dates') || false);

        onMounted(() => {
            useLoadPermissions();
            loadAll();
            loadGattungnames();
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

        function loadGattungnames() {
            const ajaxUrl = modx_options.rest_url + 'Boote?limit=100000&returntype=gattungnames';
            return axios.get(ajaxUrl)
                .then(function (response) {
                    gattungnames.value = response.data.results;
                })
                .catch(function (error) {
                    console.log(error);
                });
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

        function loadEventsToday() {
            const data = {};
            data.start_date = selectedDate.value + ' 00:00:00';
            data.end_date = selectedDate.value + ' 23:59:59';
            loadEvents(data).then(function (result) {
                loadedEvents.value = prepareEvents(result);
            });
        }

        function getSelectedEventNames(fields=[],eventfields=[]){
            let names = [];
            loadedEvents.value.forEach((event,id) => {
                event.names.forEach((name,name_id) => {
                    let item = {};
                    if (name.selected) {
                        if (eventfields.length > 0){
                            eventfields.forEach((field) => {
                                item['Date_' + field] = event[field];
                            })
                        }                        
                        if (fields.length > 0){
                            fields.forEach((field) => {
                                item[field] = name[field];
                            })
                        } else {
                            item = name;
                        }
                        names.push(item);
                    }
                })
            })
            return names;
        }

        function getSelectedFahrtNames(fields=[],fahrtfields=[]){
            let names = [];
            open.value.forEach((fahrt,id) => {
                fahrt.names.forEach((name,name_id) => {
                    //name.selected = false;
                    let item = {};
                    if (name.selected) {
                        if (fahrtfields.length > 0){
                            fahrtfields.forEach((field) => {
                                item['Fahrt_' + field] = fahrt[field];
                            })
                        }                         
                        if (fields.length > 0){
                            fields.forEach((field) => {
                                item[field] = name[field];
                            })
                        } else {
                            item = name;
                        }
                        names.push(item);
                    }
                })
            })
            sheduled.value.forEach((fahrt,id) => {
                fahrt.names.forEach((name,name_id) => {
                    //name.selected = false;
                    let item = {};
                    if (name.selected) {
                        if (fahrtfields.length > 0){
                            fahrtfields.forEach((field) => {
                                item['Fahrt_' + field] = fahrt[field];
                            })
                        }                         
                        if (fields.length > 0){
                            fields.forEach((field) => {
                                item[field] = name[field];
                            })
                        } else {
                            item = name;
                        }
                        names.push(item);
                    }
                })
            })            
            return names;
        }

        function onNameCheckbox(name,from){
            let excludeFahrt = 0;
            let excludeEvent = 0;
            if (from == 'fahrt'){
                excludeFahrt = name.fahrt_id;
            }
            if (from == 'date'){
                excludeEvent = name.date_id;
            }            

            uncheckEventsNames(excludeEvent);
            uncheckFahrtenNames(excludeFahrt);
        }

        function uncheckEventsNames(exclude){
            for (let i = 0; i < loadedEvents.value.length; i++) {
                 if (loadedEvents.value[i].id != exclude){
                     uncheckNames(loadedEvents.value[i].names);   
                 }
            }
        } 
        function uncheckFahrtenNames(exclude){

            for (let i = 0; i < open.value.length; i++) {
                if (open.value[i].id != exclude){
                    uncheckNames(open.value[i].names);
                }                
                 
            }
            for (let i = 0; i < sheduled.value.length; i++) {
                if (sheduled.value[i].id != exclude){
                    uncheckNames(sheduled.value[i].names);
                }                
                
           }            
        }
        function uncheckNames(names){
            for (let i = 0; i < names.length; i++) {
                names[i].selected = false;
            }
        }

 

        function moveMembers(properties){
            let hasSelected = false;
            let sourcedate = '';
            let message = '';
            const targetdate = Quasar.date.formatDate(properties.date, 'DD.MM.YYYY');
            const eventNames = getSelectedEventNames(['id','date_id','member_id'],['date']);
            const fahrtNames = getSelectedFahrtNames(['id','fahrt_id','member_id'],['date']);
            if (eventNames.length > 0){
                hasSelected = true;
                properties['source'] = 'dates';
                properties['names'] = eventNames;
                sourcedate = Quasar.date.formatDate(eventNames[0].Date_date, 'DD.MM.YYYY');
            }
            if (fahrtNames.length > 0){
                hasSelected = true;
                properties['source'] = 'fahrten';
                properties['names'] = fahrtNames;
                sourcedate = Quasar.date.formatDate(fahrtNames[0].Fahrt_date, 'DD.MM.YYYY');
            }            
            if (!hasSelected) {
                message = 'Es wurden keine Personen zum Einfügen markiert';
                $q.dialog({
                    title: 'Warnung!',
                    message: message,
                    html:true
                  })                
                return;
            }
            
            if (sourcedate != targetdate){
                message = 'Das Startdatum des Ursprungs ist nicht identisch mit dem Startdatum des Ziels';
                message += '<br>Ursprung: ' + sourcedate;
                message += '<br>Ziel: ' + targetdate;
                $q.dialog({
                    title: 'Warnung!',
                    message: message,
                    html:true
                  })
                return;
            }

            properties['processaction'] = 'moveNames'; 
            const ajaxUrl = modx_options.rest_url + 'Fahrtnames';
            axios.post(ajaxUrl,properties)
            .then(function (response) {
                loadAll();
            })
            .catch(function (error) {
                console.log(error);
            });             
            
        }

        function loadEvents(data) {
            data.which_page = 'rowinglogbook';
            data.showall_dates = showall_dates.value;

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

        function onChangeDatetypes(){
            SessionStorage.set('showall_dates', showall_dates.value)            
            loadEventsToday();
        }

        return {
            selectedDate,
            formattedDate,
            finished,
            open,
            sheduled,
            loadedEvents,
            urlDate,
            mm_expanded,
            gattungnames,
            showall_dates,
            loadAll,
            loadEventsToday,
            moveMembers,
            getSelectedEventNames,
            onNameCheckbox,
            onNext,
            onPrev,
            onToday,
            useHasPermission,
            onChangeDatetypes
        }
    },

    template: '#fahrtenbuch-view'
}