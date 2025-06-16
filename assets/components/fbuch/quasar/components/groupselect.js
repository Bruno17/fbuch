//import { computed, onMounted, useAttrs } from "vue";

export default {
    emits:['onFiltered'],
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

        function getGroupByValue(value){
            const item = collect(props.options).where("value", value).first() || false;
            return item ? item.group : '';
        }

        function handleItemUniqueByGroup(items,dontemit){
            console.log('handleItemUniqueByGroup',items);
            if (!attrs.hasOwnProperty("multiple") || attrs.multiple === false) {
                return;
            }
            const collection = collect(items);

            const last = collection.last()||{};

            const filtered = collection.filter((item, key) => {
                if (item == last){
                    return true;
                }
                const last_group = getGroupByValue(last);
                const item_group = getGroupByValue(item);
                if (last_group==item_group){
                    return false;
                }
                return true;
            }).toArray();
            model.value = filtered;
            if (dontemit){

            } else {
              context.emit('update:modelValue', filtered); 
              context.emit('onFiltered', filtered);                 
            }
 
            

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
          model.value=value;
          //context.emit('update:modelValue', model.value); 
          handleItemUniqueByGroup(model.value,true);      
      })        

        onMounted(() => {
          model.value=props.modelValue;
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


