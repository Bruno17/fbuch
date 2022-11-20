const {onMounted, ref } = Vue;
const userPermissions = ref([]);

export function useLoadPermissions(permissions){
    const data = {};
    const ajaxUrl = modx_options.rest_url + 'Permissions';
    data.permissions = permissions;
    axios.get(ajaxUrl,{params:data})
    .then(function (response) {
        userPermissions.value = response.data.results;
        //console.log(loadedEvents.value);
    })
    .catch(function (error) {
        console.log(error);
    }); 
}

export function useHasPermission(permission){
    return userPermissions.value.includes(permission); 
}