const {onMounted, ref } = Vue;
const userPermissions = ref([]);
const loaded = ref(false);

export function useLoadPermissions(permissions){
    if (loaded.value){
        return;
    }
    const data = {};
    const ajaxUrl = modx_options.rest_url + 'Permissions';
    data.permissions = permissions;
    axios.get(ajaxUrl,{params:data})
    .then(function (response) {
        userPermissions.value = response.data.results;
        loaded.value=true;
        //console.log(loadedEvents.value);
    })
    .catch(function (error) {
        console.log(error);
    }); 
}

export function useHasPermission(permission){
    if (userPermissions.value.includes('is_sudo')){
        return true;
    }
    return userPermissions.value.includes(permission); 
}