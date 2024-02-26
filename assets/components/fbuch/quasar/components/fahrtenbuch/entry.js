import { useHasPermission } from "../../composables/helpers.js";
import api_select from '../../components/api_select.js'

export default {
  emits:['nameCheckbox'],
  props: {
    view: '',
    entry: {},
    moveMembers: Function,
    loadAll: Function,
    section: ''
  },
  components: {
    api_select: api_select
  },


  setup(props,{emit}) {

    const { onMounted, ref } = Vue;
    const modx = modx_options;
    const { useQuasar } = Quasar;
    const $q = useQuasar();

    function pullMembers() {
      const properties = {
        target: 'fahrten',
        target_id: props.entry.id,
        date: props.entry.date
      };
      props.moveMembers(properties);
    }

    function deleteEntry(entry) {
      const id = entry.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
      let properties = {};
      properties['processaction'] = 'setDeleted';
      properties['deleted'] = 1;
      if (id) {
        axios.put(ajaxUrl,properties)
          .then(function (response) {
            props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }    

    function setObmann(properties) {
      const id = properties.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
      properties['processaction'] = 'setObmann';
      if (id) {
        axios.post(ajaxUrl,properties)
          .then(function (response) {
            props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }

    function setCox(properties) {
      const id = properties.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
      properties['processaction'] = 'setCox';
      if (id) {
        axios.post(ajaxUrl,properties)
          .then(function (response) {
            props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }

    function setMemberState(name){
      //console.log(name);
      const id = name.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
      name['processaction'] = 'setMemberState';
      if (id) {
        axios.post(ajaxUrl,name)
          .then(function (response) {
            name.finished_menu = false;
            //props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }      

      
    }
    
    function lockEntry(entry) {
      console.log(entry);
      const id = entry.id || false;
      const properties = {}; 
      const ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
      properties['set_locked'] = entry.locked==1 ? 0 : 1;
      if (id) {
        axios.put(ajaxUrl,properties)
          .then(function (response) {
            props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }     
    
    function confirmDeleteEntry(entry){
      $q.dialog({
        title: 'Eintrag entfernen',
        message: 'Soll der Eintrag mit dem Namen <strong>' + entry.Boot_name + '</strong> wirklich gelöscht werden? Trage zur Bestätigung den Namen des Eintrags ein!',
        html: true,
        prompt: {
          model: '',
          isValid: val => val == entry.Boot_name, // << here is the magic
          type: 'text' // optional
        },             
        ok: { label: 'Entfernen' },
        cancel: true,
        persistent: true
      }).onOk(() => {
        deleteEntry(entry);
      })      
    }

    function confirmRemoveName(name) {
      $q.dialog({
        title: 'Person entfernen',
        message: 'Soll ' + name.Member_firstname + ' ' + name.Member_name + ' wirklich aus dem Eintrag entfernt werden?',
        ok: { label: 'Entfernen' },
        cancel: true,
        persistent: true
      }).onOk(() => {
        removeName(name);
      })
    }

    function removeName(name) {
      const id = name.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
      if (id) {
        axios.delete(ajaxUrl)
          .then(function (response) {
            props.loadAll();
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }

    function onNameCheckbox(name){
        emit('nameCheckbox',name,'fahrt');
    }

    return {
      expanded: ref(false),
      modx,
      setCox,
      setObmann,
      confirmRemoveName,
      confirmDeleteEntry,
      useHasPermission,
      pullMembers,
      onNameCheckbox,
      lockEntry,
      setMemberState
    }
  },
  template: '#entry-component'
  // or `template: '#my-template-element'`
}