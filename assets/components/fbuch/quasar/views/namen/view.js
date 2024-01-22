import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js';
import api_select from '../../components/api_select.js';

export default {

    components: {
        valuesetter: valuesetter,
        api_select: api_select
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const names = ref([]);
        const columns = [
            { name: 'id',field: 'id'},
            { name: 'CompetencyLevel_level', label: 'Kompetenzstufe', field: 'CompetencyLevel_level', sortable: true, align:'left' },
            { name: 'name', label: 'Nachname', field: 'name', sortable: true, align:'left' },
            { name: 'firstname', label: 'Vorname', field: 'firstname', sortable: true, align:'left' },
            { name: 'riot_user_id', label: 'Element ID', field: 'riot_user_id', sortable: true, align:'left' },
            { name: 'Jahrgang', label: 'Jahrgang', field: 'Jahrgang', sortable: true, align:'left'  },
            { name: 'Aktionen', label: 'Aktionen', field: 'Aktionen', sortable: false }
        ]
        const visible_columns = ref(['CompetencyLevel_level','name','firstname','riot_user_id','Aktionen']);

        const initial_pagination = {
            rowsPerPage: 200
        }

        onMounted(() => {
            useLoadPermissions();
            loadNames();
        })
 
        function onSelectGroup(){
            loadNames();    
        }

        function loadNames(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Names';
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                names.value = response.data.results;
                visible_columns.value = ['CompetencyLevel_level','name','firstname','riot_user_id'];
                if (useHasPermission('fbuch_view_birthdate')){
                    visible_columns.value.push('Jahrgang');
                }
                visible_columns.value.push('Aktionen');                
            })
            .catch(function (error) {
                console.log(error);
            });            
        }


        return {
            filter: ref(''),
            state,
            useHasPermission,
            onSelectGroup, 
            names,
            columns,
            visible_columns,
            initial_pagination,
            Quasar
        }
    },

    template: '#view'
}