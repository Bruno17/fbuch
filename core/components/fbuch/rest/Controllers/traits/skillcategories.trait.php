<?php

trait SkillcategoriesTrait {

    public function getNodes($parent,$addSkills=true){
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

                if ($children = $this->getNodes($object->get('id'),$addSkills)){
                    $node['haschildren'] = 1;
                    $node['children'] = $children;
                }
                if ($addSkills){
                    $node['hasskills'] = 0; 
                    if ($children = $this->addSkills($object->get('id'))){
                        $node['hasskills'] = 1;
                        $node['skills'] = $children;
                    }                    
                }

                $prevobject = $object;                      
                $nodes[$idx] = $node;  
                $idx++;               
            }
            
        }

        return $nodes;
    } 
    
    public function getChildren($parent){
        $classKey = 'fbuchCompetencyLevelSkillsCategory';
        $c = $this->modx->newQuery($classKey);
        $c->where(['parent'=>$parent]);
        $c->sortby('pos');
        $c->sortby('id');
        $collection = $this->modx->getCollection($classKey,$c);
        return $collection;        
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
                "on"=>"MemberSkill.grade!=9 AND MemberSkill.skill_id=fbuchCompetencyLevelSkill.id AND MemberSkill.member_id=$member_id AND MemberSkill.createdby=$user_id"
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
                $node['MemberSkill_icon'] = $this->getGradeIcon($node['MemberSkill_grade']);
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
    
    public function getGradeIcon($grade){
        $grade = empty($grade) ? '0' : $grade;
        $gradeicons = $this->getProperty('gradeicons',[]);
        return is_array($gradeicons) && isset($gradeicons[$grade]) ? $gradeicons[$grade] : '';        

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
 
        $c->sortby('createdon','DESC');

        //$c->prepare();echo $c->toSql();

        if ($collection = $this->modx->getCollection($classKey,$c)){
            $nodes = [];
            foreach ($collection as $object){
                $node = $object->toArray();
                if (empty($node['CreatorMember_id'])){
                    $node['CreatorMember_firstname'] = $node['CreatorProfile_fullname'];
                }
                $node['icon'] = $this->getGradeIcon($node['grade']);
                $nodes[] = $node;
            }
        }

        return $nodes;        
    }    

}