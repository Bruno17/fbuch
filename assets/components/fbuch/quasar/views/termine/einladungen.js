import api_select from '../../components/api_select.js'
import { useLoadPermissions, useLoadCurrentUser, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

    components: {
        api_select: api_select
    },
    setup() {

        const { onMounted, ref, watch } = Vue;
        const urls = ref({});
        const currentUser = ref({});
        const params = Vue.$router.currentRoute._value.params;
        const id = params.id || 'new';
        const invites = ref([]);

        onMounted(() => {
            useLoadPermissions();
            useLoadCurrentUser().then(function (data) {
                currentUser.value = data.object;
            });
            loadInvited();
        })



        function loadInvited() {
            const data = {};
            data.date_id = id;
            const ajaxUrl = modx_options.rest_url + 'Dateinvited';
            axios.get(ajaxUrl, { params: data })
                .then(function (response) {
                    const results = response.data.results;
                    invites.value = results;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }        

        return {
            invites,
            currentUser,
            useHasPermission,
            urls
        }
    },

    template: '#einladungen'
}