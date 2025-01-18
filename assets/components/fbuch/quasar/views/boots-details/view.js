import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js'

export default {

    components: {
        valuesetter: valuesetter
    },
    setup() {

        const { onMounted, ref } = Vue;
        const state = ref({});
        const boat = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';        

        onMounted(() => {
            useLoadPermissions();
            loadBoatData();
        })

        function loadBoatData(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Boote/' + id;
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                boat.value = response.data.object;
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function lockBoat(){
            setBoatData({gesperrt:1});
        }
        function unlockBoat(){
            setBoatData({gesperrt:0});
        }        

        function setBoatData(data){
            var ajaxUrl = modx_options.rest_url + 'Boote/' + id;
 
            axios.put(ajaxUrl,data)
            .then(function (response) {
                //boat.value = response.data.object;
                loadBoatData();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }        


        return {
            state,
            boat,
            useHasPermission,
            lockBoat,
            unlockBoat 
        }
    },

    template: '#view'
}