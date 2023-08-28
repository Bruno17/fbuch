export default {

    components: {
 
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const mailinglists = ref([]);
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';        

        onMounted(() => {
            loadMailinglists();
        })

        function loadMailinglists(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Mailinglists';
            //data.iid = this.$route.query.iid;
            //data.code = this.$route.query.code; 
            data.date_id = id;
            data.which_page = 'subscribe';
            //data.returntype = 'grouped_by_type';
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
            mailinglists,
            subscribe
        }
    },

    template: '#einladungsliste'
}