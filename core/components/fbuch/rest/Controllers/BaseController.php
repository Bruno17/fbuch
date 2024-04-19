<?php

class BaseController extends fbuchRestController {
    
    public $defaultLimit = 0;
    
	public function setProperties(array $properties = array(),$merge = false) {
        parent::setProperties($properties,$merge);
        $this->sanitizeRequest();
	} 
    
    /**
     * Harden GPC variables by removing any MODX tags, Javascript, or entities.
     */
    public function sanitizeRequest() {
        $modxtags = array_values($this->modx->sanitizePatterns);
        
        $depth = (int)ini_get('max_input_nesting_level');
        $depth = ($depth <= 0) ? 99 : $depth + 1;

        if ($this->modx->getOption('allow_tags_in_post',null,true)) {
            modX :: sanitize($this->properties);
        } else {
            modX :: sanitize($this->properties, $modxtags, $depth);
        }
    }     

    public function getCurrentFbuchMember() {
        return $this->modx->fbuch->getCurrentFbuchMember();
    }

    public function getDefaultCompetencyLevel() {
        $object = $this->modx->getObject('fbuchCompetencyLevel',['level'=>'A']);
        if ($object) {
            $this->CompetencyLevel_color = $object->get('color');
            $this->CompetencyLevel_color_name = $object->get('color_name');
        }        
        return $object;
        
    }
    
    public function getNameEmail($object) {
        $email = '';
        if ($object) {
            $email = $object->get('email');
            //try to get email from MV

            /*
            $member_id = $object->get('mv_member_id');
            if ($member = $this->modx->getObject('mvMember', $member_id)) {
                $email = $member->get('email');
            }
            */
        }
        return $email;
    }    
    
}
