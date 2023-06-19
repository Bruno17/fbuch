<?php

$modx->log(modX::LOG_LEVEL_INFO, 'resolve setup resource');

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

        $group = [
            'policy'=>'Administrator',
            'user_group'=>'Administrator',
            'resource_groups'=>[
                [
                    'name'=>'fbuch'
                ],
                [
                    'name'=>'fbuch_instructor'
                ],
                [
                    'name'=>'Administrator'
                ]               
            ]
        ]; 
        
        //userGroup
        if ($accessPolicy = $modx->getObject('modAccessPolicy', ['name' => $group['policy']])){
            if ($userGroup = $modx->getObject('modUserGroup', ['name' => $group['user_group']])){

            } else {
                $userGroup = $modx->newObject('modUserGroup');
                $userGroup->set('name' , $group['user_group']); 
                $userGroup->save();   
            }
            if ($contextAccess = $modx->getObject('modAccessContext', ['target' => 'fbuch', 'principal_class' => 'modUserGroup', 'principal' => $userGroup->get('id'), 'policy' => $accessPolicy->get('id')])){

            } else {
                 $contextAccess = $modx->newObject('modAccessContext');
                 $contextAccess->set('target', 'fbuch');
                 $contextAccess->set('principal_class', 'modUserGroup');
                 $contextAccess->set('principal', $userGroup->get('id'));
                 $contextAccess->set('policy', $accessPolicy->get('id'));
                 $contextAccess->set('authority', 9999);
                 $contextAccess->save();
             } 
        } 
        
        //resourceGroups
        $context_key = 'fbuch';
        $principal_class = 'modUserGroup';
        $policy_id = '1';//Resource - policy, is this allways 1?
        $authority = '9999';
        //target - document_group_names
        //principal - member_group_names
        $resource_groups = isset($group['resource_groups']) ? $group['resource_groups'] : [];

        if (is_array($resource_groups) && $userGroup = $modx->getObject('modUserGroup', ['name' => $group['user_group']])){
            $principal = $userGroup->get('id');
            foreach ($resource_groups as $resource_group){
                if (isset($resource_group['name']) && $resourceGroup = $modx->getObject('modResourceGroup', ['name' => $resource_group['name']])){
                    
                } else {
                    $resourceGroup = $modx->newObject('modResourceGroup');
                    $resourceGroup->set('name' , $resource_group['name']); 
                    $resourceGroup->save();   
                }
                $target_id = $resourceGroup->get('id');
                $rga_array = [
                    'target' => $target_id, 
                    'principal_class' => $principal_class, 
                    'principal' => $principal, 
                    'policy' => $policy_id,
                    'context_key' => $context_key
                ];
                if ($resourceGroupAccess = $modx->getObject('modAccessResourceGroup', $rga_array)){
    
                } else {
                    $resourceGroupAccess = $modx->newObject('modAccessResourceGroup');
                    $resourceGroupAccess->fromArray($rga_array);
                    $resourceGroupAccess->set('authority', 9999);
                    $resourceGroupAccess->save();
                }  
            }
        }        

        //Resource 'Setup'

        if ($template = $modx->getObject('modTemplate',['templatename'=>'fbuch Quasar 2'])){
            $template_id = $template->get('id');

            $resource_array = [
                'pagetitle' => 'Setup',
                'alias' => 'setup',
                'template' => $template_id,
                'context_key' => 'fbuch',
                'uri' => 'setup.html',
                'published' => 1
            ];
    
            if ($resource = $modx->getObject('modResource',['pagetitle'=>'Setup','parent'=>0])){
    
            }  else {
                $resource = $modx->newObject('modResource');
            }        
    
            $resource->fromArray($resource_array);  
            $resource->save();
            $resource->joinGroup('Administrator');
            
        } else {
            $modx->log(modX::LOG_LEVEL_INFO, 'could not find template fbuch Quasar 2');    
        }
   
        break;

}

return true;
