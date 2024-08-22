<?php


include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupAddStatusToEntries extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function setDateEnd(){

        $query = '
        update `modx_fbuch_fahrt_names`  
        set member_status = ""
        ';
        
        $result = $this->modx->exec($query);   
        
        $query = '
        update `modx_fbuch_fahrten`  
        set date_end = date
        WHERE date_end = "1970-01-01 00:00:00" or date_end is null or date_end < date';
        
        $result = $this->modx->exec($query);           

        $query = '
        update `modx_fbuch_fahrten`  
        set date_end = date
        WHERE date_end = "1970-01-01 00:00:00" or date_end is null';
        
        $result = $this->modx->exec($query);         

    }

    public function post() {
        
        $properties = $this->getProperties();
        $returntype = $this->getProperty('returntype');
        $limit = (int) $this->getProperty('limit');
        $results = [];
        $c = $this->modx->newQuery('fbuchFahrtNames');
        $c->where(['fahrt_id:!='=>0]);
        $total = $this->modx->getCount('fbuchFahrtNames',$c);
        if ($returntype == 'total'){
            $this->setDateEnd();
            return $this->success('',['total'=>$total,'results'=>$results]);    
        }
        $c->where(['member_status'=>'']);
        $total = $this->modx->getCount('fbuchFahrtNames',$c);        
        if ($limit > 0){
            $c->limit($limit,0);    
        }
        
        if ($collection = $this->modx->getIterator('fbuchFahrtNames',$c)){
            foreach ($collection as $fahrtname){
                $result = [];
                if ($fahrt = $fahrtname->getOne('Fahrt')){
                    $result['date_end'] = $fahrt_date = $fahrt->get('date_end');
                    $result['date'] = $fahrt->get('date');
                    $fahrt_date = empty($fahrt_date) ? $result['date'] : $fahrt_date;
                    $fahrt_date = substr($fahrt_date,0,10);
                    $fahrt_year = substr($fahrt_date,0,4);

                    $guestname = $fahrtname->get('guestname');
                    $member_status = 'Unbekannt';
                    if (!empty($guestname)){
                        $member_status = 'Gasteintrag';
                    }

                    if ($member = $fahrtname->getOne('Member')){
                        $member_status = $member->get('member_status');
                        $birthdate = $member->get('birthdate');
                        $result['name'] = $member->get('firstname') . ' ' . $member->get('name');
                        $result['member_id'] = $member->get('id');
                        $eintritt = $member->get('eintritt');
                        $result['eintritt'] = $eintritt = substr($eintritt,0,10);
                        $result['year_eintritt'] = $year_eintritt = substr($eintritt,0,4);
                        $austritt = $member->get('austritt');
                        $austritt = substr($austritt,0,10);
                        if (!empty($year_eintritt) && $fahrt_year>=$year_eintritt){
                            //austritt immer auf Ende des Austrittsjahres setzen.
                            $austritt = empty($austritt) ? $fahrt_year . '-12-31' : $austritt;                            
                        }
 
                        $result['austritt'] = $austritt;
                        /*
                        if ($fahrt_date>=$eintritt && $fahrt_date<=$austritt){
                            $member_status = 'Mitglied';
                        }
                        */
                        if (!empty($year_eintritt) && $fahrt_year>=$year_eintritt && $fahrt_date<=$austritt){
                            //Jahr der Fahrt später oder gleich dem Eintrittsjahr und
                            //Datum der Fahrt früher als das Austrittsdatum
                            $member_status = 'Mitglied';
                        }
                        if ($fahrt_year < $year_eintritt){
                            //Jahr der Fahrt vor dem Jahr des Eintritts
                            //Fahrt als Gast Fahrt einstufen, wenn momentan Mitglied oder Ausgetreten
                            //ansonsten aktuellen Mitgliedstatus (zb. VHS) eintragen
                            if ($member_status == 'Mitglied' || $member_status == 'Ausgetreten'){
                                $member_status = 'Gast';    
                            }
                        }                                                
                        if ($member_status == 'Ausgetreten'){
                            //ist eigentlich nicht möglich - weiterhin als Gast in der Auswertung?
                            $member_status = 'Gast';
                        }
                        if ($member_status == 'Unbekannt' && !empty($birthdate) && empty($eintritt) && empty($austritt)){
                            //aktueller Status, warum auch immer, Unbekannt, Geburtstag eingetragen, kein Eintritts oder Austrittsdatum
                            //das sind scheinbar oftmals Mitglieder aus einer früheren Mitgliederverwaltung, wo kein Eintrittsdatum eingetragen war 
                            //wir gehen einfach mal davon aus, daß diese Personen zum Zeitpunkt der Fahrt Mitglied war
                            $member_status = 'Mitglied';
                        }                        
                        
                    }
                    $result['member_status'] = $member_status;
                    $fahrtname->set('member_status',$member_status);
                    $fahrtname->save();                    
                    

                }
                $results[] = $result;
            }
        }

        return $this->success('',['total'=>$total,'results'=>$results]);

    }

    
    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}