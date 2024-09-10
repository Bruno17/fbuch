import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {

    },
    setup() {

        const { onMounted, ref } = Vue;
        const { useQuasar } = Quasar;
        const $q = useQuasar();            
        const tab = ref('acls');
        const total = ref(0);
        const rest = ref(0);

        onMounted(() => {
            useLoadPermissions();
        })

        function setupAcls(){
            const ajaxUrl = modx_options.rest_url + 'setup/Acls';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function prefill(classname){
            const ajaxUrl = modx_options.rest_url + 'setup/PrefillTable';
            axios.post(ajaxUrl,{'classname':classname})
            .then(function (response) {
                const success = response.data.success;
                let message = response.data.message;
                message = message == 'table has allready items' ? 'Die Tabelle hat bereits Inhalt. Leere die Tabelle, bevor Du sie mit initialem Inhalt befüllst.' : message;
                if (!success){
                  $q.dialog({
                    title: 'Warnung!',
                    message: message
                  })
                  return;              
                }     
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function setupResources(){
            const ajaxUrl = modx_options.rest_url + 'setup/Resources';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            }); 
        } 

        function insertUsedElementsSnippet(){
            const ajaxUrl = modx_options.rest_url + 'setup/InsertUsedElementsSnippet';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            });             
        }

        function findOrphans(){
            const ajaxUrl = modx_options.rest_url + 'setup/FindOrphans';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }         

        function fixFinishedEntries(){
            const ajaxUrl = modx_options.rest_url + 'setup/FixFinishedEntries';
            axios.post(ajaxUrl)
            .then(function (response) {
  
            })
            .catch(function (error) {
                console.log(error);
            }); 
        } 

        async function addStatusToEntries(){
            const ajaxUrl = modx_options.rest_url + 'setup/AddStatusToEntries';
            const limit = 1000;
            let again = true;
            let prevrest = 0;
            await getTotal(ajaxUrl);
            
            do {
                await axios.post(ajaxUrl,{'limit':limit})
                .then(function (response) {
                    rest.value = response.data.object.total;
                    if (prevrest == rest.value){
                        again = false;
                    }
                    prevrest = rest.value;
                })
                .catch(function (error) {
                    console.log(error);
                });     
              }
              while (rest.value > 0);            

        } 
        
        async function getTotal(ajaxUrl){
            await axios.post(ajaxUrl,{'returntype':'total'})
            .then(function (response) {
                total.value = response.data.object.total;
            })
            .catch(function (error) {
                console.log(error);
            });  
        }
 
        return {
            tab,
            setupAcls,
            prefill,
            setupResources,
            fixFinishedEntries,
            addStatusToEntries,
            findOrphans,
            insertUsedElementsSnippet,
            total,
            rest
        }
    },

    template: '#setup-view'
}