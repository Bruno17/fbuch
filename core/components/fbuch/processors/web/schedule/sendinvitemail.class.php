<?php

include_once ('base.class.php');

/**
 * Class fbuchSendInviteMailProcessor
 */
class fbuchSendInviteMailProcessor extends fbuchScheduleBaseProcessor {

    /**
     * Send Invite Mail to Member
     *
     * @return mixed
     */
    public function process() {
        $modx = &$this->modx;

        $results = array();

        $invite_id = $this->getProperty('invite_id'); 
        $comment = $this->getProperty('comment'); 
        $comment_name = $this->getProperty('comment_name'); 
        $add_datecomment = $this->getProperty('add_datecomment'); 
        $add_datecomment = empty($add_datecomment) || $add_datecomment == 'false' ? false : true;
        $subj_prefix = $this->getProperty('subject_prefix'); 

        $success = $this->fbuch->sendInviteMail($invite_id, $comment, $comment_name, $add_datecomment, $subj_prefix);
        $results['sendInviteMail'] = array('success'=>$success,'invite_id'=>$invite_id);

        return array('message' => json_encode($results));
    }
}

return 'fbuchSendInviteMailProcessor';
