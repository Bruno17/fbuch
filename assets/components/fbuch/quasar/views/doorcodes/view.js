import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js';
import api_select from '../../components/api_select.js';
import { useCreateTable,useCopyTable } from "../../composables/tableHelpers.js";

export default {

    components: {
        valuesetter: valuesetter,
        api_select: api_select
    },
    setup() {

        const { onMounted, ref } = Vue;
        const { useQuasar,LocalStorage,SessionStorage } = Quasar;
        const $q = useQuasar();          
        const state = ref({});
        const names = ref([]);
        const filter = ref(SessionStorage.getItem('doorcodes_filter')) || ref('');
        const export_codes = ref([]);
        const columns = [
            { name: 'id',field: 'id'},
            { name: 'Code_time_setting', label: '', field: 'Code_time_setting', sortable: false, align:'left' }, 
            { name: 'Code_blocked', label: '', field: 'Code_blocked', sortable: false, align:'left' },           
            { name: 'code', label: 'Code', field: 'code', sortable: true, align:'left' },
            { name: 'Member_name', label: 'Name', field: 'Member_name', sortable: true, align:'left' },
            { name: 'Member_member_status', label: 'Status', field: 'Member_member_status', sortable: false, align:'left' },
            { name: 'Aktionen', label: 'Aktionen', field: 'Aktionen', sortable: false }
        ]
        const visible_columns = ref(['Code_time_setting','Code_blocked','code','Member_name','Member_member_status','Aktionen']);
        const export_columns = ref(['code','time_setting','is_active']);
        const initial_pagination = ref(SessionStorage.getItem('doorcodes_pagination'));
        if (initial_pagination.value == null){
            initial_pagination.value = {
                rowsPerPage: 200
            }
        }

        const sortby = ref(initial_pagination.value.sortBy) || ref('code');

        onMounted(() => {
            useLoadPermissions();
            loadCodes();
        })
 
        function onSelectGroup(){
            loadCodes();    
        }

        function prepareCodes(results){
            let code_row = {};
            let exportcode_row = {};
            let codes = [];
            let collected_codes = [];
            export_codes.value = [];
            let bg = '';
            let code = '';
            for (let i=0;i<results.length;i++){
                code_row = results[i];
                if (sortby.value=="Member_name"){
                    if (code_row.Member_name != code){
                        if (bg==''){
                            bg = 'bg-grey-4';
                        } else {
                            bg = '';
                        }
                        code = code_row.Member_name;
                    }
                } else {
                    if (code_row.code != code){
                        if (bg==''){
                            bg = 'bg-grey-4';
                        } else {
                            bg = '';
                        }
                        code = code_row.code;
                    }                    
                }



                if (!inArray(code_row.code,collected_codes)){
                    exportcode_row = {
                        code:code_row.code,
                        time_setting:code_row.Code_time_setting,
                        is_active:code_row.Code_blocked==1 ? 0 : 1
                    };                    
                    collected_codes.push(code_row.code);
                    export_codes.value.push(exportcode_row);                    
                }

                if (code_row.Code_time_setting==1){
                    code_row.timesetting_color='blue';
                }
                code_row.blocked_color='green';
                if (code_row.Code_blocked==1){
                    code_row.blocked_color='red';
                }                
                
                code_row.bg = bg;
                codes.push(code_row);        
            }

            return codes;
        }

        function inArray(needle, haystack) {
            var length = haystack.length;
            for(var i = 0; i < length; i++) {
                if(haystack[i] == needle) return true;
            }
            return false;
        }            

        function checkSortOrder(){
            if (initial_pagination.value.sortBy != sortby.value){
              sortby.value = initial_pagination.value.sortBy || '';  
              loadCodes();  
            }
            
        }

        function customSort(rows, sortBy, descending){
            return rows;
        }

        function loadCodes(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'DoorcodesMembers';
            data.sortby = sortby.value;
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                names.value = prepareCodes(response.data.results);
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function saveCodes() {
            let data = {};
            data.amount = 10;
            data.processaction = 'createCodes';
            const ajaxUrl = modx_options.rest_url + 'Doorcodes/';
            axios.post(ajaxUrl, data)
            .then(function (response) {
                const success = response.data.success;
                let message = response.data.message;
                if (!success){
                $q.dialog({
                    title: 'Warnung!',
                    message: message
                })
                return;              
                }
                message =  response.data.object.new_codes;
                $q.dialog({
                    title: response.data.object.new_count + ' neue Codes wurden erstellt',
                    message: message
                })
                loadCodes();                
                //event.value = response.data.object;
                //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
                //Vue.$router.push('/');
            })
            .catch(function (error) {
                console.log(error);
            });
 
          }         

        function createCodes(){
            $q.dialog({
                title: 'Neue Codes erstellen',
                message: 'Hiermit werden 10 neue Codes erstellt',
                cancel: true,
                persistent: true
              }).onOk(data => {
                saveCodes();
              }).onCancel(() => {
                //entry.value.member_id = 0;
              }).onDismiss(() => {
                //console.log('I am triggered on both OK and Cancel')            
              })              
        }

        function copyData() {
            console.log(export_codes.value);
            var table = useCreateTable(export_codes.value,export_columns.value);
            useCopyTable(table);
        }
        
        function storeFilter(value){
             SessionStorage.set('doorcodes_filter', value);            
        }

        function storePagination(value){
            SessionStorage.set('doorcodes_pagination', value); 
            initial_pagination.value = value;
            checkSortOrder();     
        }

        return {
            filter,
            state,
            useHasPermission,
            onSelectGroup,
            createCodes,
            copyData,
            storeFilter,
            storePagination, 
            customSort,
            names,
            columns,
            visible_columns,
            initial_pagination,
            Quasar,
            SessionStorage
        }
    },

    template: '#view'
}