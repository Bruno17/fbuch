import api_select from './api_select.js'
import { useLoadPermissions, useHasPermission } from "../composables/helpers.js";

export default {
    emits: ['updateNames'],
    props: {
        entry:{},
        hideoptions:{}
    },

    components: {
        api_select: api_select
    },
    setup(props, {emit}) {

        const { onMounted, ref } = Vue;
        const selectionState = ref({});
        const newguest = ref({});
        const personSelect = ref();
        const hideoptions = {
            'cox':props.hideoptions.cox || false,
            'obmann':props.hideoptions.obmann || false,
        }

        onMounted(() => {
            useLoadPermissions();
            initNames();
        })

        function setObmann(index) {
            for (let i = 0; i < props.entry.names.length; i++) {
              props.entry.names[i].obmann = (i == index) ? 1 : 0;
            }
          }
      
          function setCox(index) {
            for (let i = 0; i < props.entry.names.length; i++) {
              const iscox = props.entry.names[i].cox == 1 ? 0 : 1;
              props.entry.names[i].cox = (i == index) ? iscox : 0;
            }
          }            

        function initNames(){
            if (!props.entry.names) {
                props.entry.names = [];
            }            
        }
        function removePerson(index) {
            const name = props.entry.names[index];
            props.entry.names.splice(index, 1);
            emit('updateNames','remove',name);
        }
        function findPerson(id) {
            let result = false;
            props.entry.names.forEach((person) => {
                if (person.value == id) {
                    result = person;
                }
            })
            return result;
        }

        function onSelectPerson(value) {
            if (!props.entry.names) {
                props.entry.names = [];
            }
            const id = value.value;
            const exists = findPerson(id);
            selectionState.value.person = 0;
            personSelect.value.clearSelection();
            if (exists) {
                return;
            }
            props.entry.names.push(value);
            emit('updateNames','add',value);

        }
        function addGuest() {
            const value = {
                guestname: newguest.value.name || '',
                guestemail: newguest.value.email || '',
                member_status: 'Gasteintrag'
            }
            if (value.name != '') {
                if (!props.entry.names) {
                    props.entry.names = [];
                }
                props.entry.names.push(value);
                emit('updateNames','add',value);
            }
            newguest.value = {};
        }
        return {
            selectionState,
            newguest,
            props,
            personSelect,
            onSelectPerson,
            removePerson,
            addGuest,
            setObmann,
            setCox,
            useHasPermission,
            hideoptions
        }
    },

    template: '#personslist-editor'
}