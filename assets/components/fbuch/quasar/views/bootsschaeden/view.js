import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js'

export default {

    components: {
        valuesetter: valuesetter
    },
    setup() {

        const { exportFile,useQuasar,LocalStorage,SessionStorage } = Quasar; 
        const { onMounted, ref } = Vue;
        const comments = ref([]);
        const state = ref({});
        const export_items = ref([]);
        const export_columns = ref(['formattedDate','Boot_name','comment','name']);

        onMounted(() => {
            SessionStorage.set('last_href', window.location.href);
            useLoadPermissions();
            loadBootcomments();
        })

        function prepareComments(comments) {
            const preparedEvents = [];
            export_items.value = [];
            comments.forEach((comment, id) => {
                comment['formattedDate'] = Quasar.date.formatDate(comment.createdon, 'DD.MM.YYYY');
                comment['formattedDoneon'] = Quasar.date.formatDate(comment.doneon, 'DD.MM.YYYY');
                preparedEvents.push(comment);
                export_items.value.push(comment);
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

        function exportCsv () {
            
            const content = 
            export_items.value.map(row => export_columns.value.map(col => '"'+row[col]+'"').join(',')).join('\r\n');
            const status = exportFile(
              'Bootsschaeden.csv',
              content,
              'text/csv'
            );
    
            if (status !== true) {
              $q.notify({
                message: 'Browser denied file download...',
                color: 'negative',
                icon: 'warning'
              })
            }
          }             

        return {
            comments,
            state,
            exportCsv,
            useHasPermission 
        }
    },

    template: '#view'
}