import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js'

export default {

    components: {
        valuesetter: valuesetter
    },
    setup() {

        const { useQuasar,LocalStorage,SessionStorage } = Quasar; 
        const { onMounted, ref } = Vue;
        const comments = ref([]);
        const state = ref({});

        onMounted(() => {
            SessionStorage.set('last_href', window.location.href);
            useLoadPermissions();
            loadBootcomments();
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
            state,
            useHasPermission 
        }
    },

    template: '#view'
}