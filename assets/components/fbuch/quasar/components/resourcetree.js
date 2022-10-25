export default {
  setup() {
    const {onMounted, ref } = Vue;
    const resourcetree = ref([]);

    onMounted(() => {
        loadTree();
    })

    function loadTree(){
        const data = {};
        const ajaxUrl = modx_options.rest_url + 'ResourceTree';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           resourcetree.value = response.data.results;
        })
        .catch(function (error) {
            console.log(error);
        }); 
    }      

    return { resourcetree }
  },
  template: `
  <q-list bordered separator>
    <template v-for="item in resourcetree">
      <template v-if="item.haschildren==0">
        <q-item clickable v-ripple tag="a" v-bind="item.attributes" :href="item.link">
          <q-item-section>{{ item.menutitle }}</q-item-section>
        </q-item>
      </template>

      <template v-if="item.haschildren==1">
        <q-expansion-item group="somegroup" :label="item.menutitle" >
          <q-list bordered separator>
            <template v-for="child in item.children">
              <q-item clickable v-ripple tag="a" v-bind="child.attributes" :href="child.link" :inset-level="0.2">
                <q-item-section>{{ child.menutitle }}</q-item-section>
              </q-item>
            </template>
          </q-list>
        </q-expansion_item>
      </template>
    </template>
  </q-list> 
  `
  // or `template: '#my-template-element'`
}
