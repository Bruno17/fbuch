import api_select from '../../components/api_select_multiple.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        api_select: api_select
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const entry = ref({});
        const message = ref({'comment':''});
        const messages = ref([]);
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const mail_dialog = ref(false);
        const selectionState = ref({});
        const sendtype = ref('comment_only');
        const mail_expanded = ref(false);

        onMounted(() => {
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadEvent();
            loadMessages();
            selectionState.value.person=[];
            selectionState.value.invited=[];
            selectionState.value.allinvited=false;
            selectionState.value.allpersons=false;
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

        function loadMessages() {
            const data = {};
            data.date_id = id;
            const ajaxUrl = modx_options.rest_url + 'Datecomments';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    messages.value = results; 
                })
                .catch(function (error) {
                    console.log(error);
                });
        }        

        function addMessage() {
            const ajaxUrl = modx_options.rest_url + 'Datecomments';
            let properties = {};
            if (message.value.comment == ''){
                return;
            }
            properties.comment = message.value.comment;
            properties.date_id = entry.value.id;
            axios.post(ajaxUrl, properties)
                .then(function (response) {
                    message.value.comment = '';
                    loadMessages();
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function onClickSendMail(){
            mail_dialog.value = true;    
        }

        function onChangeSendType(){
            if (sendtype.value != 'comment_only'){
                mail_expanded.value = true;
            } else {
                mail_expanded.value = false;
            }
            console.log('onChangeSendType');
        }

         return {
            entry,
            message,
            currentUser,
            messages,
            mail_dialog,
            addMessage,
            useHasPermission,
            onClickSendMail,
            urls,
            selectionState,
            sendtype,
            mail_expanded,
            onChangeSendType
        }
    },

    template: '#eventcomments'
}