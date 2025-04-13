import api_select from '../../components/api_select_multiple.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        api_select: api_select
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const invites = ref([]);
        const addedinvites = ref([]);
        const removedinvites = ref([]);
        const state = ref({});
        const personSelect = ref();
        const mailvalues = ref({});

        onMounted(() => {
            resetState();
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadInvited();
        })

        function resetState() {
            state.value = {};
            state.value.persons = [];
            mailvalues.value = {
                skip_accepted:true,
                skip_canceled:true
            };
        }

        function onSelectPerson(value) {
            personSelect.value.hidePopup();
            return;
        }
        
        function onHideDialog() {
            resetState();
        } 
        
        function loadInvited(){
            loadDefault();
            loadAdded();
            loadRemoved();
        }

        function loadDefault() {
            const data = {};
            data.date_id = id;
            data.added = 0;
            data.removed = 0;
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    invites.value = results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        } 

        function loadAdded() {
            const data = {};
            data.date_id = id;
            data.added = 1;
            data.removed = 0;
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    addedinvites.value = results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }   
        
        function loadRemoved() {
            const data = {};
            data.date_id = id;
            data.added = 0;
            data.removed = 1;
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    removedinvites.value = results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        } 

        function sendInvites(){
            let properties = mailvalues.value;
            properties.processaction = 'invite';
            properties.date_id = id;            
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.post(ajaxUrl, properties)
                .then(function (response) {
                    resetState();
                    //loadInvited();
                })
                .catch(function (error) {
                    console.log(error);
                });            
        }
        

        function addPersons(){
            let properties = {};
            properties.persons = state.value.persons;
            properties.processaction = 'add';
            properties.date_id = id;            
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.post(ajaxUrl, properties)
                .then(function (response) {
                    resetState();
                    loadInvited();
                })
                .catch(function (error) {
                    console.log(error);
                });            
        }

        function readdPerson(name){
            const person = {'id':name.member_id};
            state.value.persons = [];
            state.value.persons.push(person);
            addPersons();        
        }

        function removePerson(person){
            let properties = {};
            let persons = [];
            persons.push(person);
            properties.persons = persons;
            properties.processaction = 'remove';
            properties.date_id = id;            
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.post(ajaxUrl, properties)
                .then(function (response) {
                    resetState();
                    loadInvited();
                })
                .catch(function (error) {
                    console.log(error);
                });            
        }        

        return {
            invites,
            addedinvites,
            removedinvites,
            currentUser,
            useHasPermission,
            onSelectPerson,
            onHideDialog,
            addPersons,
            removePerson,
            readdPerson,
            sendInvites,
            personSelect,
            urls,
            state,
            mailvalues
        }
    },

    template: '#einladungen'
}