<?php

include 'BaseController.php';

class MyControllerMailinglists extends BaseController {
    public $classKey = 'fbuchMailinglist';
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        $action = $this->getProperty('action');
        if ($this->modx->hasPermission('fbuch_edit_mailinglists')) {
            switch ($action) {
                case 'add_members':
                case 'import_members':
                    break;
                default:
                    $this->object->set('editedby', $this->modx->user->get('id'));
                    $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S'));
                    break;
            }

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPut(array & $objectArray) {
        $action = $this->getProperty('action');
        $list_id = $this->object->get('id');
        switch ($action) {
            case 'import_members':
                $importlist_id = $this->getProperty('importlist_id');
                $member_ids = [];
                $c = $this->modx->newQuery('fbuchMailinglistNames');
                $c->where(array('list_id' => $importlist_id));
                if ($collection = $this->modx->getCollection('fbuchMailinglistNames',$c)){
                    foreach ($collection as $object){
                        $member_ids[] = $object->get('member_id');
                    }
                    $this->addMembers($list_id,$member_ids);
                }
                break;
            case 'add_members':
                $member_ids = $this->getProperty('member_ids');
                $this->addMembers($list_id,$member_ids);
                break;
            default:
                break;
        }
    }

    public function addMembers($list_id,$member_ids){
        if (is_array($member_ids)){
            foreach ($member_ids as $member_id){
                if ($name_o = $this->modx->getObject('fbuchMailinglistNames',array('list_id'=>$list_id,'member_id'=>$member_id))){
                    
                } else {
                    $name_o = $this->modx->newObject('fbuchMailinglistNames');
                    $name_o->set('list_id',$list_id);
                    $name_o->set('member_id',$member_id);
                    $name_o->set('createdby',$this->modx->user->get('id'));
                    $name_o->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
                    $name_o->save();
                }
            }
        }
        //add all members also to fbuchDateInvited
        $this->modx->fbuch->updateDatesMailinglistNames($list_id);
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_create_mailinglists')) {
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
        $which_page = $this->getProperty('which_page');
        $exclude = $this->getProperty('exclude');
        
        $where = array('deleted' => 0);

        switch ($returntype) {
            case 'options':
                break;
            default:
            if ($fbuchUser = $this->getCurrentFbuchUser()) {
                $joins = '[{"alias":"Names","on":"list_id=fbuchMailinglist.id and member_id=' . $fbuchUser->get('id') . '"}]';
                $this->modx->migx->prepareJoins($this->classKey, json_decode($joins, 1), $c);
            }
                break;
        }        

        $w = array();
        switch ($which_page){
            case 'edit_mailinglists':
            $w[] = array('member_filter_id' => 0);
            break;
        } 
        
        if (!empty($exclude)){
            $w[] = array('id:!=' => $exclude);
        }
        
        $w[] = $where;
        $c->where($w);
        $c->sortby('type');
        $c->sortby('weekday');
        $c->sortby('time');

        //$c->prepare();echo $c->toSql();
        return $c;
    }

    public function getList() {
        $this->getProperties();
        $returntype = $this->getProperty('returntype');
        $c = $this->modx->newQuery($this->classKey);
        //$c = $this->addSearchQuery($c);
        $c = $this->prepareListQueryBeforeCount($c);
        $total = $this->modx->getCount($this->classKey, $c);
        $alias = !empty($this->classAlias) ? $this->classAlias : $this->classKey;
        $c->select($this->modx->getSelectColumns($this->classKey, $alias));
        //$c->select(array('id','type'));
        $c = $this->prepareListQueryAfterCount($c);
        //$c->sortby('type');
        $c->sortby($this->getProperty($this->getOption('propertySort', 'sort'), $this->defaultSortField), $this->getProperty($this->getOption('propertySortDir', 'dir'), $this->defaultSortDirection));
        $limit = $this->getProperty($this->getOption('propertyLimit', 'limit'), $this->defaultLimit);
        if (empty($limit))
            $limit = $this->defaultLimit;
        $c->limit($limit, $this->getProperty($this->getOption('propertyOffset', 'start'), $this->defaultOffset));
        $objects = $this->modx->getCollection($this->classKey, $c);
        if (empty($objects))
            $objects = array();
        $list = array();
        /**
         *  * @var xPDOObject $object */
        foreach ($objects as $object) {
            switch ($returntype) {
                case 'grouped_by_type':
                    $list[$object->get('type')][] = $this->prepareListObject($object);
                    break;
                default:
                    $list[] = $this->prepareListObject($object);
                    break;
            }


        }
        return $this->collection($list, $total);
    }

    protected function prepareListObject(xPDOObject $object) {
        $returntype = $this->getProperty('returntype');

        $objectArray = $object->toArray();

        switch ($returntype) {
            case 'options':
                $objectArray = array();
                $objectArray['label'] = $object->get('name');
                $objectArray['value'] = $object->get('id');               
                break;
            default:
                $member_id = $object->get('Names_id');
                $name_subscribed = $object->get('Names_subscribed');
                $name_unsubscribed = $object->get('Names_unsubscribed');
        
                $objectArray['Names_active'] = empty($member_id) ? false : true;
                $objectArray['Names_subscribed'] = empty($name_subscribed) ? false : true;
                $objectArray['Names_unsubscribed'] = empty($name_unsubscribed) ? false : true;
                break;
        }

 


        return $objectArray;
    }

}
