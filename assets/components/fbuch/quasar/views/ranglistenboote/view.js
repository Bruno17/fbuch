import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import { useCreateTable,useCopyTable } from "../../composables/tableHelpers.js";
import valuesetter from '../../components/valuesetter.js';
import api_select from '../../components/api_select.js';
import monthrange_select from '../../components/monthrange_select.js';
import datepicker from '../../components/datepicker_simple.js';
import fahrten_listview from '../../components/ranglisten/fahrten_listview.js';

export default {

    components: {
        valuesetter: valuesetter,
        api_select: api_select,
        datepicker: datepicker,
        monthrange_select: monthrange_select,
        fahrten_listview: fahrten_listview   
    },
    setup() {

        const { onMounted, ref } = Vue;
        const mm_expanded = ref(false);
        const state = ref({});
        const rangliste = ref([]);
        const km_sum = ref(0);
        const current_member = ref({});
        const columns = [
            { name: 'Rang', label: 'Rang', field: 'Rang', sortable: true, style:'width: 20px;' },
            { name: 'id',label: 'id', field: 'id', sortable: true},            
            { name: 'Name', label: 'Name', field: 'Name', sortable: true },
            { name: 'km', label: 'km', field: 'km', sortable: true },
            { name: 'Fahrten', label: 'Fahrten', field: 'Fahrten', sortable: true },
            { name: 'Aktionen', label: 'Aktionen', field: 'Aktionen', sortable: false }
        ]
        const visible_columns = ref(['Rang','id','Name','km','Fahrten','Aktionen']);
        const export_columns = ref(['Rang','id','Name','km','Fahrten']);

        const initial_pagination = {
            rowsPerPage: 200
        }

        onMounted(() => {
            useLoadPermissions();
            initFilterselects();
            loadRangliste();
        })

        function initFilterselects(){
            const newDate = new Date(); 
            state.value.start_date = Quasar.date.formatDate(newDate, 'YYYY-01-01');
            state.value.end_date = Quasar.date.formatDate(newDate, 'YYYY-12-31');
            state.value.group = 'alle';
            state.value.gattung = 'Ruderboot';
            state.value.monthrange = { "value": 12, "label": "1 Jahr" }
        }

        function onSelectGroup(){
            loadRangliste();    
        }

        function onSelectStartdate(){
            const monthrange = state.value.monthrange.value;
            let end_date = Quasar.date.addToDate(state.value.start_date,{month:monthrange});
            end_date = Quasar.date.subtractFromDate(end_date,{day:1});
            state.value.end_date = Quasar.date.formatDate(end_date,'YYYY-MM-DD');
            /*
            const days_total = Quasar.date.getDateDiff(state.value.end_date, state.value.start_date , 'days'); 
            if (days_total < 0) {
                state.value.end_date = state.value.start_date;    
            }
            */
            loadRangliste();
        }

        function showFahrten(row){
            current_member.value.id=row.id;
            current_member.value.Vorname=row.Vorname;
            current_member.value.Nachname=row.Nachname;
            state.value.querytype='boats';
            state.value.showfahrten=true;            
        }

        function loadRangliste(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Ranglisten';
            data.start_date = state.value.start_date;
            data.end_date = state.value.end_date;
            data.group = state.value.group;
            data.gattung = state.value.gattung;
            data.querytype = 'boats';
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                rangliste.value = response.data.results;
                km_sum.value = response.data.km_sum;
            })
            .catch(function (error) {
                console.log(error);
            });            
        }


          
        function copyData() {
            var table = useCreateTable(rangliste.value,export_columns.value);
            useCopyTable(table);
        }


        return {
            mm_expanded,
            state,
            useHasPermission,
            onSelectStartdate,
            onSelectGroup, 
            showFahrten,
            copyData,           
            rangliste,
            columns,
            visible_columns,
            initial_pagination,
            km_sum,
            current_member
        }
    },

    template: '#view'
}