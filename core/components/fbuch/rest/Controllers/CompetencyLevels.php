<?php

include 'BaseController.php';
include 'traits/skillcategories.trait.php'; 

class MyControllerCompetencyLevels extends BaseController {
    use SkillcategoriesTrait;

    public $classKey = 'fbuchCompetencyLevel';
    public $defaultSortField = 'level';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePost() {
        throw new Exception('Unauthorized', 401);
    }

    public function verifyAuthentication() {
        //keine bestimmte Berechtigung benötigt
        return true;
    }

    public function getSkillCategories(){
        $categories = $this->getNodes(0,false);
        $parents = [];
        $allcategories = [];
        foreach ($categories as $category){
            if (is_array($category['children'])){
                foreach ($category['children'] as $child){
                    $parents[$child['id']] = $child['parent'];
                }
            }
        }
        $this->setProperty('SkillCategoryParents',$parents);
    }

    public function prepareSkillsTree($nodes) {
        $skillgrades = $this->getProperty('skillgrades',[]);
        $categories = $this->getNodes(0,false);
        $tree = [];
        foreach ($nodes as $levelname => $node){
            foreach ($categories as $category_key => $category){
                if (isset($node[$category['id']])){
                    $category_node = $node[$category['id']];
                    $tree[$levelname][$category_key]['category']=$category['name'];
                    if (is_array($skillgrades['importances'])){
                        foreach ($skillgrades['importances'] as $importance){
                            if (isset($importance['value']) && isset($category_node[$importance['value']])){
                                $tree[$levelname][$category_key]['nodes'][$importance['value']]['label'] = $importance['label']; 
                                $importance_node = $category_node[$importance['value']];
                                if (is_array($category['children'])){
                                    foreach ($category['children'] as $subcategory_key => $subcategory){
                                        if (isset($importance_node[$subcategory['id']])){
                                            $tree[$levelname][$category_key]['nodes'][$importance['value']]['nodes'][$subcategory_key]['category'] = $subcategory['name'];
                                            if (is_array($importance_node[$subcategory['id']])){
                                                foreach ($importance_node[$subcategory['id']] as $skill_key => $skill){
                                                    $tree[$levelname][$category_key]['nodes'][$importance['value']]['nodes'][$subcategory_key]['nodes'][$skill_key] = $skill;    
                                                }
                                            }
                                        }
                                    }
                                } 
                            }
                        }
                    }
                }            
            }
        }

        //$tree = $nodes;
        //print_r($tree);
        return $tree;
    }

    public function getSkills(){
        $parents = $this->getProperty('SkillCategoryParents',[]);
        $grades_by_value = $this->getProperty('grades_by_value',[]);
        $nodes = false;
        $classKey = 'fbuchCompetencyLevelSkill';
        $c = $this->modx->newQuery($classKey);
        $c->select($this->modx->getSelectColumns($classKey, $classKey, ''));
        $c->sortby('pos','ASC');
        $c->sortby('id','ASC');        
        if ($collection = $this->modx->getCollection($classKey,$c)){
            $nodes = [];
            $idx = 1;
            foreach ($collection as $object){
                $node = $object->toArray();
                $levels = explode(',',$object->get('levels'));
                if (is_array($levels)){
                    foreach ($levels as $levelstring){
                        $level = explode(':',$levelstring);
                        if (is_array($level)){
                            $levelname = isset($level[0]) ? $level[0] : '' ;
                            $importance = isset($level[1]) ? $level[1] : '' ;
                            if (isset($level[2])){
                                $grade =  $level[2] ;
                                $node['grade'] = $grade;
                                if (isset($grades_by_value[$grade])){
                                    $node['grade_label'] = isset($grades_by_value[$grade]['label']) ? $grades_by_value[$grade]['label'] : ''; 
                                    $node['grade_icon'] = isset($grades_by_value[$grade]['icon']) ? $grades_by_value[$grade]['icon'] : '';                                      
                                }
                            }
                        
                            $nodes[$levelname][$parents[$object->get('category_id')]][$importance][$object->get('category_id')][$idx] = $node;     
                            $idx++;                        
                        }
                     
                    }
                }
            }
        }

        $skillstree = $this->prepareSkillsTree($nodes);

        return $skillstree;
    } 

    public function getList(){
        $this->modx->fbuch->getChunkName('fbuch_skill_grades');
        $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $skillgrades = json_decode($this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades')),1);
        $this->setProperty('skillgrades',$skillgrades);
        $grades_by_value = [];
        if (is_array($skillgrades['grades'])){
            foreach($skillgrades['grades'] as $grade){
                $grades_by_value[$grade['value']] = $grade;
            }
        }
        $this->setProperty('grades_by_value',$grades_by_value);

        $this->getSkillCategories();
        $levelskills = $this->getSkills();
        $this->setProperty('levelskills',$levelskills);

        return parent::getList();
    }


    
    
    
    protected function prepareListObject(xPDOObject $object) {
        $skillgrades = $this->getProperty('skillgrades',[]);
        $levelskills = $this->getProperty('levelskills',[]);
        $output = $object->toArray();
        $level = $object->get('level');
        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['importances'] = [];
                $output['grades'] = [];
                if (is_array($skillgrades['competency_levels_select']) && in_array($object->get('level'),$skillgrades['competency_levels_select'])){
                    $output['importances'] = is_array($skillgrades['importances']) ? $skillgrades['importances'] : [];
                }
                if (is_array($skillgrades['competency_levels_select']) && in_array($object->get('level'),$skillgrades['competency_levels_select'])){
                    $output['grades'] = is_array($skillgrades['grades']) ? $skillgrades['grades'] : [];
                }                
                $output['label'] = $object->get('name'). ' (' . $object->get('level') . ')';
                $output['value'] = $object->get('level');
                break;
                default:
                $permissions = $object->get('permissions');
                $output['permissions'] = json_decode($permissions,true);
                $output['skills'] = isset($levelskills[$level]) ? $levelskills[$level] : [];
                break;
        }

        return $output;
    }    

}