<?php

include_once ('base.class.php');

/**
 * Class fbuchSendMailProcessor
 */
class fbuchSendMailProcessor extends fbuchScheduleBaseProcessor {

    /**
     * Send Mail to Member
     *
     * @return mixed
     */
    public function process() {
        $modx = &$this->modx;

        $results = array();

        $log_id = $this->getProperty('log_id');
        $mail_id = $this->getProperty('mail_id');
        $member_id = $this->getProperty('member_id');

        if ($mail = $modx->getObject('mvMail', array('id' => $mail_id))) {

            if ($member = $modx->getObject('mvMember', array('id' => $member_id))) {
                $properties = $mail->toArray();
                $properties['email'] = $member->get('email');
                $properties['tpl'] = $modx->getOption('mv_mail_tpl');
                $properties['subject'] = $mail->get('subject');
                $success = $this->fbuch->sendMail($properties);
                if ($success) {
                    $log = $modx->newObject('mvMailLogRecipient');
                    $log->set('member_id', $member->get('id'));
                    $log->set('log_id', $log_id);
                    $log->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
                    //$log->set('createdby', $modx->user->get('id'));
                    if ($log->save()) {

                    }
                }

                $results['sendMail'] = array(
                    'success' => $success,
                    'mail_id' => $mail_id,
                    'member_id' => $member_id);
            }


        }


        return array('message' => json_encode($results));
    }
}

return 'fbuchSendMailProcessor';
