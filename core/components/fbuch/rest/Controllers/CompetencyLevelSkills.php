<?php

include 'BaseController.php';

class MyControllerCompetencyLevelSkills extends BaseController {
    public $classKey = 'fbuchCompetencyLevelSkill';
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

    public function getSisters($category_id){
        $c = $this->modx->newQuery($this->classKey);
        $c->where(['category_id'=>$category_id]);
        $c->sortby('pos');
        $c->sortby('id');
        $collection = $this->modx->getCollection($this->classKey,$c);
        return $collection;        
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
                if ($sisters = $this->getSisters($object->get('category_id'))){
                    $pos = 1;
                    $prev_id = 0;
                    $prev_prev_id = 0;
                    $before_id = 0;
                    $after_next = false;
                    if ($direction == 'up'){
                        foreach ($sisters as $sister){
                            $prev_prev_prev_id = $prev_prev_id;
                            $prev_prev_id = $prev_id;
                            $prev_id = $sister->get('id');
                            if ($sister->get('id') == $object->get('id')){
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

                    foreach ($sisters as $sister){

                        if ($direction == 'up' && $before_id == $sister->get('id')){
                            $object->set('pos',$pos);
                            $object->save();
                            $pos++;
                            print_r($object->toArray());                            
                        }                        
                        
                        if ($sister->get('id') != $object->get('id')) {
                            $sister->set('pos',$pos);
                            $sister->save();
                            $pos++;
                            print_r($sister->toArray());
                        }

                        if ($after_next){
                            $object->set('pos',$pos);
                            $object->save();
                            $pos++;
                            $after_next = false;
                            print_r($object->toArray());
                        } 
                        

                        if ($direction == 'down' && $sister->get('id') == $object->get('id')){
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


}