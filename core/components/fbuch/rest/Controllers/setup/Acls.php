<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupAcls extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
        $result = $this->updatePermissions();
        $result = $this->updatePolicies();
        $result = $this->updateUserGroups();
        $result = $this->updateResourceGroups();

        return $this->success('',$objectArray);
    }

    public function getPermissions(){
        include 'permissions.inc.php';
        if (isset($permissions) && is_array($permissions)){
            return $permissions;
        }
        return false;
    }

    public function getUsergroups(){
        include 'usergroups.inc.php';
        if (isset($usergroups) && is_array($usergroups)){
            return $usergroups;
        }
        return false;                   
    }

    public function getTemplate(){
        $group = $this->modx->getObject('modAccessPolicyTemplateGroup', ['name' => 'Admin']);
        if (!$group) return false;

        /** @var modAccessPolicyTemplate $template */
        $template = $this->modx->getObject('modAccessPolicyTemplate', ['name' => 'fbuch', 'template_group' => $group->get('id')]);            
        if (!$template) {
            $template = $modx->newObject('modAccessPolicyTemplate');
            $template->set('name', 'fbuch');
            $template->set('template_group', $group->get('id'));
            $template->set('description', 'A policy template for Fbuch');
            $template->set('lexicon', 'permissions');
            $template->save();
        }
        return $template;
    }

    public function updatePolicies(){
        $permissions = $this->getPermissions();
        if (!$permissions){
            return false;
        }
        $usergroups = $this->getUsergroups();
        if (!$usergroups){
            return false;
        }
        $template = $this->getTemplate(); 
        if (!$template){
            return false;
        }                    
        foreach ($usergroups as $group){
            /** @var modAccessPolicy $accessPolicy */
            $accessPolicy = $this->modx->getObject('modAccessPolicy', ['name' => $group['policy']]);
            if (!$accessPolicy) {
                $accessPolicy = $this->modx->newObject('modAccessPolicy');
                $accessPolicy->set('name', $group['policy']);
                $accessPolicy->set('description', 'Zusätzliche Frontend - Berechtigungen für Instruktoren usw.');
                $accessPolicy->set('template', $template->get('id'));
                $accessPolicy->set('lexicon', $template->get('lexicon'));
            }

            $data = [];

            foreach ($permissions as $permission) {
                if ($permission[$group['policy']]=='1'){
                    $data[$permission['name']] = true;    
                }
            }

            $accessPolicy->set('data', $data);
            $accessPolicy->save();
        }         
    }

    public function updatePermissions(){
        $permissions = $this->getPermissions();
        if (!$permissions){
            return false;
        }
        $template = $this->getTemplate();
        if (!$template){
            return false;
        }        
        foreach ($permissions as $permission) {
            /** @var modAccessPermission $obj */

            if ($obj = $this->modx->getObject('modAccessPermission', ['template' => $template->get('id'),'name' => $permission['name']])){

            }  else {
                $obj = $this->modx->newObject('modAccessPermission');
                $obj->set('template', $template->get('id'));
                $obj->set('name', $permission['name']);
            }

            $obj->set('description', $permission['description']);
            $obj->save();
        } 
        
    }

    public function updateUserGroups(){
        $usergroups = $this->getUsergroups();
        if (!$usergroups){
            return false;
        }

        foreach ($usergroups as $group){
            if ($accessPolicy = $this->modx->getObject('modAccessPolicy', ['name' => $group['policy']])){
                if ($userGroup = $this->modx->getObject('modUserGroup', ['name' => $group['user_group']])){

                } else {
                    $userGroup = $this->modx->newObject('modUserGroup');
                    $userGroup->set('name' , $group['user_group']); 
                    $userGroup->save();   
                }
                if ($contextAccess = $this->modx->getObject('modAccessContext', ['target' => 'fbuch', 'principal_class' => 'modUserGroup', 'principal' => $userGroup->get('id'), 'policy' => $accessPolicy->get('id')])){
    
                } else {
                     $contextAccess = $this->modx->newObject('modAccessContext');
                     $contextAccess->set('target', 'fbuch');
                     $contextAccess->set('principal_class', 'modUserGroup');
                     $contextAccess->set('principal', $userGroup->get('id'));
                     $contextAccess->set('policy', $accessPolicy->get('id'));
                     $contextAccess->set('authority', 9999);
                     $contextAccess->save();
                 } 
            }
  
        }
    }

    public function updateResourceGroups(){
        $usergroups = $this->getUsergroups();
        if (!$usergroups){
            return false;
        }

        foreach ($usergroups as $group){
            $context_key = 'fbuch';
            $principal_class = 'modUserGroup';
            $policy_id = '1';//Resource - policy, is this allways 1?
            $authority = '9999';
            //target - document_group_names
            //principal - member_group_names
            $resource_groups = isset($group['resource_groups']) ? $group['resource_groups'] : [];

            if (is_array($resource_groups) && $userGroup = $this->modx->getObject('modUserGroup', ['name' => $group['user_group']])){
                $principal = $userGroup->get('id');
                foreach ($resource_groups as $resource_group){
                    if (isset($resource_group['name']) && $resourceGroup = $this->modx->getObject('modResourceGroup', ['name' => $resource_group['name']])){
                        
                    } else {
                        $resourceGroup = $this->modx->newObject('modResourceGroup');
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
                    if ($resourceGroupAccess = $this->modx->getObject('modAccessResourceGroup', $rga_array)){
        
                    } else {
                        $resourceGroupAccess = $this->modx->newObject('modAccessResourceGroup');
                        $resourceGroupAccess->fromArray($rga_array);
                        $resourceGroupAccess->set('authority', 9999);
                        $resourceGroupAccess->save();
                    }  
                }
            }
        }
    }    
    
    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}