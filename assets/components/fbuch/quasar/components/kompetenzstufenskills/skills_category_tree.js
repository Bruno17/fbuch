import { useLoadPermissions,useHasPermission } from "../../composables/helpers.js";
import api_select from '../../components/api_select_multiple.js';
import groupselect from '../../components/groupselect.js';


export default {
    props:{
      member:null,
      moveable:null,
      editable:null
    },   
  components: {
    api_select: api_select,
    groupselect: groupselect
  },  
  setup(props) {
    const {onMounted, ref, watch } = Vue;
    const { useQuasar } = Quasar;
    const $q = useQuasar();    
    const resourcetree = ref([]);
    const state = ref({});
    const form = ref({});
    const levels = ref([]);
    const leveloptions = ref([]);
    const competency_levels = ref([]);
    const bootsnutzergruppen = ref([]); 
    form.value.selected=['B:2:3','B:1:3'];

    onMounted(() => {
        loadTree();
        loadCompetencyLevelOptions();
    })

    function formatDate(date){
        return Quasar.date.formatDate(date, 'dd DD. MMMM YYYY')
    }

    watch(() => props.member, (value) => {
        loadTree();
    })    

    function loadTree(){
        const data = {};
        const member = props.member || {};
        data.member_id = member.id;
        data.resource_id = modx_options.resource_id;
        const ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           resourcetree.value = response.data.results;
        })
        .catch(function (error) {
            console.log(error);
        }); 
    }

    function loadCompetencyLevelOptions(){
        const data = {};
        const ajaxUrl = modx_options.rest_url + 'CompetencyLevels?limit=100000&returntype=options';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           leveloptions.value = onloadCompetencyLevels(response.data.results);
        })
        .catch(function (error) {
            console.log(error);
        }); 
    } 
    
        function onloadCompetencyLevels(options){
            let new_options = [];
            for (let i = 0;i < options.length;i++){
                let option = {};
                let option2 = {};
                let option3 = {};
                let option4 = {};                
                option.group = options[i].name + '- wünschenswert';
                option.label = 'akzeptabel';
                option.value = options[i].level + ':2:3'; 
                new_options.push(option);
                option2.group = options[i].name + '- wünschenswert';
                option2.label = 'perfekt';
                option2.value = options[i].level + ':2:1'; 
                new_options.push(option2);                
                option3.group = options[i].name + '- erforderlich';
                option3.label = 'akzeptabel';
                option3.value = options[i].level + ':1:3'; 
                new_options.push(option3);
                option4.group = options[i].name + '- erforderlich';
                option4.label = 'perfekt';
                option4.value = options[i].level + ':1:1'; 
                new_options.push(option4);                                   
            }
            return new_options;
        }    


    
    
      function editCategory(id){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories/' +id;
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                form.value = response.data.object;
                state.value.dialog_category = true;
            })
            .catch(function (error) {
                console.log(error);
            });                
        }
        
      function editSkill(id){
            var data = {};
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills/' +id;
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                var selected = ["B:2:3","B:1:3"];
                response.data.object.selected = selected;
                form.value = response.data.object;
                let levels = form.value.levels.split(',');
                competency_levels.value = levels[0]=='' ? [] : levels;
                state.value.dialog_skill = true;
            })
            .catch(function (error) {
                console.log(error);
            });                
        }              
        
        
        function updateCategory(){
            var data = {};
            data = form.value;

            if (data.id>0){
                var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories/' +data.id;
                axios.put(ajaxUrl,data)
                .then(function (response) {
                    //prepareNutzergruppen(response.data.results);
                    //loadCompetencyLevels();
                    loadTree();
                })
                .catch(function (error) {
                    console.log(error);
                });               
            } else {
                var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories';
    
                axios.post(ajaxUrl,data)
                .then(function (response) {
                    //prepareNutzergruppen(response.data.results);
                    //loadCompetencyLevels();
                    loadTree();
                })
                .catch(function (error) {
                    console.log(error);
                }); 
            }

           
        } 
        
        function updateSkill(){
            var data = {};
            data = form.value;
            data.levels = competency_levels.value.join(',');

            if (data.id>0){
                var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills/' +data.id;
                axios.put(ajaxUrl,data)
                .then(function (response) {
                    //prepareNutzergruppen(response.data.results);
                    //loadCompetencyLevels();
                    loadTree();
                })
                .catch(function (error) {
                    console.log(error);
                });               
            } else {
                var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills';
    
                axios.post(ajaxUrl,data)
                .then(function (response) {
                    //prepareNutzergruppen(response.data.results);
                    //loadCompetencyLevels();
                    loadTree();
                })
                .catch(function (error) {
                    console.log(error);
                }); 
            }

           
        }
        
        function updateSkillGrade(skill_id,grade){
            var data = {};
            data.grade = grade || null;
            data.skill_id = skill_id;
            data.member_id = props.member.id;
            if (form.value.skill_id){
                data.note = form.value.note || '';  
            }
            

            var ajaxUrl = modx_options.rest_url + 'MemberSkills';

            axios.post(ajaxUrl,data)
            .then(function (response) {
                 loadTree();
            })
            .catch(function (error) {
                console.log(error);
            }); 
            
        }        
        
        function deleteSkill(id){
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills/' +id;
 
            axios.delete(ajaxUrl)
            .then(function (response) {
                //prepareNutzergruppen(response.data.results);
                //loadCompetencyLevels();
                loadTree();
            })
            .catch(function (error) {
                console.log(error);
            });            
        } 
        
        function moveCategory(id,direction,parent) {
            var data = {
              id:id,
              direction:direction,
              processaction:'move_pos'
            };
            if (direction == 'category'){
              data = {
                  parent:parent,
                  pos:10000  
              };              
            }            
                  
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories/' +id;

            axios.put(ajaxUrl,data)
            .then(function (response) {
                //prepareNutzergruppen(response.data.results);
                //loadCompetencyLevels();
                loadTree();
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }

        function moveSkill(id,direction,category_id) {
            var data = {
              id:id,
              direction:direction,
              processaction:'move_pos'
            };
            if (direction == 'category'){
              data = {
                  category_id:category_id,
                  pos:10000  
              };              
            }
                  
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills/' +id;

            axios.put(ajaxUrl,data)
            .then(function (response) {
                //prepareNutzergruppen(response.data.results);
                //loadCompetencyLevels();
                loadTree();
            })
            .catch(function (error) {
                console.log(error);
            }); 
        }        
    
        function deleteCategory(category){
            const id = category.id;
            if (category.hasskills==1 || category.haschildren==1){
              $q.dialog({
                title: 'Achtung',
                message: 'Nur leere Kategorien können gelöscht werden!'
              })
              return;
            }

            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkillsCategories/' +id;
 
            axios.delete(ajaxUrl)
            .then(function (response) {
                //prepareNutzergruppen(response.data.results);
                //loadCompetencyLevels();
                loadTree();
            })
            .catch(function (error) {
                console.log(error);
            });            
        } 
        


    return { 
        resourcetree,
        deleteCategory,
        moveCategory,
        moveSkill,
        leveloptions,
        loadTree,
        onloadCompetencyLevels,
            state,
            form,
            levels,
            bootsnutzergruppen,
            useHasPermission,
            updateCategory,
            editCategory,
            updateSkill,
            editSkill, 
            deleteSkill,
            updateSkillGrade,
            formatDate,
            competency_levels,
            props
    }
  },
  template: '#skills_category_tree'
  // or `template: '#my-template-element'`
}
