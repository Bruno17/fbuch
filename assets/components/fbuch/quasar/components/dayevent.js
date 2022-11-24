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
                <q-btn color="primary" icon-right="keyboard_arrow_right" label="Aktionen" >
                <q-menu anchor="bottom right" self="top right" :offset="[0, 5]">
                <q-list dense bordered separator>

                <q-item :href="'/termine/anmelden.html?date_id='+event.id"  clickable v-ripple>
                <q-item-section avatar><q-avatar icon="group_add" /></q-item-section>
                  <q-item-section>
                  <q-item-label>Anmelden</q-item-label>
                  <q-item-label caption>Dich oder Andere anmelden<br>oder Nachricht schreiben</q-item-label>   
                  </q-item-section>
                </q-item> 
                
                <q-item :href="'/?offset='+event.date + '&type=dragdrop&dir=none'"  clickable v-ripple>
                <q-item-section avatar><q-avatar icon="launch" /></q-item-section>
                  <q-item-section>
                  <q-item-label>Zum Termin im Fahrtenbuch</q-item-label>
                  </q-item-section>
                </q-item>                    

                <q-item :href="'/termine/rudern.html?date_id='+event.id"  clickable v-ripple>
                <q-item-section avatar><q-avatar icon="event" /></q-item-section>
                  <q-item-section>
                  <q-item-label>Einladungen vornehmen</q-item-label>
                  <q-item-label caption>Übersicht Einladungen und Zusagen</q-item-label>                  
                  </q-item-section>
                </q-item>                
                
                <q-item  v-if="useHasPermission('fbuch_edit_termin')" :to="'/event-update/'+event.id" clickable v-ripple>
                <q-item-section avatar><q-avatar icon="edit" /></q-item-section>
                  <q-item-section>Termin/Wiederholungen bearbeiten</q-item-section>
                </q-item>

                <q-item v-if="event.parent>0 && useHasPermission('fbuch_edit_termin')" @click="confirmDelete" clickable v-ripple>
                <q-item-section avatar><q-avatar icon="visibility_off" /></q-item-section>
                  <q-item-section>
                  <q-item-label>Diese Wiederholung verbergen</q-item-label>
                  </q-item-section>
                </q-item>                 

                <q-item  v-if="modx.user_id==event.createdby || useHasPermission('fbuch_delete_termin')"  @click="confirmDelete" clickable v-ripple>
                <q-item-section avatar><q-avatar text-color="red" icon="delete" /></q-item-section>
                  <q-item-section>Termin löschen</q-item-section>
                </q-item>       
       
              </q-list>
                </q-menu>
                </q-btn>
              </q-card-actions>

            </q-card>
          </div>
    `
    // or `template: '#my-template-element'`
  }