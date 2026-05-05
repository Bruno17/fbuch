import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js';
import api_select from '../../components/api_select.js';
import fahrten_listview from '../../components/ranglisten/fahrten_listview.js';

export default {

    components: {
        valuesetter: valuesetter,
        api_select: api_select,
        fahrten_listview: fahrten_listview
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const names = ref([]);
        const current_member = ref({});
        const columns = [
            { name: 'id',field: 'id'},
            { name: 'CompetencyLevel_level', label: 'Kompetenzstufe', field: 'CompetencyLevel_level', sortable: true, align:'left' },
            { name: 'name', label: 'Nachname', field: 'name', sortable: true, align:'left' },
            { name: 'firstname', label: 'Vorname', field: 'firstname', sortable: true, align:'left' },
            { name: 'member_status', label: 'Status', field: 'member_status', sortable: true, align:'left' },
            { name: 'riot_user_id', label: 'Element ID', field: 'riot_user_id', sortable: true, align:'left' },
            { name: 'Jahrgang', label: 'Jahrgang', field: 'Jahrgang', sortable: true, align:'left'  },
            { name: 'Aktionen', label: 'Aktionen', field: 'Aktionen', sortable: false }
        ]
        const visible_columns = ref(['CompetencyLevel_level','name','firstname','member_status','riot_user_id','Aktionen']);

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

        function showFahrten(row){
            current_member.value.id=row.id;
            current_member.value.Vorname=row.firstname;
            current_member.value.Nachname=row.name;
            state.value.showfahrten=true; 
            state.value.showfilter=true; 
            state.value.querytype='allemembereintraege';            
        }        

        function loadNames(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Names';
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                names.value = response.data.results;
                visible_columns.value = ['CompetencyLevel_level','name','firstname','member_status','riot_user_id'];
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
            showFahrten, 
            current_member,
            names,
            columns,
            visible_columns,
            initial_pagination,
            Quasar,
            modx_options
        }
    },

    template: '#view'
}