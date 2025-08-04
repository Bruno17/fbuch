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
    const skillgrades = ref([]);
    const leveloptions = ref([]);
    const competency_levels = ref([]);
    const competency_levels2 = ref([]);
    const competency_levels_string = ref('');
    const bootsnutzergruppen = ref([]); 
    form.value.selected=['B:2:3','B:1:3'];

    onMounted(() => {
        loadTree();
        loadCompetencyLevelOptions();
        loadSkillGrades();
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

    function loadSkillGrades(){
        const data = {};
        const ajaxUrl = modx_options.rest_url + 'SkillGrades';
        axios.get(ajaxUrl,{params:data})
        .then(function (response) {
           skillgrades.value = response.data.results.grades || [];
        })
        .catch(function (error) {
            console.log(error);
        }); 
    }     
    
        function onloadCompetencyLevels(options){
            let new_options = [];
            let option = {};
            for (let i = 0;i < options.length;i++){
                for (let ii = 0;ii < options[i].importances.length;ii++){
                    option = {};
                    option.group = options[i].name + ' (' + options[i].level +')' ;
                    option.label = options[i].importances[ii].label;
                    option.value = options[i].level + ':' + options[i].importances[ii].value; 
                    new_options.push(option); 

                    /*
                    new_options = [];
                    for (let gi = 0;gi < options[i].grades.length;gi++){
                       if (options[i].grades[gi].selectable=='1'){
                            option = {};
                            option.group = options[i].name + ' (' + options[i].level +')' + ' - ' + options[i].importances[ii].label + ' (' + options[i].importances[ii].value +')' ;
                            option.label = options[i].grades[gi].label + ' (' + options[i].grades[gi].value +') ' + options[i].importances[ii].label;
                            option.value = options[i].level + ':' + options[i].importances[ii].value + ':' + options[i].grades[gi].value; 
                            new_options.push(option);                            
                        }
                    } 
                    */                       
                }
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
        
      function editSkill(id,category_id){
            if (id=='new'){
                form.value={id:0,category_id:category_id};
                competency_levels.value=[];
                competency_levels2.value=[];
                competency_levels_string.value='';
                state.value.dialog_skill=true; 
                return;               
            }

            var data = {};
            var ajaxUrl = modx_options.rest_url + 'CompetencyLevelSkills/' +id;
            axios.get(ajaxUrl,{params:data})
            .then(function (response) {
                form.value = response.data.object;
                let levels = form.value.levels.split(',');
                competency_levels_string.value = form.value.levels; 
                competency_levels.value = levels[0]=='' ? [] : levels;
                competency_levels2.value = competency_levels.value;
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
        
        function removeSkillGrade(id){
            var ajaxUrl = modx_options.rest_url + 'MemberSkills/' +id;
 
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

        function onUpdateCompetencyLevels(value){
            console.log('onUpdateCompetencyLevels',value);
            competency_levels.value=value;
            competency_levels_string.value=value.join(',');
        }

        function onPasteCompetencyLevel(value){
            console.log('onPasteCompetencyLevel',value); 
            competency_levels.value=value.split(','); 
            competency_levels2.value=value.split(','); 

        }
        


    return { 
        resourcetree,
        deleteCategory,
        moveCategory,
        moveSkill,
        leveloptions,
        skillgrades,
        loadTree,
        onloadCompetencyLevels,
        onPasteCompetencyLevel,
        removeSkillGrade,
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
            competency_levels2,
            competency_levels_string,
            onUpdateCompetencyLevels,
            props
    }
  },
  template: '#skills_category_tree'
  // or `template: '#my-template-element'`
}
