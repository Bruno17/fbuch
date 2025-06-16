<?php

include 'BaseController.php';

class MyControllerCompetencyLevels extends BaseController {
    public $classKey = 'fbuchCompetencyLevel';
    public $defaultSortField = 'level';
    public $defaultSortDirection = 'ASC';

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePost() {
        throw new Exception('Unauthorized', 401);
    }

    public function verifyAuthentication() {
        //keine bestimmte Berechtigung benötigt
        return true;
    }

    public function getList(){
        $this->modx->fbuch->getChunkName('fbuch_skill_grades');
        $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $skillgrades = $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $this->setProperty('skillgrades',json_decode($skillgrades,1));
        return parent::getList();
    }


    
    
    
    protected function prepareListObject(xPDOObject $object) {
        $skillgrades = $this->getProperty('skillgrades',[]);
        $output = $object->toArray();
        $returntype = $this->getProperty('returntype');
        switch ($returntype) {
            case 'options':
                $output['importances'] = [];
                $output['grades'] = [];
                if (is_array($skillgrades['competency_levels_select']) && in_array($object->get('level'),$skillgrades['competency_levels_select'])){
                    $output['importances'] = is_array($skillgrades['importances']) ? $skillgrades['importances'] : [];
                }
                if (is_array($skillgrades['competency_levels_select']) && in_array($object->get('level'),$skillgrades['competency_levels_select'])){
                    $output['grades'] = is_array($skillgrades['grades']) ? $skillgrades['grades'] : [];
                }                
                $output['label'] = $object->get('name'). ' (' . $object->get('level') . ')';
                $output['value'] = $object->get('level');
                break;
                default:
                $permissions = $object->get('permissions');
                $output['permissions'] = json_decode($permissions,true);
                break;
        }

        return $output;
    }    

}