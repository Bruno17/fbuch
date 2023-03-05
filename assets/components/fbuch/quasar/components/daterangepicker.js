export default {
    props:['date','startfield','endfield'],
    setup(props) {
      const {onMounted, ref } = Vue;
      //const date = ref({ from: '2020-07-08 00:00:00', to: '2020-07-17 00:00:00' })
      const proxyDate = ref({})
      //const startdate = ref(props.startdate);
      const startfield = props.startfield;
      const endfield = props.endfield;

      onMounted(() => {

      })
  
      function updateProxy () {
        if (props.date[startfield] == props.date[endfield]){
            proxyDate.value = props.date[startfield];
        } else {
            proxyDate.value.from = props.date[startfield];
            proxyDate.value.to = props.date[endfield];
        }
        //proxyDate.value = date.value
      }

      function save () {
        if (proxyDate.value.from){        
          //date.value = proxyDate.value;
          props.date[startfield] = proxyDate.value.from;
          props.date[endfield] = proxyDate.value.to;
        } else {
          props.date[startfield] = proxyDate.value;
          props.date[endfield] = proxyDate.value;
        }
    }
  
      return {proxyDate, save, updateProxy }
    },
    template: `
    <div class="q-pa-md">
    <div class="q-mb-sm">
      <q-badge color="teal">
        Model: {{ proxyDate }} <br>
        startdate: {{ startdate }}
      </q-badge>
    </div>

    <q-btn icon="event" round color="primary">
      <q-popup-proxy @before-show="updateProxy" cover transition-show="scale" transition-hide="scale">
        <q-date range v-model="proxyDate"  mask="YYYY-MM-DD 00:00:00">
          <div class="row items-center justify-end q-gutter-sm">
            <q-btn label="Abbrechen" color="primary" flat v-close-popup ></q-btn>
            <q-btn label="OK" color="primary" flat @click="save" v-close-popup ></q-btn>
          </div>
        </q-date>
      </q-popup-proxy>
    </q-btn>
  </div>
    `
    // or `template: '#my-template-element'`
  }
  