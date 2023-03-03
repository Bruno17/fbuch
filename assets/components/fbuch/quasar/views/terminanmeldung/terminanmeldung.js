import personslist from '../../components/personslist-editor.js'
import { useLoadPermissions, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        personslist: personslist
    },
    setup() {

        const { onMounted, ref,watch } = Vue;
        const entry = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';        

        onMounted(() => {
            useLoadPermissions();
            loadEvent();
        })

        function loadEvent(){
            const data = {};
            data.returntype = 'selfregistered_names'
            const ajaxUrl = modx_options.rest_url + 'Dates/' + id;
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                const object = response.data.object;
                entry.value = object;
            })
            .catch(function (error) {
                console.log(error);
            }); 
        } 
        
        function onUpdateNames(action,value){
            const member_id = value.id || 0;
            const guestname = value.guestname || '';
            const guestemail = value.guestemail || '';
            let names = [];
            let guestnames = [];
            let guestemails = [];
            let properties = {};
            if (member_id > 0){
                names.push(member_id);
            }
            if (guestname != ''){
                guestnames.push(guestname);
                guestemails.push(guestemail);
            }
            properties.processaction = action;
            properties.person = names;
            properties.guestname = guestnames;
            properties.guestemail = guestemails;
            properties.date_id = id;
            
            console.log(properties);

            const ajaxUrl = modx_options.rest_url + 'Datenames' ;
            axios.post(ajaxUrl,properties)
            .then(function (response) {
                loadEvent();
            })
            .catch(function (error) {
                console.log(error);
            });             
            
        }        


        return {
            entry,
            onUpdateNames
        }
    },

    template: '#terminanmeldung'
}