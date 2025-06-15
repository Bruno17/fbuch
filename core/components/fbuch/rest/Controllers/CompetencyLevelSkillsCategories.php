<?php

include 'BaseController.php';

class MyControllerCompetencyLevelSkillsCategories extends BaseController {
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

    public function addSkillGrades($skill_id){
        $fbuchMember = $this->getCurrentFbuchMember();
        $member_id = $this->getProperty('member_id',0);
        $user_id = $this->modx->user->get('id');
        $nodes = false;

        if ($member_id==$fbuchMember->get('id') || $this->modx->hasPermission('mv_edit_membercompetencies')) {

        } else {
            return false;
        }

        $classKey = 'mvMemberSkill';
        $c = $this->modx->newQuery($classKey);
        $c->select($this->modx->getSelectColumns($classKey, $classKey, ''));
        $joins = [["alias"=>"CreatorMember","selectfields"=>"id,firstname,name"],
                  ["alias"=>"CreatorProfile","selectfields"=>"id,fullname"]];
        $this->modx->migx->prepareJoins($classKey, $joins , $c);  
                    
        $c->where(['member_id'=>$member_id,'skill_id'=>$skill_id]);
 
        $c->sortby('createdby');

        //$c->prepare();echo $c->toSql();

        if ($collection = $this->modx->getCollection($classKey,$c)){
            $nodes = [];
            foreach ($collection as $object){
                $node = $object->toArray();
                if (empty($node['CreatorMember_id'])){
                    $node['CreatorMember_firstname'] = $node['CreatorProfile_fullname'];
                }
                $nodes[] = $node;
            }
        }

        return $nodes;        
    }

    public function addSkills($category){
        $fbuchMember = $this->getCurrentFbuchMember();
        $member_id = $this->getProperty('member_id',0);
        $user_id = $this->modx->user->get('id');
        $nodes = false;
        $classKey = 'fbuchCompetencyLevelSkill';
        $c = $this->modx->newQuery($classKey);
        $c->select($this->modx->getSelectColumns($classKey, $classKey, ''));
        if (!empty($member_id) && ($member_id==$fbuchMember->get('id') || $this->modx->hasPermission('mv_edit_membercompetencies'))){
            $joins = [["alias"=>"MemberSkill","classname"=>"mvMemberSkill",
                "on"=>"MemberSkill.skill_id=fbuchCompetencyLevelSkill.id AND MemberSkill.member_id=$member_id AND MemberSkill.createdby=$user_id"
                ]];
            //print_r($joins);
            $this->modx->migx->prepareJoins($classKey, $joins , $c);  
                    
        }
        $c->where(['category_id'=>$category]);
 
        $c->sortby('pos');
        $c->sortby('fbuchCompetencyLevelSkill.id');        
        //die();  
        //$c->prepare();echo $c->toSql(); 
        //die();       
        if ($collection = $this->modx->getCollection($classKey,$c)){
            $nodes = [];
            //$count = 0;
            $count = count($collection);
            $idx = 1;            
            foreach ($collection as $object){
                $node = $object->toArray();
                $node['_first'] = $idx==1 ? 1 : 0;
                $node['_last'] = $idx==$count ? 1 : 0;
                $node['_idx'] = $idx; 
                if (!empty($member_id) && $grades=$this->addSkillGrades($object->get('id'))){
                    $node['hasgrades'] = 1;
                    $node['grades'] = $grades;
                }
                
                $nodes[] = $node; 
                $idx++;              
            }
        }

        return $nodes;
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
            $this->movePos();
        } else {
            parent::put();    
        }       
        
    } 
    
    public function getChildren($parent){
        $c = $this->modx->newQuery($this->classKey);
        $c->where(['parent'=>$parent]);
        $c->sortby('pos');
        $c->sortby('id');
        $collection = $this->modx->getCollection($this->classKey,$c);
        return $collection;        
    }

    public function getNodes($parent){
       $nodes = false;
        
        if ($collection = $this->getChildren($parent)){
            $nodes = [];
            $count = count($collection);
            $idx = 1;
            $prevobject = null;
            foreach ($collection as $object){
                $node = $object->toArray();
                $node['_first'] = $idx==1 ? 1 : 0;
                $node['_last'] = $idx==$count ? 1 : 0;
                $node['_idx'] = $idx;  
                $node['haschildren'] = 0;
                $node['_next'] = 0;
                $node['_prev'] = $prevobject ? $prevobject->get('id') : 0; 
                if ($idx > 1){
                     $nodes[$idx-1]['_next'] = $node['id'];   
                }

                if ($children = $this->getNodes($object->get('id'))){
                    $node['haschildren'] = 1;
                    $node['children'] = $children;
                }
                $node['hasskills'] = 0; 
                if ($children = $this->addSkills($object->get('id'))){
                    $node['hasskills'] = 1;
                    $node['skills'] = $children;
                }
                $prevobject = $object;                      
                $nodes[$idx] = $node;  
                $idx++;               
            }
            
        }

        return $nodes;
    }     
    
    public function getList() {
        $total = 0;

        $list = $this->getNodes(0);

        $list = $list ? $list : [];
 
        return $this->collection($list,$total);
    }       

}