<?php

include 'BaseController.php';

class MyControllerMemberSkills extends BaseController {
    public $classKey = 'mvMemberSkill';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        if ($this->modx->hasPermission('mv_delete_memberskills')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePut() {
        $fbuchMember = $this->getCurrentFbuchMember();
        $member_id = $this->getProperty('member_id',0);

        if ($member_id==$fbuchMember->get('id') || $this->modx->hasPermission('mv_edit_membercompetencies')) {
            $this->object->set('createdby', $this->modx->user->get('id'));
            $this->object->set('createdon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {
        $fbuchMember = $this->getCurrentFbuchMember();
        $member_id = $this->getProperty('member_id',0);        
        if ($member_id==$fbuchMember->get('id') || $this->modx->hasPermission('mv_edit_membercompetencies')) {
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

    public function post() {
        $properties = $this->getProperties();
        $member_id = $this->getProperty('member_id',0);
        $skill_id = $this->getProperty('skill_id',0);
        $user_id = $this->modx->user->get('id',0);
        $c = $this->modx->newQuery($this->classKey);
        $c->where(['member_id'=>$member_id,'skill_id'=>$skill_id,'createdby'=>$user_id]);
        if ($object = $this->modx->getObject($this->classKey,$c)){
            $this->setProperty('id',$object->get('id'));
            $this->put();
        } else {
            parent::post();
        }

    }    


}