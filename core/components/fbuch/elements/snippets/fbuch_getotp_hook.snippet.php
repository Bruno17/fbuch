<?php
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

$email = $hook->getValue('email');
if ($member = $modx->getObject('mvMember',['email'=>$email])){
    $otp = $member->get('otp');
    $otp_sendedon = $member->get('otp_sendedon');
    
    $date1 = date_create();
    $date2 = date_create($otp_sendedon);

    $dateDifference = ($date1->getTimestamp() - $date2->getTimestamp()) / 60;

    if ($otp_sendedon != null && !empty($otp) &&  $dateDifference < 10){
        //otp innerhalb der letzten 10 Minuten bereits angefordert
        
        $modx->setPlaceholder('my.successMessage','Auf diese Adresse wurde kürzlich bereits ein Login Link gesendet. Warte mindestens 10 Minuten bis zum nächsten Versuch.');

        return true;
    }

    $success = false;
    $params = [
        'target' =>$hook->getValue('target'),
        'route' => $hook->getValue('route')
    ];

    $c = $modx->newQuery('mvMember');
    $c->where(['email'=>$email]);
    if ($collection = $modx->getCollection('mvMember',$c)){
        foreach ($collection as $object){
            if ($modx->fbuch->sendLoginMail($object->get('id'),$params)) {
                $success = true;
            }             
        }
    }
    if ($success){
        $modx->setPlaceholder('my.successMessage','Falls Du mit der angegebenen Email Adresse bei uns eingetragen bist, erhälst Du in Kürze eine Mail mit einem Login Link.');
        return true;
    }
    
   
}

$modx->setPlaceholder('my.successMessage','Für diese Email Adresse kann kein Login Link erstellt werden');
return false;