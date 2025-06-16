<?php

class MyControllerSkillGrades extends fbuchRestController {
    
    public $classKey = 'modResource';
 
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
        return true;
    }


    
    public function getList() {
        $total = 0;
        
        $this->modx->fbuch->getChunkName('fbuch_skill_grades');
        $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $skillgrades_json = $this->modx->getChunk($this->modx->fbuch->getChunkName('fbuch_skill_grades'));
        $list = json_decode($skillgrades_json,1);
 
        return $this->collection($list,$total);
    }    

}
