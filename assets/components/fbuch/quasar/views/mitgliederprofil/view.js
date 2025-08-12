import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
//import skillscategorytree from '../../components/kompetenzstufenskills/skills_category_tree.js';

export default {

    components: {
        //skillscategorytree: skillscategorytree
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const fbuchuser = ref({});
 
        onMounted(() => {
            useLoadPermissions();
            loadCurrentFbuchUser();
        })

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
            useHasPermission,
            formatDate
        }
    },

    template: '#view'
}