<?php

include 'BaseController.php';
include 'traits/skillcategories.trait.php'; 

class MyControllerCompetencyLevelSkillsCategories extends BaseController {
    use SkillcategoriesTrait;

    public $classKey = 'fbuchCompetencyLevelSkillsCategory';
    public $defaultSortField = 'pos';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('mv_edit_membercompetencies')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {
        if ($this->modx->hasPermission('mv_edit_membercompetencies')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {
        if ($this->modx->hasPermission('mv_edit_membercompetencies')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        //keine bestimmte Berechtigung benötigt
        return true;
    }
    
    
    protected function prepareListObject(xPDOObject $object) {
        
        $output = $object->toArray();
        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['label'] = $object->get('name'). ' (' . $object->get('level') . ')';
                $output['value'] = $object->get('level');
                break;
                default:
                $permissions = $object->get('permissions');
                $output['permissions'] = json_decode($permissions,true);
                break;
        }

        return $output;
    }

    public function movePos(){
            $id = $this->getProperty($this->primaryKeyField,false);
            $direction = $this->getProperty('direction');
            if (empty($id)) {
                return $this->failure($this->modx->lexicon('rest.err_field_ns',array(
                    'field' => $this->primaryKeyField,
                )));
            }
            $c = $this->getPrimaryKeyCriteria($id);

            if ($object = $this->modx->getObject($this->classKey,$c)) {
                if ($categories = $this->getChildren($object->get('parent'))){
                    $pos = 1;
                    $prev_id = 0;
                    $prev_prev_id = 0;
                    $before_id = 0;
                    $after_next = false;
                    if ($direction == 'up'){
                        foreach ($categories as $category){
                            $prev_prev_prev_id = $prev_prev_id;
                            $prev_prev_id = $prev_id;
                            $prev_id = $category->get('id');
                            if ($category->get('id') == $object->get('id')){
                                $before_id = $prev_prev_id;    
                            }                                 
                        }
                        if ($before_id == 0){
                            $object->set('pos',$pos);
                            $object->save();
                            $pos++;
                            print_r($object->toArray());                            
                        }
                    }

                    foreach ($categories as $category){

                        if ($direction == 'up' && $before_id == $category->get('id')){
                            $object->set('pos',$pos);
                            $object->save();
                            $pos++;
                            print_r($object->toArray());                            
                        }                        
                        
                        if ($category->get('id') != $object->get('id')) {
                            $category->set('pos',$pos);
                            $category->save();
                            $pos++;
                            print_r($category->toArray());
                        }

                        if ($after_next){
                            $object->set('pos',$pos);
                            $object->save();
                            $pos++;
                            $after_next = false;
                            print_r($object->toArray());
                        } 
                        

                        if ($direction == 'down' && $category->get('id') == $object->get('id')){
                             $after_next = true;
                        } 
 
                    }

                    if ($after_next){
                        $object->set('pos',$pos);
                        $object->save();
                        $pos++;
                        $after_next = false;
                        print_r($object->toArray());
                    }                     
                }
            }
 
    }
    
    public function put() {
        $processaction = $this->getProperty('processaction');

        if ($processaction == 'move_pos') {
            $this->beforePut();
            $this->movePos();
        } else {
            parent::put();    
        }       
        
    } 
    
  
    
    public function getList() {
        $this->modx->fbuch->getChunkName('fbuch_skill_grades');
        $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $skillgrades_json = $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $skillgrades = json_decode($skillgrades_json,1);
        $this->setProperty('skillgrades',$skillgrades);
        $gradeicons = []; 
        if (is_array($skillgrades) && is_array($skillgrades['grades'])){
            foreach($skillgrades['grades'] as $grade_item){
                $gradeicons[$grade_item['value']] = $grade_item['icon']; 
            }
        } 
        $this->setProperty('gradeicons',$gradeicons);       

        $total = 0;

        $list = $this->getNodes(0);

        $list = $list ? $list : [];
 
        return $this->collection($list,$total);
    }       

}