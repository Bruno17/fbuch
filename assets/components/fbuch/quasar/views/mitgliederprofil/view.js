import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import fahrten_listview from '../../components/ranglisten/fahrten_listview.js';
//import skillscategorytree from '../../components/kompetenzstufenskills/skills_category_tree.js';

export default {

    components: {
        //skillscategorytree: skillscategorytree
        fahrten_listview: fahrten_listview
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const fbuchuser = ref({});
        const current_member = ref({});
 
        onMounted(() => {
            useLoadPermissions();
            loadCurrentFbuchUser();
        })

        function showFahrten(row){
            current_member.value.id = row.id;
            current_member.value.Vorname=row.firstname;
            current_member.value.Nachname=row.name;
            state.value.showfahrten=true; 
            state.value.showfilter=true; 
            state.value.querytype='allemembereintraege';            
        }                

        function loadCurrentFbuchUser(){
            var data = {};
            //var self = this;
            var ajaxUrl = modx_options.rest_url + 'Names/me';
            //data.iid = this.$route.query.iid;
            //data.code = this.$route.query.code;
            data.id = 'me';
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                //console.log(response);
                fbuchuser.value = response.data.object;
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function formatDate(date){
            return Quasar.date.formatDate(date, 'dd DD. MMMM YYYY')
        }        

        
        return {
            state,
            fbuchuser,
            current_member,
            useHasPermission,
            formatDate,
            showFahrten
        }
    },

    template: '#view'
}