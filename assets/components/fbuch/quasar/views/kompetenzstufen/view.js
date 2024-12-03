import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import valuesetter from '../../components/valuesetter.js'

export default {

    components: {
        valuesetter: valuesetter
    },
    setup() {

        const { onMounted, ref } = Vue;
        const mm_expanded = ref(false);
        const state = ref({});
        const levels = ref([]);
        const bootsnutzergruppen = ref([]);

        onMounted(() => {
            useLoadPermissions();
            loadBootsnutzergruppen();
        })

        function nl2br(text){
            return text.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/> $2');
        }

        function loadCompetencyLevels(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevels';
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                levels.value = response.data.results;
            })
            .catch(function (error) {
                console.log(error);
            });            
        }

        function prepareNutzergruppen(group){
            let groups = {};
            for (let i in group){
                 groups[group[i].name] = group[i];
            }
            bootsnutzergruppen.value = groups;
        }        

        function loadBootsnutzergruppen(){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'Bootsnutzergruppen';
 
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                prepareNutzergruppen(response.data.results);
                loadCompetencyLevels();
            })
            .catch(function (error) {
                console.log(error);
            });            
        }        

        return {
            mm_expanded,
            state,
            levels,
            bootsnutzergruppen,
            nl2br,
            useHasPermission 
        }
    },

    template: '#view'
}