import { useHasPermission } from "../composables/helpers.js";

export default {

    props:{
        event:{},
        loadDayEvents:Function
    },

    setup(props) {
    
      const {onMounted, ref } = Vue;
      const modx = modx_options;
      const { useQuasar } = Quasar;
      const $q = useQuasar(); 
            
      function confirmDelete(){
        $q.dialog({
            title: 'Termin löschen',
            message: 'Soll der Termin wirklich gelöscht werden?',
            ok: {label:'Löschen'},
            cancel: true,
            persistent: true
          }).onOk(() => {
             deleteEvent();
          })
      }

      function deleteEvent(){
        const ajaxUrl = modx_options.rest_url + 'Dates/' + props.event.id;
        const event = {deleted:1};
        axios.put(ajaxUrl,event)
        .then(function (response) {
          props.loadDayEvents();      
        })
        .catch(function (error) {
            console.log(error);
        }); 
      }

      return {modx, confirmDelete,useHasPermission }
    },
    template: `
          <div class="col-12 col-md-6">
            <q-card bordered class="full-height full-width">
              <q-card-section :class="'bg-'+event.Type_colorstyle" class="text-white">
                <div class="text-h6">{{event.title}}</div>
                <div class="text-subtitle2">
                  {{ event.formattedDate }}
                  <template v-if="event.formattedEndDate != ''">
                    <br>
                    {{ event.formattedEndDate }}
                  </template>
                </div>
              </q-card-section>
    
              <q-separator inset></q-separator>
    
              <q-card-section>
                <pre style="white-space: pre-wrap; word-wrap: break-word;font-family: inherit;" >{{ event.description }}</pre>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn v-if="useHasPermission('fbuch_edit_termin')" flat round color="black" icon="edit" :to="'/event-update/'+event.id" ></q-btn>
                <q-btn v-if="modx.user_id==event.createdby || useHasPermission('fbuch_delete_termin')" 
                flat round color="red" icon="delete" 
                @click="confirmDelete"
                ></q-btn>
                <q-btn flat round color="primary" icon="share" ></q-btn>
              </q-card-actions>

            </q-card>
          </div>
    `
    // or `template: '#my-template-element'`
  }