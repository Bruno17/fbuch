import api_select from '../../components/api_select.js'
import timeinput from '../../components/timeinput.js'
import datepicker from '../../components/datepicker.js'
import valuesetter from '../../components/valuesetter.js'
import riggerungform from '../../components/fahrtenbuch/riggerungform.js'
import { useLoadPermissions, useLoadCurrentMember, useHasPermission } from "../../composables/helpers.js";

export default {

  props: {

  },
  components: {
    api_select: api_select,
    datepicker: datepicker,
    timeinput: timeinput,
    valuesetter: valuesetter,
    riggerungform:riggerungform
  },

  setup(props) {

    const { onMounted, ref, watch} = Vue;
    const { useQuasar } = Quasar;
    const $q = useQuasar();     
    const routeValue = Vue.$router.currentRoute._value;
    const params = routeValue.params;
    let id = params.id || 'new';
    const entry = ref({});
    const boot = ref({ 'id': 0 });
    const nutzergruppe = ref({});
    const gattung = ref({});
    const newguest = ref({});
    const state = ref({});
    const bootSelect = ref();
    const personSelect = ref();
    const selectionState = ref({});
    const bootsgattungSelect = ref();
    const submitclicked = ref(false);
    const tab = ref('general');
    const inputs = ref({});
    const currentMember = ref({});
    const showpersonstab = ref(true);
    const showbootsgattungselect = ref(false);
    const warnung = ref(false);
    const form_riggerung = ref({});
    const year = params.year || Quasar.date.formatDate(new Date(), 'YYYY');
    const month = params.month || Quasar.date.formatDate(new Date(), 'MM');
    const day = params.day || Quasar.date.formatDate(new Date(), 'DD');
    const date = year + '-' + month + '-' +day;      

    onMounted(() => {
      state.value.firstload = true;
      useLoadPermissions();
      useLoadCurrentMember().then(function (data) {
        currentMember.value = data.object;
      });      
      loadEntry();
      state.value.days = state.value.days || 0;
      state.value.hours = state.value.hours || 0;
      state.value.minutes = state.value.minutes || 0;
      state.value.minutes_total = state.value.minutes_total || 0;
      state.value.duration_valid = state.value.duration_valid || true;
      
      
    })

    watch(() => entry.value.date, (value) => {
      selectionState.value.formattedDate = Quasar.date.formatDate(entry.value.date, 'DD.MM.YYYY');
    })

    function nl2br(text){
      if (text === undefined){
        return
      }
      return text.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1 <br/> $2');
  }    

    function onSelectGattung(value) {
      if (bootsgattungSelect.value){
        bootsgattungSelect.value.loadNames({ 'gattung_name': value });
      }
      if (bootSelect.value){
        bootSelect.value.loadNames({ 'gattung_name': value });
      }  
      entry.value.boot_id = 0;
      selectionState.value.bootsgattung = 0;
      boot.value = {};
    }

    function onSelectBoot(value) {
      entry.value.boot_id = value.value;
      selectionState.value.gattungname = value.Bootsgattung_name;
      selectionState.value.bootsgattung = value.Bootsgattung_id;
      selectionState.value.bootname = value.name;
      if (bootsgattungSelect.value){
        bootsgattungSelect.value.loadNames({ 'gattung_name': value.Bootsgattung_name });  
      }
      if (!state.value.firstload){
        entry.value.gattung_id = 0;
      } 
      loadBoot(entry.value.boot_id);
    }

    function onSelectBootsgattung(value) {
      bootSelect.value.loadNames({ 'gattung_id': value });
      entry.value.boot_id = 0;
      boot.value = {};
    }

    function addGuest() {
      const value = {
        guestname: newguest.value.name || '',
        guestemail: newguest.value.email || '',
        member_status: 'Gasteintrag'
      }
      if (value.name != '') {
        if (!entry.value.names) {
          entry.value.names = [];
        }        
        entry.value.names.push(value);
      }
      newguest.value = {};
    }

    function selectMyself(){
      const id = currentMember.value.id;
      entry.value.member_id = id;          
    }    

    function addMyself(){
      if (!entry.value.names) {
        entry.value.names = [];
      }  
      const id = currentMember.value.id;
      const exists = findPerson(id);
      selectionState.value.person = 0;
      personSelect.value.clearSelection();
      if (exists) {
        return;
      }
      currentMember.value.value = id;
      entry.value.names.push(currentMember.value);          
    }

    function onSelectPerson(value) {
      if (!entry.value.names) {
        entry.value.names = [];
      }
      const id = value.value;
      const exists = findPerson(id);
      selectionState.value.person = 0;
      personSelect.value.clearSelection();
      if (exists) {
        return;
      }
      entry.value.names.push(value);

    }

    function removePerson(index) {
      entry.value.names.splice(index, 1);
    }

    function setObmann(index) {
      for (let i = 0; i < entry.value.names.length; i++) {
        entry.value.names[i].obmann = (i == index) ? 1 : 0;
      }
    }

    function setCox(index) {
      for (let i = 0; i < entry.value.names.length; i++) {
        const iscox = entry.value.names[i].cox == 1 ? 0 : 1;
        entry.value.names[i].cox = (i == index) ? iscox : 0;
      }
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
      let data = {};
      let ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
      if (routeValue.name == 'entry_createfromdate') {
        ajaxUrl = modx_options.rest_url + 'FahrtFromDate/' + id;
        data.datenames_id = params.datenames_id;
      }
      if (routeValue.name == 'entryform_create_gattung') {
        data.gattungname = params.gattungname;
      }     

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;
          entry.value = object;

          if (routeValue.name == 'entryform_create_gattung') {
            selectionState.value.gattungname = object.Gattung_name;
            //onSelectGattung(object.Gattung_name);
          }  else {
            onSelectBoot({ 'value': object.boot_id, 'Bootsgattung_name': object.Gattung_name, 'Bootsgattung_id': object.Gattung_id });
            //tab.value = tab.value=='' ?'date':tab.value;            
          }           
          
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function loadBoot(id) {
      if (id == 0) {
        boot.value = { 'id': 0 };
        return;
      }

      let data = {};
      data.fahrt_gattung_id = entry.value.gattung_id;
      let ajaxUrl = modx_options.rest_url + 'Boote/' + id;

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;
          boot.value = object;
          loadNutzergruppe();
          loadGattung();
          
          state.value.firstload = false;
          //onSelectBoot({ 'value': object.boot_id, 'Bootsgattung_name': object.Gattung_name, 'Bootsgattung_id': object.Gattung_id });
        })
        .catch(function (error) {
          console.log(error);
        });
    }



    function loadNutzergruppe(){
      let data = {};
      let id = boot.value.fahrt_nutzergruppe;
      let ajaxUrl = modx_options.rest_url + 'Bootsnutzergruppen/' + id; 
      axios.get(ajaxUrl, { params: data })
      .then(function (response) {
        const object = response.data.object;
        nutzergruppe.value = object;
        entry.value.nutzergruppe_id = id;
      })
      .catch(function (error) {
        console.log(error);
      });               
    }

    function loadGattung(){
      let data = {};
      let id = boot.value.fahrt_gattung_id;  
      if (routeValue.name == 'entryform_update'){
        if (state.value.firstload && entry.value.gattung_id > 0){
          id = entry.value.gattung_id;  
        }
      }
      let ajaxUrl = modx_options.rest_url + 'Bootsgattungen/' + id;  
      axios.get(ajaxUrl, { params: data })
      .then(function (response) {
        const object = response.data.object;
        gattung.value = object;
        entry.value.gattung_id = id;
      })
      .catch(function (error) {
        console.log(error);
      });              
    }

    function onCancelClick() {
      Vue.$router.push('/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD'));
    }

    function setUrls() {
      return {
        day: '/events/day/' + Quasar.date.formatDate(entry.date, 'YYYY/MM/DD'),
        kalender: '/' + Quasar.date.formatDate(entry.date, 'YYYY/MM'),
        fahrtenbuch: '/fahrtenbuch/fahrtenbuch.html/#/' + Quasar.date.formatDate(entry.date, 'YYYY/MM/DD')
      }
    }

    function onSubmitClick() {
      tab.value = 'general';
      submitclicked.value = true;
    }

    function checkPersonsAndSave(){
      if (!entry.value.names) {
        entry.value.names = [];
      }        

      if (!entry.value.names.length && !entry.value.member_id){

        $q.dialog({
          title: 'Keine Personen eingetragen!',
          html:false,
          options: {
            type: 'radio',
            model: 'opt1',
            // inline: true
            items: [
              { label: 'Personen eintragen', value: 'opt1' },
              { label: 'Personen werden aus Termningruppe reingezogen', value: 'opt2' }
            ]
          },
          persistent: true
        }).onOk(data => {
          if (data == 'opt2'){
              save(); 
          } else {
              if (showpersonstab.value) {
                tab.value = 'persons';  
              }            
          }
        }).onCancel(() => {
          // console.log('>>>> Cancel')
        }).onDismiss(() => {
          // console.log('I am triggered on both OK and Cancel')
        })

        return;  
      } 
      save();     
    }

    function save() {

      if (id == 'new') {
        const ajaxUrl = modx_options.rest_url + 'Fahrten';
        axios.post(ajaxUrl, entry.value)
          .then(function (response) {
            //Vue.$router.push('/events/day/' + Quasar.date.formatDate(event.date, 'YYYY/MM/DD'));
            if (form_riggerung.value.permanent == "1"){
              saveRiggerung(response.data.object.id);
            }            
            Vue.$router.push('/' + Quasar.date.formatDate(entry.value.date, 'YYYY/MM/DD'));
          })
          .catch(function (error) {
            console.log(error);
          });
      } else {
        const ajaxUrl = modx_options.rest_url + 'Fahrten/' + id;
        axios.put(ajaxUrl, entry.value)
          .then(function (response) {
            const success = response.data.success;
            let message = response.data.message;
            message = message == 'is_locked' ? 'Fahrt ist für die Bearbeitung gesperrt' : message;
            if (!success){
              $q.dialog({
                title: 'Warnung!',
                message: message
              })
              return;              
            }
            //console.log(form_riggerung.value);
            if (form_riggerung.value.permanent == "1"){
                saveRiggerung(response.data.object.id);
            }

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
      if (boot.value.Bootsgattung_check_availability == 1) {
        checkAvailability();
        return;
      }

      checkPersonsAndSave();
    }

    function checkAvailability() {
      const id = boot.value.id;
      let data = {};
      data.returntype = 'availability';
      data.entry = {
        id:entry.value.id,
        date:entry.value.date,
        date_end:entry.value.date_end,
        start_time:entry.value.start_time,
        end_time:entry.value.end_time
      } ;
      let ajaxUrl = modx_options.rest_url + 'Boote/' + id;

      axios.get(ajaxUrl, { params: data })
        .then(function (response) {
          const object = response.data.object;

          if (object.available){
              checkPersonsAndSave();
          } else {
            $q.dialog({
              title: 'Dieses Boot ist bereits belegt',
              message: 'von ' + object.errorstart + ' <br>bis '  + object.errorend,
              html:true,
              options: {
                type: 'checkbox',
                model: [],
                // inline: true
                items: [
                  { label: 'Eintrag trotzdem erzwingen. (Nur in dringenden, berechtigten Fällen!)', value: 'forceentry', color: 'secondary' }
                ]
              },
              cancel: true,
              persistent: true
            }).onOk(data => {
              if (data[0] == 'forceentry'){
                  checkPersonsAndSave(); 
              }
            }).onCancel(() => {
              // console.log('>>>> Cancel')
            }).onDismiss(() => {
              // console.log('I am triggered on both OK and Cancel')
            })
          }

          //boot.value = object;
          //onSelectBoot({ 'value': object.boot_id, 'Bootsgattung_name': object.Gattung_name, 'Bootsgattung_id': object.Gattung_id });
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function onChangeRiggerung(form){
      entry.value.gattung_id = form.value.gattung_id.value;
      loadBoot(boot.value.id);
      form_riggerung.value = form.value;
    }

    function saveRiggerung(id) {
      let data = {};
      data.boot_id = boot.value.id;
      data.createdon = date;
      data.gattung_id = form_riggerung.value.gattung_id.value;
      data.name = 'Fahrt:' + id;

        const ajaxUrl = modx_options.rest_url + 'Bootriggerungen';
        axios.post(ajaxUrl, data)
          .then(function (response) {
            const success = response.data.success;
            emit('onOk',form); 
          })
          .catch(function (error) {
            console.log(error);
          });

    }     

    function onReset() {
      //Vue.$router.go(-1);
    }

    return {
      entry,
      boot,
      nutzergruppe,
      gattung,
      state,
      tab,
      newguest,
      onSelectBoot,
      onSelectGattung,
      onSelectBootsgattung,
      onSelectPerson,
      onSubmit,
      onSubmitClick,
      onCancelClick,
      onChangeRiggerung,
      removePerson,
      setObmann,
      setCox,
      addGuest,
      addMyself,
      selectMyself,
      nl2br,
      bootSelect,
      personSelect,
      selectionState,
      bootsgattungSelect,
      submitclicked,
      inputs,
      currentMember,
      showpersonstab,
      showbootsgattungselect,
      warnung
    }
  },
  template: '#entryform-view'
  // or `template: '#my-template-element'`
}