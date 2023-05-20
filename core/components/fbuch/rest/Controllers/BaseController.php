<?php

class BaseController extends modRestController {
    
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
        $modx = &$this->modx;
        $user_id = $this->modx->user->get('id');
        //try to get fbuch Member by logged in MODX User
        $fbuchMember = $this->modx->getObject('mvMember', array('modx_user_id' => $user_id));    

        return $fbuchMember;
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
