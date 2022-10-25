export default {
   props:{
     controller:''
   }, 

    setup(props) {
      const {onMounted, ref } = Vue;
      const names_options = ref([]);
      const filtered_options = ref([]);
  
      onMounted(() => {
          loadNames();
      })

      function loadNames(){
        
        const ajaxUrl = modx_options.rest_url + props.controller;
        axios.get(ajaxUrl)
        .then(function (response) {
            names_options.value = response.data.results;
            filtered_options.value = response.data.results;                 
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
  
      return { names_options, filtered_options, filterFn }
    },
    template: `
    <q-select
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
  