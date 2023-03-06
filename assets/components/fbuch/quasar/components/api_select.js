export default {
   props:{
     controller:'',
     first_option:{}
   }, 

    setup(props) {
      const {onMounted, ref } = Vue;
      const names_options = ref([]);
      const filtered_options = ref([]);
      const fieldRef = ref(null);

      onMounted(() => {
          loadNames();
      })

      function clearSelection(){
        filtered_options.value = names_options.value;  
      }

      function loadNames(data = {}){
        const ajaxUrl = modx_options.rest_url + props.controller;
        axios.get(ajaxUrl, { params: data })
        .then(function (response) {
            let options = [];
            if (props.first_option && props.first_option.label && props.first_option.value){
              options.push(props.first_option);
              options.push(...response.data.results);      
            } else {
              options = response.data.results;
            }
            names_options.value = options;
            filtered_options.value = options;
            //resetValidation();                 
        })
        .catch(function (error) {
            console.log(error);
        });            
    }      
  
    function filterFn (val, update, abort) {
      update(() => {
          const needle = val.toLowerCase();
          filtered_options.value = names_options.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
  }     
  
      return { 
        names_options, 
        filtered_options, 
        filterFn, 
        fieldRef, 
        loadNames, 
        clearSelection}
    },
    template: `
    <q-select
    ref="fieldRef"
    outlined
    use-input
    hide-selected
    fill-input
    map-options
    emit-value
    input-debounce="0"
    :options="filtered_options"
    @filter="filterFn"
    v-on:input-value="$emit('input-value', $event.value)" 
    >
    <template v-slot:no-option>
      <q-item>
        <q-item-section class="text-grey">
          No results
        </q-item-section>
      </q-item>
    </template>
    <template v-slot:option="scope">
    <q-item v-bind="scope.itemProps">
      <q-item-section v-if="scope.opt.colorstyle" avatar>
      <q-avatar :color="scope.opt.colorstyle" />
      </q-item-section>
      <q-item-section>
        <q-item-label>{{ scope.opt.label }}</q-item-label>
        <q-item-label caption>{{ scope.opt.description }}</q-item-label>
      </q-item-section>
    </q-item>
  </template>      
  </q-select>
    `
    // or `template: '#my-template-element'`
  }
  