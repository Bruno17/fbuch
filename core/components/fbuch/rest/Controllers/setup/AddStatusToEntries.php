<?php


include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupAddStatusToEntries extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        $objectArray = [];
        $c = $this->modx->newQuery('fbuchFahrtNames');
        $c->where(['fahrt_id:!='=>0]);
        //$c->where(['member_status'=>'']);
        if ($collection = $this->modx->getIterator('fbuchFahrtNames',$c)){
            foreach ($collection as $fahrtname){
                if ($fahrt = $fahrtname->getOne('Fahrt')){
                    $fahrt_date = $fahrt->get('date_end');
                    $fahrt_date = empty($fahrt_date) ? $fahrt->get('date') : $fahrt_date;
                    echo $fahrt_date = substr($fahrt_date,0,10);
                    $guestname = $fahrtname->get('guestname');
                    $member_status = '';
                    if (!empty($guestname)){
                        $member_status = 'Gasteintrag';
                    }

                    if ($member = $fahrtname->getOne('Member')){
                        $member_status = $member->get('member_status');
                        echo $member->get('name');
                        echo '(';
                        echo $member->get('id');
                        echo ')';
                        //echo $fahrt_date = $fahrt->get('date_end');
                        echo ':';
                        $eintritt = $member->get('eintritt');
                        echo $eintritt = substr($eintritt,0,10);
                        echo ' - ';
                        $austritt = $member->get('austritt');
                        echo $austritt = substr($austritt,0,10);
                        echo ' ';
                        if ($fahrt_date>=$eintritt && $fahrt_date<=$austritt){
                            $member_status = 'Mitglied';
                        }
                        if ($fahrt_date<$eintritt){
                            $member_status = 'Gast';
                        }
                        if ($member_status == 'Ausgetreten'){
                            //ist eigentlich nicht möglich - weiterhin als Gast in der Auswertung?
                            $member_status = 'Gast';
                        }
                    }
                    $fahrtname->set('member_status',$member_status);
                    $fahrtname->save();                    
                    echo $member_status;
                    echo PHP_EOL;
                }
            }
        }


        return $this->success('',$objectArray);
 
        $query = '
        update `modx_fbuch_fahrten`  
        set finished = 1
        WHERE (`km` > 0)';
        
        $result = $this->modx->exec($query);

        $query = '
        update `modx_fbuch_fahrten`  
        set deleted = 1
        WHERE (`km` < 1) AND date_end = "1970-01-01 00:00:00"';
        
        $result = $this->modx->exec($query);  
        
        $query = '
        update `modx_fbuch_fahrten`  
        set finished = 1
        WHERE (`km` < 1) AND date_end = "1970-01-01 00:00:00"';
       
        $result = $this->modx->exec($query); 

        $query = '
        update `modx_fbuch_fahrten`  
        set finished = 1
        WHERE (`km` < 1) AND date_end is null';
        
        $result = $this->modx->exec($query);  
        
        $query = '
        update `modx_fbuch_fahrten`  
        set deleted = 1
        WHERE (`km` < 1) AND date_end is null';
        
        $result = $this->modx->exec($query);        

        $query = '
        update `modx_fbuch_fahrten`  
        set finished = 1
        WHERE boot_id = 0';
        
        $result = $this->modx->exec($query);       

        
    }

    
    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}