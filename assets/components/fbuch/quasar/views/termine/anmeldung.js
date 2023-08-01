import api_select from '../../components/api_select.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        api_select: api_select
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const entry = ref({});
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const selectionState = ref({});
        const personSelect = ref();
        const newguest = ref({});
        const messageCount = ref(0);
        const abmeldungen = ref([]);

        onMounted(() => {
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadEvent();
            loadInvited();
            loadMessageCount();
        })

        function prepareEvent() {
            entry.value['formattedDate'] = Quasar.date.formatDate(entry.value.date, 'dd DD.MM.YYYY ' + entry.value.start_time + ' - ');
            if (Quasar.date.isSameDate(entry.value.date, entry.value.date_end, 'days')) {
                entry.value['formattedDate'] += entry.value.end_time;
                entry.value['formattedEndDate'] = '';
            } else {
                entry.value['formattedEndDate'] = Quasar.date.formatDate(entry.value.date_end, 'dd DD.MM.YYYY ' + entry.value.end_time);
            }
        }

        function loadEvent() {
            const data = {};
            data.returntype = 'selfregistered_names'
            const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const object = response.data.object;
                    entry.value = object;
                    urls.value.fahrtenbuch = '/fahrtenbuch/fahrtenbuch.html/#/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD')
                    urls.value.dayevents = '/termine/kalenderansicht.html/#/events/day/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD')
                    console.log(urls);
                    prepareEvent();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function loadInvited() {
            const data = {};
            data.date_id = id;
            data.canceled = 1;
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    abmeldungen.value = results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }        

        function loadMessageCount() {
            const data = {};
            data.date_id = id;
            data.returntype = 'count';
            const ajaxUrl = modx_options.rest_url + 'Datecomments';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const total = response.data.total;
                    messageCount.value = total;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }        

        function onUpdateNames(action, value) {
            if (action == 'remove') {
                removeName(value);
                return;
            }

            const member_id = value.id || 0;
            const guestname = value.guestname || '';
            const guestemail = value.guestemail || '';
            let names = [];
            let guestnames = [];
            let guestemails = [];
            let properties = {};
            if (member_id > 0) {
                names.push(member_id);
            }
            if (guestname != '') {
                guestnames.push(guestname);
                guestemails.push(guestemail);
            }
            properties.processaction = action;
            properties.person = names;
            properties.guestname = guestnames;
            properties.guestemail = guestemails;
            properties.date_id = id;
            postDateNames(properties);
        }

        function addMyself() {
            const member_id = currentUser.value.Member_id || 0;
            let names = [];
            let properties = {};
            if (member_id > 0) {
                names.push(member_id);
            }
            properties.processaction = 'add';
            properties.person = names;
            properties.date_id = id;
            postDateNames(properties);
        }

        function RemoveMyself() {
            let properties = {};
            const member_id = currentUser.value.Member_id || 0;
            if (member_id > 0) {
                properties.processaction = 'remove';
                properties.person = member_id;
                properties.date_id = id;
                postDateNames(properties);
            }
        }

        function postDateNames(properties) {
            const ajaxUrl = modx_options.rest_url + 'Datenames';
            axios.post(ajaxUrl, properties)
                .then(function (response) {
                    loadEvent();
                    loadInvited();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function removeName(name) {
            console.log(name);
            const person = name.id || false;
            const datename_id = name.datename_id || false;
            let properties = {};
            if (person || datename_id) {
                properties.processaction = 'remove';
                properties.person = person;
                properties.datename_id = datename_id;
                properties.date_id = id;
                postDateNames(properties);
            }
        }

        function onSelectPerson(value) {
            if (!entry.names) {
                entry.names = [];
            }
            const id = value.value;
            const exists = findPerson(id);
            selectionState.value.person = 0;
            personSelect.value.clearSelection();
            if (exists) {
                return;
            }
            //props.entry.names.push(value);
            //emit('updateNames','add',value);
            onUpdateNames('add', value);

        } 
        
        function findPerson(id) {
            let result = false;
            entry.value.names.forEach((person) => {
                if (person.value == id) {
                    result = person;
                }
            })
            return result;
        } 

        function addGuest() {
            const value = {
                guestname: newguest.value.name || '',
                guestemail: newguest.value.email || '',
                member_status: 'Gasteintrag'
            }
            if (value.name != '') {
                if (!entry.names) {
                    entry.names = [];
                }
                //props.entry.names.push(value);
                onUpdateNames('add',value);
            }
            newguest.value = {};
        }  
        
        function removePerson(index) {
            const name = entry.value.names[index];
            //entry.value.names.splice(index, 1);
            onUpdateNames('remove',name);
        }        


        return {
            abmeldungen,
            entry,
            currentUser,
            newguest,
            onUpdateNames,
            RemoveMyself,
            addMyself,
            useHasPermission,
            selectionState,
            urls,
            onSelectPerson,
            personSelect,
            addGuest,
            removePerson,
            messageCount
        }
    },

    template: '#anmeldung'
}