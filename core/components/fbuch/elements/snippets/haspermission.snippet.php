<?php $modx->runSnippet('fbuch_is_element_used' , ['type' => 'snippets','name' => 'hasPermission']);
return $modx->hasPermission($permission) ? 'yes' : 'no';