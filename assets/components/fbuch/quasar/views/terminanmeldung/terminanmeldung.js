import personslist from '../../components/personslist-editor.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        personslist: personslist
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const entry = ref({});
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';

        onMounted(() => {
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadEvent();
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
                    prepareEvent();
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


        return {
            entry,
            currentUser,
            onUpdateNames,
            RemoveMyself,
            addMyself,
            useHasPermission,
            urls
        }
    },

    template: '#terminanmeldung'
}