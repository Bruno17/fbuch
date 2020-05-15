<?php

class mvMail extends xPDOSimpleObject
{


    public function sendMails($recipients)
    {
        foreach ($recipients as $recipient) {
            $this->sendMail($recipient);
        }
    }

    public function sendMail($recipient)
    {

        $modx = &$this->xpdo;
    
        $mailTo = $recipient->get('email');
        //$mailTo = 'b.perner@gmx.de';
 
        $tpl = 'mv_mail_tpl'; 
        $properties = $this->toArray(); 
                
        $message = $modx->getChunk($tpl,$properties);
        $subject = $this->get('subject');
        
        

        $emailFromAddress = $modx->getOption('emailsender');
        $emailFromName = $modx->getOption('site_name');

        $modx->getService('mail', 'mail.modPHPMailer');
        $modx->mail->set(modMail::MAIL_BODY, $message);
        $modx->mail->set(modMail::MAIL_FROM, $emailFromAddress);
        $modx->mail->set(modMail::MAIL_FROM_NAME, $emailFromName);
        $modx->mail->set(modMail::MAIL_SENDER, $emailFromAddress);
        $modx->mail->set(modMail::MAIL_SUBJECT, $subject);

        $mailTo = explode(',', $mailTo);

        if (is_array($mailTo)) {
            foreach ($mailTo as $email) {
                $modx->mail->address('to', $email);
            }
        }

        $modx->mail->address('reply-to', $emailFromAddress);
        $modx->mail->setHTML(true);

        $success = $modx->mail->send();
        if (!$success) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: Serienmail');
        }

        $modx->mail->reset();


    }


}
