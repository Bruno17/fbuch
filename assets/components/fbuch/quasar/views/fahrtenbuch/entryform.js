import api_select from '../../components/api_select.js'
import timeinput from '../../components/timeinput.js'
import datepicker from '../../components/datepicker.js'
import { useLoadPermissions, useHasPermission } from "../../composables/helpers.js";

export default {

  props: {

  },
  components: {
    api_select: api_select,
    datepicker: datepicker,
    timeinput: timeinput,
  },

  setup(props) {

    const { onMounted, ref } = Vue;
    const params = Vue.$router.currentRoute._value.params;
    let id = params.id || 'new';
    const entry = ref({});
    const state = ref({});
    const bootSelect = ref();
    const personSelect = ref();
    const bootSelection = ref({});
    const bootsgattungSelect = ref();
    const submitclicked = ref(false);
    const tab = ref('general');

    onMounted(() => {
      //console.log('eventform mounted');
      useLoadPermissions();
      loadEntry();
      state.value.days = state.value.days || 0;
      state.value.hours = state.value.hours || 0;
      state.value.minutes = state.value.minutes || 0;
      state.value.minutes_total = state.value.minutes_total || 0;
      state.value.duration_valid = state.value.duration_valid || true;
    })

    function onSelectGattung(value) {
      bootsgattungSelect.value.loadNames({ 'gattung_name': value });
      bootSelect.value.loadNames({ 'gattung_name': value });
      entry.value.boot_id = 0;
      bootSelection.value.bootsgattung = 0;
    }

    function onSelectBoot(value) {
      console.log('onSelectBoot', value);
      entry.value.boot_id = value.value;
      bootSelection.value.gattungname = value.Bootsgattung_name;
      bootSelection.value.bootsgattung = value.Bootsgattung_id;
      bootsgattungSelect.value.loadNames({ 'gattung_name': value.Bootsgattung_name });
    }

    function onSelectBootsgattung(value) {
      bootSelect.value.loadNames({ 'gattung_id': value });
      entry.value.boot_id = 0;
    }

    function onSelectPerson(value) {
      if (!entry.value.names) {
        entry.value.names = [];
      }
      const id = value.value;
      const exists = findPerson(id);
      bootSelection.value.person = 0;
      personSelect.value.clearSelection();
      if (exists) {
        return;
      }
      entry.value.names.push(value);

    }

    function removePerson(index) {
      entry.value.names.splice(index, 1);
    }

    function findPerson(id) {
      let result = false;
      entry.value.names.forEach((person) => {
        if (person.value == id) {
          result = person;
        }
      })
      return result;
    }

    function loadEntry() {
      const data = {};
      const ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;
          entry.value = object;
          onSelectBoot({ 'value': object.boot_id, 'Bootsgattung_name': object.Gattung_name, 'Bootsgattung_id': object.Gattung_id });
          //tab.value = tab.value=='' ?'date':tab.value;
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function setUrls() {
      return {
        day: '/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD'),
        kalender: '/' + Quasar.date.formatDate(event.date, 'YYYY/MM'),
        fahrtenbuch: '/?offset=' + Quasar.date.formatDate(event.date, 'YYYY-MM-DD') + '&type=dragdrop&dir=none'
      }
    }

    function onSubmitClick() {
      //console.log('submitclick',id);
      tab.value = 'general';
      submitclicked.value = true;
    }

    function save() {

      if (id == 'new') {
        const ajaxUrl = modx_options.rest_url + 'Fahrten';
        axios.post(ajaxUrl, entry.value)
          .then(function (response) {
            //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD'));
            Vue.$router.push('/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD'));
          })
          .catch(function (error) {
            console.log(error);
          });
      } else {
        const ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
        axios.put(ajaxUrl, entry.value)
          .then(function (response) {
            //event.value = response.data.object;
            //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.value.date, 'YYYY/MM/DD')); 
            Vue.$router.push('/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD'));
          })
          .catch(function (error) {
            console.log(error);
          });
      }
    }

    function onSubmit() {
      save();
    }

    function onReset() {
      console.log('reset');
      //Vue.$router.go(-1);
    }

    return {
      entry,
      state,
      tab,
      onSelectBoot,
      onSelectGattung,
      onSelectBootsgattung,
      onSelectPerson,
      onSubmit,
      onSubmitClick,
      removePerson,
      bootSelect,
      personSelect,
      bootSelection,
      bootsgattungSelect,
      submitclicked,
    }
  },
  template: '#entryform-view'
  // or `template: '#my-template-element'`
}