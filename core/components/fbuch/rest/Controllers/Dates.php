<?php

class MyControllerDates extends modRestController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDate';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function get() {
        $pk = $this->getProperty($this->primaryKeyField);
        
        $date = $this->getProperty('date');
        if ($pk == 'new' && !empty($date)){
            return $this->newDate($date);
        }

        if (empty($pk)) {
            return $this->getList();
        }
        return $this->read($pk);
    }    

    public function newDate($date){
        $this->object = $this->modx->newObject($this->classKey);
        if (empty($this->object)) {
            return $this->failure($this->modx->lexicon('rest.err_obj_nf',array(
                'class_key' => $this->classKey,
            )));
        }
        $this->object->set('date',$date);
        $this->object->set('start_time','00:00'); 
        $this->object->set('date_end',$date);
        $this->object->set('end_time','01:30');         
        $objectArray = $this->object->toArray();
                
        return $this->success('',$objectArray);    
    }

    public function put() {
        $deleted = (int) $this->getProperty('deleted');
        if ($deleted == 1) {
            $id = $this->getProperty($this->primaryKeyField,false);
            if (empty($id)) {
                return $this->failure($this->modx->lexicon('rest.err_field_ns',array(
                    'field' => $this->primaryKeyField,
                )));
            }
            $c = $this->getPrimaryKeyCriteria($id);
            $old_deleted = 0;
            if ($object = $this->modx->getObject($this->classKey,$c)) {
                $old_deleted = (int) $object->get('deleted');
            }
            if ($deleted == 1 && $old_deleted == 0){
                if ($this->modx->hasPermission('fbuch_delete_termin') || $object->get('createdby') == $this->modx->user->get('id')) {
                    $this->setProperty('deletedby', $this->modx->user->get('id'));
                    $this->setProperty('deletedon', strftime('%Y-%m-%d %H:%M:%S'));   
                } else {
                    $this->unsetProperty('deleted');
                }
            }
        }       
        parent::put();
    }    

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_delete_termin')) {
            $this->object->set('deletedby', $this->modx->user->get('id'));
            $this->object->set('deletedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_termin')) {
            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPost(array &$objectArray){
        $this->modx->fbuch->checkDateMailinglistNames($this->object->get('id'));
        $this->modx->fbuch->afterCreateDate($this->object);
    }

    public function afterPut(array &$objectArray){
        $this->modx->fbuch->checkDateMailinglistNames($this->object->get('id'));
        $this->modx->fbuch->afterCreateDate($this->object);
    }    

    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_create_termin')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        return true;
    }
    
    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $returntype = $this->getProperty('returntype');
        $show_hidden = $this->getProperty('show_hidden');
        $which_page = $this->getProperty('which_page');

        $joins = '[{"alias":"Type"}]';
        
        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);


     /*   
        if($show_hidden){
            $where = array('deleted' => 0);
        }else{
            $where = array('deleted' => 0,'hidden' => 0);    
        }
        $w = array();
        switch ($which_page){
            case 'edit_mailinglists':
            $w[] = array('member_filter_id' => 0);
            break;
        }
        
        
        $w[] = $where;
        $c->where($w);
        
        $c->groupby('type');
        */
        $c->where(['deleted' => 0]);
        if (isset($_GET['types']) && !empty($_GET['types'])){
            $types = explode(',',$_GET['types']);
            $c->where(['type:IN' => $types]);
        }

        if (isset($_GET['parent']) && !empty($_GET['parent'])){
            $c->where(['parent' => (int) $_GET['parent']]);
        }        
        
        if (isset($_GET['start']) && isset($_GET['end'])){
            $start = $this->getProperty('start');
            $end = $this->getProperty('end');
            $c->where(['date_end:>=' => $start]);
            $c->where(['date:<=' => $end]);

        } elseif (isset($_GET['start'])) {
            $start = $this->getProperty('start');
            $c->where(['date:>=' => $start]);
        } else {
            $date = strftime('%Y-%m-%d 00:00:00');
            $c->where(['date:>=' => $date]);
        }
        $c->sortby('date','ASC');
        $c->sortby('start_time','ASC');
        //$c->prepare();echo $c->toSql();
        return $c;
        
    }
    
    protected function prepareListObject(xPDOObject $object) {
        $date = $object->toArray();
        $date['Type_colorstyle'] = (string) $date['Type_colorstyle'] == '' ? 'grey' : $date['Type_colorstyle'];
        return $date;
    } 
       

}
