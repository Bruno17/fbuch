import { useHasPermission } from "../composables/helpers.js";

export default {
  emits: ['updateEvent', 'nameCheckbox'],
  props: {
    view: '',
    event: {}
  },

  setup(props, { emit }) {

    const { onMounted, ref } = Vue;
    const modx = modx_options;
    const { useQuasar } = Quasar;
    const $q = useQuasar();
    const state = ref({});

    function confirmDelete() {
      $q.dialog({
        title: 'Termin löschen',
        message: 'Soll der Termin wirklich gelöscht werden?',
        ok: { label: 'Löschen' },
        cancel: true,
        persistent: true
      }).onOk(() => {
        deleteEvent();
      })
    }

    function confirmHide() {
      $q.dialog({
        title: 'Termin verbergen',
        message: 'Soll der Termin wirklich deaktiviert werden? Termin kann über die Wiederholungen wieder sichtbar gemacht werden.',
        ok: { label: 'Verbergen' },
        cancel: true,
        persistent: true
      }).onOk(() => {
        hideEvent();
      })
    }

    function deleteEvent() {
      const ajaxUrl = modx_options.rest_url + 'Dates/' + props.event.id;
      const event = { deleted: 1 };
      axios.put(ajaxUrl, event)
        .then(function (response) {
          state.value.showmenu = false;
          emit('updateEvent');
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function hideEvent() {
      const ajaxUrl = modx_options.rest_url + 'Dates/' + props.event.id;
      const event = { hidden: props.event.hidden == 1 ? 0 : 1 };
      axios.put(ajaxUrl, event)
        .then(function (response) {
          emit('updateEvent');
        })
        .catch(function (error) {
          console.log(error);
        });
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
      console.log(name);
      let properties = {};
      const member_id = name.member_id || false;
      const datename_id = name.id || false;
      const ajaxUrl = modx_options.rest_url + 'Datenames';
      if (member_id || datename_id) {
        properties.processaction = 'remove';
        properties.person = member_id;
        properties.date_id = props.event.id;
        properties.datename_id = datename_id;
        axios.post(ajaxUrl, properties)
          .then(function (response) {
            emit('updateEvent');
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }

    function onNameCheckbox(name) {
      emit('nameCheckbox', name, 'date');
    }

    return {
      expanded: ref(false),
      modx,
      state,
      confirmDelete,
      confirmHide,
      hideEvent,
      useHasPermission,
      confirmRemoveName,
      onNameCheckbox
    }
  },
  template: '#dayevent-component'
  // or `template: '#my-template-element'`
}