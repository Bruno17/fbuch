import api_select from '../../components/api_select.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        api_select: api_select
    },
    setup() {

        const {onMounted, ref, watch } = Vue;
        const entry = ref({});
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const messageCount = ref(0);
  
        onMounted(() => {
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadEvent();
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
                    urls.value.fahrtenbuch = 'fahrtenbuch/fahrtenbuch.html/#/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD')
                    urls.value.dayevents = 'termine/kalenderansicht.html/#/events/day/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD')
                    prepareEvent();
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




        return {
            entry,
            currentUser,
            useHasPermission,
            urls,
            messageCount
        }
    },

    template: '#terminanmeldung'
}