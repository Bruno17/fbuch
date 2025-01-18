import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js';
import api_select from '../../components/api_select.js';

export default {

    components: {
        valuesetter: valuesetter,
        api_select: api_select,
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const boatlist = ref([]);
        const params = Vue.$router.currentRoute._value.params;
        const columns = [
            { name: 'id',field: 'id'},
            { name: 'name', label: 'Name', field: 'name', sortable: true, align:'left' },
            { name: 'Nutzergruppe_name', label: 'Klassifizierung', field: 'Nutzergruppe_name', sortable: true, align:'left' },            
            { name: 'gewichtsklasse', label: 'Gewichtsklasse', field: 'gewichtsklasse', sortable: true, align:'left' },
            { name: 'Bootsgattung_shortname', label: 'Gattung', field: 'Bootsgattung_shortname', sortable: true, align:'left' },           
            { name: 'Aktionen', label: 'Aktionen', field: 'Aktionen', sortable: false }
        ]
        const visible_columns = ref(['name','gewichtsklasse','Nutzergruppe_name','Bootsgattung_shortname','Aktionen']);

        const initial_pagination = {
            rowsPerPage: 200
        }        

        onMounted(() => {
            useLoadPermissions();
            state.value.bootsgattung = parseInt(params.gattung_id) || '0';  
            onSelectBootsgattung();            
        })

        function onSelectBootsgattung(value) {
            if (state.value.bootsgattung == '0'){
                boatlist.value = [];
                return;
            }
            Vue.$router.push('/' + state.value.bootsgattung);             
            loadBoatList();
        }        

        function loadBoatList(){
            var data = {'gattung_id':state.value.bootsgattung};

            var ajaxUrl = modx_options.rest_url + 'Boote/' ;
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                boatlist.value = response.data.results;
            })
            .catch(function (error) {
                console.log(error);
            });            
        }


        return {
            state,
            boatlist,
            visible_columns,
            columns,
            initial_pagination,
            useHasPermission,
            onSelectBootsgattung 
        }
    },

    template: '#view'
}