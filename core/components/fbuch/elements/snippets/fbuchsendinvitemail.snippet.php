<?php 
$fbuchCorePath = realpath($modx->getOption('fbuch.core_path', null, $modx->getOption('core_path') . 'components/fbuch')) . '/';
$fbuch = $modx->getService('fbuch', 'Fbuch', $fbuchCorePath . 'model/fbuch/');

$invite_id = $modx->getOption('invite_id', $scriptProperties,0);
$comment = $modx->getOption('comment', $scriptProperties,'');
$comment_name = $modx->getOption('comment_name', $scriptProperties,'');
$add_datecomment = $modx->getOption('add_datecomment', $scriptProperties,false);
$subj_prefix = $modx->getOption('subj_prefix', $scriptProperties,'');

$fbuch->sendInviteMail($invite_id, $comment, $comment_name, $add_datecomment, $subj_prefix);