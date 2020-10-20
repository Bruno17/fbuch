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
                    break;
                default:
                    $this->setProperty('editedby', $this->modx->user->get('id'));
                    $this->setProperty('editedon', strftime('%Y-%m-%d %H:%M:%S'));
                    break;
            }

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function afterPut(array & $objectArray) {
        $action = $this->getProperty('action');
        switch ($action) {
            case 'add_members':
                $member_ids = $this->getProperty('member_ids');
                $list_id = $this->object->get('id');
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
                                
                
                break;
            default:
                break;
        }


    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_create_mailinglists')) {
            $this->setProperty('createdby', $this->modx->user->get('id'));
            $this->setProperty('createdon', strftime('%Y-%m-%d %H:%M:%S'));

        } else {
            throw new Exception('Unauthorized', 401);
        }


        return !$this->hasErrors();
    }

    public function verifyAuthentication() {
        if ($fbuchUser = $this->getCurrentFbuchUser()) {
            return true;
        }
        return false;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
        $returntype = $this->getProperty('returntype');
        $which_page = $this->getProperty('which_page');
        
        $where = array('deleted' => 0);
        /*
        $datewhere = array();
        switch ($returntype) {
        case 'open':
        $this->setProperty('dir','ASC');
        $where['km'] = 0;
        $datewhere['date:<='] = strftime('%Y-%m-%d 23:59:59');
        $datewhere['start_time:<='] = strftime('%H:%M');
        $datewhere['OR:date:<'] = strftime('%Y-%m-%d 00:00:00');
        break;
        case 'sheduled':
        $this->setProperty('dir','ASC');
        $where['km'] = 0;
        $where['date:>='] = strftime('%Y-%m-%d 00:00:00');
        
        $datewhere['date:>='] = strftime('%Y-%m-%d 00:00:00');
        $datewhere['start_time:>'] = strftime('%H:%M');
        $datewhere['OR:date:>'] = strftime('%Y-%m-%d 23:59:00');                
        
        break;                
        case 'finished':
        $this->setProperty('dir','DESC');
        $where['km:>'] = 0;
        break;                
        } 
        */
        if ($fbuchUser = $this->getCurrentFbuchUser()) {
            $joins = '[{"alias":"Names","on":"list_id=fbuchMailinglist.id and member_id=' . $fbuchUser->get('id') . '"}]';
        }


        $this->modx->migx->prepareJoins($this->classKey, json_decode($joins, 1), $c);

        $w = array();
        switch ($which_page){
            case 'edit_mailinglists':
            $w[] = array('member_filter_id' => 0);
            break;
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

        $objectArray = $object->toArray();
        $member_id = $object->get('Names_id');
        $name_subscribed = $object->get('Names_subscribed');
        $name_unsubscribed = $object->get('Names_unsubscribed');

        $objectArray['Names_active'] = empty($member_id) ? false : true;
        $objectArray['Names_subscribed'] = empty($name_subscribed) ? false : true;
        $objectArray['Names_unsubscribed'] = empty($name_unsubscribed) ? false : true;


        return $objectArray;
    }

}
