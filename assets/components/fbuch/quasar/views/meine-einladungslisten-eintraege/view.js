import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {

    },
    setup() {

        const { onMounted, ref } = Vue;
        const fbuchuser = ref({});
        const mailinglisttypes = ref([]);
        const mailinglists = ref([]);
        const tab = ref(null);

        onMounted(() => {
            useLoadPermissions();
            loadMailinglistTypes();
            loadMailinglists();
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

        function loadMailinglistTypes(){
            var data = {};
            //var self = this;
            var ajaxUrl = modx_options.rest_url + 'Mailinglisttypes';
            //data.iid = this.$route.query.iid;
            //data.code = this.$route.query.code;            
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                mailinglisttypes.value = response.data.results;
                if (typeof mailinglisttypes.value[0] != "undefined") {
                    tab.value = mailinglisttypes.value[0]['name'];   
                }                
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function loadMailinglists(){
            var data = {};
            //var self = this;
            var ajaxUrl = modx_options.rest_url + 'Mailinglists';
            //data.iid = this.$route.query.iid;
            //data.code = this.$route.query.code; 
            data.returntype = 'grouped_by_type';
            data.which_page = 'subscribe';
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                mailinglists.value = response.data.results;
                //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
                //self.$forceUpdate();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function subscribe(mailinglist,action){
            //var self = this;
            var data = {};
            
            data.id = mailinglist['Names_id'];
            data.action = action;
            data.list_id = mailinglist['id'];
            //data.iid = this.$route.query.iid;
            //data.code = this.$route.query.code;             

            if (mailinglist.Names_active){
                var ajaxUrl = modx_options.rest_url + 'Mailinglistnames';
                axios.put(ajaxUrl,data)
                .then(function (response) {
                    loadMailinglists();
                })
                .catch(function (error) {
                    console.log(error);
                });
            } else {
                var ajaxUrl = modx_options.rest_url + 'Mailinglistnames';
                axios.post(ajaxUrl,data)
                .then(function (response) {
                    loadMailinglists();
                })
                .catch(function (error) {
                    console.log(error);
                });            
            }
        
        }


        return {
            tab,
            mailinglists,
            mailinglisttypes,
            fbuchuser,
            useHasPermission,
            subscribe 
        }
    },

    template: '#view'
}