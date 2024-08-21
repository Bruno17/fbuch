import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";


export default {

    components: {
 
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const { useQuasar,LocalStorage,SessionStorage } = Quasar;
        const comments = ref([]);
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const state = ref({});        

        onMounted(() => {
            useLoadPermissions();
            loadBootcomments();
            SessionStorage.set('last_href', window.location.href);
            state.value.show_done = 0; 
        })

        function prepareComments(comments) {
            const preparedEvents = [];
            comments.forEach((comment, id) => {
                comment['formattedDate'] = Quasar.date.formatDate(comment.createdon, 'DD.MM.YYYY');
                comment['formattedDoneon'] = Quasar.date.formatDate(comment.doneon, 'DD.MM.YYYY');
                preparedEvents.push(comment);
            })
            return preparedEvents;
        }        

        function loadBootcomments(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Bootcomments';

            data.hide_done = state.value.show_done == 1 ? 0 : 1;
            data.boot_id = id; 

            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                const result = response.data.results;
                comments.value = prepareComments(result);                
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        return {
            comments,
            useHasPermission,
            loadBootcomments,
            id,
            state
        }
    },

    template: '#schaeden'
}