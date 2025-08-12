<?php
include 'BaseController.php';

class MyControllerDateComments extends BaseController {
    
    public $defaultLimit = 0;
    public $classKey = 'fbuchDateComment';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $currentFbuchMember = false;

    public function beforeDelete() {
        if ($this->modx->hasPermission('fbuch_delete_datecomment')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }
        return !$this->hasErrors();
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_edit_datecomment')) {
 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }


    public function beforePost() {
        if ($this->modx->hasPermission('fbuch_accept_invite')) {
            if ($member = $this->getCurrentFbuchMember()){
                $this->object->set('member_id',$member->get('id'));
            }
               $date = new DateTime;
               $this->object->set('createdon',date_format($date,'Y-m-d H:i:s'));   
               $this->object->set('createdby',$this->modx->user->get('id'));          
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function post() {
        $properties = $this->getProperties();
        $selection_state = $this->getProperty('selection_state',[]); 
        $commenttype = $this->modx->getOption('commenttype',$selection_state,false); 
        $objectArray = [];
        switch ($commenttype){
            case 'mail_only':
                break;
            default:
                    /** @var xPDOObject $object */
                    $this->object = $this->modx->newObject($this->classKey);
                    $this->object->fromArray($properties);
                    $beforePost = $this->beforePost();
                    if ($beforePost !== true && $beforePost !== null) {
                        return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
                    }
                    if (!$this->object->{$this->postMethod}()) {
                        $this->setObjectErrors();
                        if ($this->hasErrors()) {
                            return $this->failure();
                        } else {
                            return $this->failure($this->modx->lexicon('rest.err_class_save',array(
                                'class_key' => $this->classKey,
                            )));
                        }

                    }
                    $objectArray = $this->object->toArray();
                break;    
        }

        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }

    public function afterPost(array &$objectArray) {
        $selection_state = $this->getProperty('selection_state',[]); 
        $commenttype = $this->modx->getOption('commenttype',$selection_state,false); 
        switch ($commenttype){
            case 'comment_and_mail':
            case 'mail_only':
                $this->sendMails($selection_state);
                break;    
        }
        if ($commenttype != 'mail_only'){
            $this->sendElementMessage();    
        }

    }

    public function sendElementMessage(){
        if ($member = $this->getCurrentFbuchMember()){
            $current_member_id = $member->get('id');
            $date_id = $this->getProperty('date_id',false);
            $comment = $this->getProperty('comment','');
            $this->modx->fbuch->sendElementMessage($comment,$date_id,$current_member_id);    
        } 
        
    }

    public function sendMails($selection_state){
        if ($member = $this->getCurrentFbuchMember()){
            $date_id = $this->getProperty('date_id',false);
            $comment = $this->getProperty('comment','');
            $recipients = $this->collectRecipients($selection_state,$date_id);
            $this->modx->fbuch->sendCommentMails($comment, $recipients, $member, $date_id);     
        }        
   
    }
    
    public function collectRecipients($selection_state,$date_id){
        
        $all_invited = $this->modx->getOption('allinvited',$selection_state,false); 
        $all_persons = $this->modx->getOption('allpersons',$selection_state,false); 
        $invited = $this->modx->getOption('invited',$selection_state,[]); 
        $persons = $this->modx->getOption('persons',$selection_state,[]);
        $member_ids = [];
        $this->getMembersWithMail($invited,$member_ids);
        $this->getMembersWithMail($persons,$member_ids);
        if ($all_invited){
            $this->getAllInvited($date_id,$member_ids); 
        }
        if ($all_persons){
            $this->getAllPersons($date_id,$member_ids); 
        }        
        return array_values($member_ids);
    }

    public function getMembersWithMail($persons,&$member_ids){
        $ids = [];
        if (is_array($persons)){
            foreach ($persons as $person){
                if (is_array($person) && !empty($person['member_id'])){
                    $ids[] = $person['member_id'];
                }
            }
        }
        if (count($ids)>0){
            $c = $this->modx->newQuery('mvMember');
            $c->where(['email:!='=>'','id:IN'=>$ids]);
            if ($collection = $this->modx->getCollection('mvMember',$c)){
                foreach ($collection as $object){
                    $member_ids[$object->get('id')] = $object->get('id');
                }
            }
        }
        return true;
    }

    public function getAllInvited($date_id, &$member_ids){
        $c = $this->modx->newQuery('fbuchDateInvited');
        $c->select($this->modx->getSelectColumns('fbuchDateInvited', 'fbuchDateInvited', ''));
        $joins = '[{"alias":"Member","selectfields":"email"}]';
        $this->modx->migx->prepareJoins('fbuchDateInvited', json_decode($joins,1) , $c);
        $c->where(['date_id'=>$date_id]);
        if ($collection = $this->modx->getCollection('fbuchDateInvited',$c)){
            
            foreach ($collection as $object){
                if (!empty($object->get('Member_email'))){
                    $member_ids[$object->get('member_id')] = $object->get('member_id');
                }    
            }
        }
        return true;
    }
    public function getAllPersons($date_id, &$member_ids){
        $c = $this->modx->newQuery('fbuchDateNames');
        $c->select($this->modx->getSelectColumns('fbuchDateNames', 'fbuchDateNames', ''));
        $joins = '[{"alias":"Member","selectfields":"email"}]';
        $this->modx->migx->prepareJoins('fbuchDateNames', json_decode($joins,1) , $c);   
        $c->where(['date_id'=>$date_id]);
        if ($collection = $this->modx->getCollection('fbuchDateNames',$c)){
            foreach ($collection as $object){
                if (!empty($object->get('Member_email'))){
                    $member_ids[$object->get('member_id')] = $object->get('member_id');
                }  
            }
        }
        return true;
    }    


    protected function prepareListQueryBeforeCount(xPDOQuery $c) {

        $date_id = $this->getProperty('date_id',false);
        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'count':
                break;
    
                default:  
                    $joins = '[{"alias":"Member","selectfields":"name,firstname"}]';
                    $this->modx->migx->prepareJoins($this->classKey, json_decode($joins,1) , $c);  
                    $this->currentFbuchMember = $this->getCurrentFbuchMember();            
            }            

        if ($date_id){
            $c->where(['date_id' => $date_id]);
        } 

        $c->sortby('fbuchDateComment.createdon','DESC');
        return $c;
    } 
    
    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);

        $returntype = $this->getProperty('returntype');

 
        switch ($returntype) {
            case 'count':
            break;

            default:        
            $name = $object->get('Member_firstname') . ' ' . $object->get('Member_name');
            $member_id = $this->currentFbuchMember ? $this->currentFbuchMember->get('id') : false;
            $output['from'] = $name;
            $date = date_create($object->get('createdon'));
            $output['createdon_formatted'] = date_format($date,'d.m.Y H:i'); 
            $output['text'] = [str_replace("\n",'<br>',$object->get('comment'))];
            $output['sent'] = $member_id == $object->get('member_id');
            $output['initials'] = substr($object->get('Member_firstname'),0,1) . substr($object->get('Member_name'),0,1);                
            break;                                 
        }

        return $output;
    }    

    public function verifyAuthentication() {
        if ($this->modx->hasPermission('fbuch_read_datecomments')) {
            return true;
        }
        return false;
    }
    
}
