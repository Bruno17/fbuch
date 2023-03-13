<?php
switch ($modx->event->name) {
    case 'OnWebAuthentication':
        $class = $modx->getOption('class',$scriptProperties,'');
        $name = $modx->getOption('name',$scriptProperties,'');

        $fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
        $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');
        
        $authenticated = $modx->fbuch->is_authenticated();

         
        /* Your authentication code here sets $authenticated to true if the 
           user should be allowed to log in */
         
        $modx->event->_output = (bool) $authenticated;
        return;

        break;
    }