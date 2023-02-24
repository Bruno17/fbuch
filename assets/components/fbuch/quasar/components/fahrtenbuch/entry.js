import { useHasPermission } from "../../composables/helpers.js";

export default {

  props: {
    view: '',
    entry: {},
    moveMembers: Function,
    loadAll: Function,
    section: ''
  },

  setup(props) {

    const { onMounted, ref } = Vue;
    const modx = modx_options;
    const { useQuasar } = Quasar;
    const $q = useQuasar();

    function pullMembers() {
      const properties = {
        target: 'fahrten',
        target_id: props.entry.id
      };
      props.moveMembers(properties);
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

    function setObmann(properties) {
      const id = properties.id || false;
      const ajaxUrl = modx_options.rest_url + 'Fahrtnames/' + id;
      console.log(properties);
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
      console.log(properties);
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

    return {
      expanded: ref(false),
      modx,
      setCox,
      setObmann,
      confirmRemoveName,
      useHasPermission,
      pullMembers
    }
  },
  template: '#entry-component'
  // or `template: '#my-template-element'`
}