import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";

export default {

    components: {

    },
    setup() {

        const { onMounted, ref } = Vue;
        const namesselect = ref(null);
        const tab = ref('');
        const mailinglists = ref([]);
        const mailinglisttypes = ref([]);
        const mailinglistnames = ref([]);
        const filtered_options = ref([]);
        const mailinglist_options = ref([]);
        const namesoptions = ref([]);
        const fbuchuser = ref({});
        const new_name_ids = ref(null);
        const importlist_id = ref(null);
        const current_list_id = ref(null);
        const addnames_modal = ref(false);
        const importlist_modal = ref(false);
        const mailinglist_modal = ref(false);
        const mailinglist_modal_title = ref('Einladungsliste erstellen');
        const mailinglist_form = ref({
          id:{value:0,default:0} , 
          name:{value:'',default:''} ,
          type:{value:'',default:''} ,
          target_group:{value:'',default:''} ,
          description:{value:'',default:''} ,
          weekday:{value:'',default:''} ,
          time:{value:'',default:''} ,
          hidden:{value:'',default:'0'} ,
        });

        onMounted(() => {
            useLoadPermissions();
            loadNames();
            loadMailinglistTypes();
            loadMailinglists();
            loadCurrentFbuchUser();
        })

        function filterFn(val, update, abort) {
            update(() => {
                const needle = val.toLowerCase();
                filtered_options.value = namesoptions.value.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
            })
        } 

      function loadCurrentFbuchUser(){
          var data = {};
          //var self = this;
          var ajaxUrl = modx_options.rest_url + 'Names/me';
          //data.iid = this.$route.query.iid;
          //data.code = this.$route.query.code;
          data.id = 'me';
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
              //console.log(response);
              self.fbuchuser = response.data.object;
              //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
              //self.$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          });            
      }

      function loadMailinglistTypes(){
          var data = {};
          var self = this;
          var ajaxUrl = modx_options.rest_url + 'Mailinglisttypes';
          //data.iid = this.$route.query.iid;
          //data.code = this.$route.query.code;  
          data.show_hidden = 1;
          data.which_page = 'edit_mailinglists';
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
              mailinglisttypes.value = response.data.results;
              if (typeof mailinglisttypes.value[0] != "undefined") {
                  tab.value = mailinglisttypes.value[0]['name'];   
              }                
              //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
              //self.$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          });            
      }

      function loadMailinglistNames(list_id) {
          var data = {};
          //var self = this;
          var ajaxUrl = modx_options.rest_url + 'Mailinglistnames';
          data.list_id = list_id;
          //data.code = this.$route.query.code; 
          //data.returntype = 'grouped_by_type';
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
              //new_name_id.value = null;
              mailinglistnames.value[list_id] = response.data.results;
              //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
              //self.$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          });  
      }

      function addNames(){
          if (new_name_ids.value == null){
              addnames_modal.value = false;
              return;
          }
      
          //var self = this;
          var data = {};
          
          data.id = current_list_id.value;
          data.action = 'add_members';
          data.member_ids = new_name_ids.value;              

          var ajaxUrl = modx_options.rest_url + 'Mailinglists';
          axios.put(ajaxUrl,data)
          .then(function (response) {
              loadMailinglistNames(current_list_id.value);
              addnames_modal.value = false;
          })
          .catch(function (error) {
              console.log(error);
          });

      }
      
      function importNames(){
          if (importlist_id.value == null){
              importlist_modal.value = false;
              return;
          }
      
          //var self = this;
          var data = {};
          
          data.id = current_list_id.value;
          data.action = 'import_members';
          data.importlist_id = importlist_id.value;              

          var ajaxUrl = modx_options.rest_url + 'Mailinglists';
          axios.put(ajaxUrl,data)
          .then(function (response) {
              loadMailinglistNames(current_list_id.value);
              importlist_modal.value = false;
          })
          .catch(function (error) {
              console.log(error);
          });

      }        
      
      function updateMailinglist(item){
          
      for (let key in mailinglist_form.value) {
          mailinglist_form.value[key].value = item[key];
      } 
      //convert number to string for checkbox
      mailinglist_form.value['hidden'].value = mailinglist_form.value['hidden'].value == 0 ? '0' : '1';
      
      mailinglist_modal_title.value = 'Mailingliste bearbeiten';
      mailinglist_modal.value=true;  
      }

      function createMailinglist(){
          
      for (let key in mailinglist_form.value) {
          mailinglist_form.value[key].value = mailinglist_form.value[key].default;
      } 
      mailinglist_modal_title.value = 'Mailingliste erstellen';
      mailinglist_modal.value = true;  

      } 
      
      function loadImportNamesModal(item) {
          var data = {};
          //var self = this;
          var ajaxUrl = modx_options.rest_url + 'Mailinglists';
          //data.iid = this.$route.query.iid;
          //data.code = this.$route.query.code; 
          data.returntype = 'options';
          data.which_page = 'edit_mailinglists';
          data.exclude = item.id;
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
              current_list_id.value = item.id;
              importlist_modal.value = true;                 
              mailinglist_options.value = response.data.results;
              //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
              //self.$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          });            

      }
      
      function saveMailinglist(){

          //var self = this;
          var data = {};
          
          var data = {};
          for (let key in mailinglist_form.value) {
              data[key] = mailinglist_form.value[key].value;
          }  
          if (data.id == 0){
              var ajaxUrl = modx_options.rest_url + 'Mailinglists';
              axios.post(ajaxUrl,data)
              .then(function (response) {
                  mailinglist_modal.value = false;
                  loadMailinglistTypes();
                  loadMailinglists();
              })
              .catch(function (error) {
                  console.log(error);
              });
          } else {
              var ajaxUrl = modx_options.rest_url + 'Mailinglists';
              axios.put(ajaxUrl,data)
              .then(function (response) {
                  mailinglist_modal.value = false;
                  loadMailinglistTypes();
                  loadMailinglists();
              })
              .catch(function (error) {
                  console.log(error);
              });                
          }

      }        
      
      function loadMailinglists(){
          var data = {};
          //var self = this;
          var ajaxUrl = modx_options.rest_url + 'Mailinglists';
          //data.iid = this.$route.query.iid;
          //data.code = this.$route.query.code; 
          data.returntype = 'grouped_by_type';
          data.which_page = 'edit_mailinglists';
          axios.get(ajaxUrl,{params:data})
          .then(function (response) {
              mailinglists.value = response.data.results;
              //make sure, the model gets updated, nested properties, which are not defined in the data, are not watched
              //self.$forceUpdate();
          })
          .catch(function (error) {
              console.log(error);
          });            
      }

      function loadNames(){
          //var self = this;
          
          var ajaxUrl = modx_options.rest_url + 'Names?limit=100000&returntype=options';
          axios.get(ajaxUrl)
          .then(function (response) {
              namesoptions.value = response.data.results;
              filtered_options.value = response.data.results;                 
          })
          .catch(function (error) {
              console.log(error);
          });            
      }

      function hideNamesPopup(){
         //this.$refs.namesselect.updateInputValue('');
         namesselect.value.hidePopup();
         
      }        



        return {
            loadMailinglistNames,
            createMailinglist,
            updateMailinglist,
            loadImportNamesModal,
            importNames,
            saveMailinglist,
            filterFn,
            addNames,
            tab,
            mailinglists,
            mailinglisttypes,
            mailinglistnames,
            filtered_options,
            mailinglist_options,
            namesselect,
            namesoptions,
            fbuchuser,
            new_name_ids,
            importlist_id,
            current_list_id,
            addnames_modal,
            importlist_modal,
            mailinglist_modal,
            mailinglist_modal_title,
            mailinglist_form,
            weekday_options:[
                {label:'Bitte Wochentag wählen',value:''},
                {label:'Mo',value:'1'}, 
                {label:'Di',value:'2'}, 
                {label:'Mi',value:'3'}, 
                {label:'Do',value:'4'}, 
                {label:'Fr',value:'5'}, 
                {label:'Sa',value:'6'}, 
                {label:'So',value:'7'}, 
            ]
         
        }
    },

    template: '#view'
}
