//import { computed, onMounted, useAttrs } from "vue";

export default {
    props:{
      controller:'',
      first_option:{},
      callback_onload:false,
      options:[],
      modelValue: ''
    }, 
 
     setup(props,context) {
       const {computed,useAttrs,onMounted, ref,watch } = Vue;
        const groups = computed(() =>
        collect(props.options).unique("group").pluck("group")
        );

        console.log(props.options);

        const items = computed(() => {
        const options = collect();

        groups.value.each((group) => {
            const items = collect(props.options).where("group", group).toArray();
            options
            .push({ label: group, is_group: true, disable: true })
            .push(...items);
        });

        return options.toArray();
        });

        const model = ref([]);

        const attrs = useAttrs();

        function handleItemUniqueByGroup(items){
            if (!attrs.hasOwnProperty("multiple") || attrs.multiple === false) {
                return;
            }
            const collection = collect(items);

            const last = collection.last()||{};

            const filtered = collection.filter((item, key) => {
                console.log('last',last);
                console.log('item',item);

                return true;
            }).toArray();
            
            model.value = filtered;
            context.emit('update:modelValue', model.value); 

            return;

            model.value = 
                collection.unique((item) => {
                // Allow selecting multiple items that are not grouped
                if (item.group === null) {
                    return item.value;
                }
                console.log(item);
                return item.group;
                })
                .toArray();

                context.emit('update:modelValue', model.value);     
        };

      watch(() => props.modelValue, (value) => {
          //console.log('watch',value);
          //model.value=value;
          //context.emit('update:modelValue', model.value);       
      })        

        onMounted(() => {
            console.log('mounted',props.modelValue);
        //handleItemUniqueByGroup(model.value);
          model.value=props.modelValue;
          //model.value=[{value:'A:1:1',label:'test'}];
          context.emit('update:modelValue', model.value); 
        });
   
   
       return { 
           model,
           items,
           handleItemUniqueByGroup
       }
     },
     template: `
     
  <q-select
    v-model="model"
    :options="items"
    @update:model-value="handleItemUniqueByGroup"
    v-bind="$attrs"
  >
    <template v-slot:option="scope">
      <q-item
        v-if="scope.opt.is_group"
        v-bind="{ ...scope.itemProps, ...groupItemAttrs }"
        :key="scope.label"
        :disable="true"
        :clickable="false"
        class="text-xs bg-slate-200 !cursor-default hover:!bg-slate-200"
        dense
      >
        <q-item-section class="!cursor-default">
          {{ scope.opt.label || "Ungroup" }}
        </q-item-section>
      </q-item>
      <q-item
        v-else
        v-bind="{ ...scope.itemProps, ...optionItemAttrs }"
        :key="scope.id"
      >
        <q-item-section>{{ scope.opt.label }}</q-item-section>
      </q-item>
    </template>
  </q-select>
     `
     // or `template: '#my-template-element'`
   }


