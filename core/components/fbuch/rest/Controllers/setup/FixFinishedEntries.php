<?php


include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupFixFinishedEntries extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
 
        $query = '
        update `modx_fbuch_fahrten`  
        set finished = 1
        WHERE (`km` > 0)';
        
        
        
        $result = $this->modx->exec($query);

        $query = '
        update `modx_fbuch_fahrten`  
        set deleted = 1
        WHERE (`km` < 1) AND date_end < "1971"';
                
        
        $result = $this->modx->exec($query);        

        return $this->success('',$objectArray);
    }

    
    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}