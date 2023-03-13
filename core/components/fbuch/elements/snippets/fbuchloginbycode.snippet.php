<?php
$code = $modx->getOption('code', $_REQUEST, '');
$iid = (int) $modx->getOption('iid', $_REQUEST, '');
$redirect_to = '';
if (!empty($code) && !empty($iid)){
    $fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
    $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');
    
    if ($invite_o = $modx->getObject('fbuchDateInvited', $iid)) {
        $date_id = $invite_o->get('date_id');
        $member_id = $invite_o->get('member_id');
        if ($name_o = $invite_o->getOne('Member')) {
            $email = $modx->fbuch->getNameEmail($name_o);
        }
    }
    $code_matches = ($date_id && $email && $code == md5($date_id . $email . $iid)) ? true : false;    

    if ($code_matches && $user = $modx->fbuch->createUserFromMember($member_id,1)){
    $modx->fbuch->authenticated = true;
    $properties = array(
                'login_context' => 'fbuch',
                //'add_contexts'  => $this->getProperty('contexts',''),
                'username'      => $user->get('username'),
                'password'      => 'password',
                'returnUrl'     => '/',
                'rememberme'    => false
            );
            $rawResponse = $modx->runProcessor('security/login', $properties);
            $modx->sendRedirect('/termine/terminanmeldung.html/#/' . $date_id);          
    }
    
  
}
return 'Zugang mit diesen Daten nicht möglich';