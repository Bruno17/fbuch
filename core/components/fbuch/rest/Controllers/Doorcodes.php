<?php

include 'BaseController.php';

class MyControllerDoorcodes extends BaseController {
    public $classKey = 'mvDoorAccesscode';
    public $defaultSortField = 'mvDoorAccesscode.code';
    public $defaultSortDirection = 'ASC';
    public $CodeLength = 6;

    public function beforeDelete() {
        throw new Exception('Unauthorized', 401);
    }

    public function beforePut() {

        if ($this->modx->hasPermission('fbuch_manage_doorcodes')) {
            $this->object->set('editedby', $this->modx->user->get('id'));
            $this->object->set('editedon', strftime('%Y-%m-%d %H:%M:%S')); 
        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    public function beforePost() {

        if ($this->modx->hasPermission('fbuch_manage_doorcodes')) {

        } else {
            throw new Exception('Unauthorized', 401);
        }

        return !$this->hasErrors();
    }

    // Funktion zur Generierung eines 7-stelligen Zahlencodes
    public function generate_code() {
        $code = str_pad(strval(rand(0, 9999999)), $this->CodeLength, '0', STR_PAD_LEFT);
        return $code;
    }

    // Funktion zur Überprüfung der Sicherheit des Codes
    public function is_secure($code) {
        //Überprüfung auf richtige Länge des Codes
        if (strlen($code) != $this->CodeLength){
            return false;
        }

        // Überprüfung auf keine Zahlenfolgen von mehr als 3 Zahlen
        for ($i = 0; $i < strlen($code) - 3; $i++) {
            if (in_array(substr($code, $i, 4), ['0123', '1234', '2345', '3456', '4567', '5678', '6789', '7890', '8901', '9012'])) {
                return false;
            }
        }

        // Überprüfung auf keine mehr als zweimal hintereinander stehenden gleichen Zahlen
        for ($i = 0; $i < strlen($code) - 2; $i++) {
            if ($code[$i] == $code[$i+1] && $code[$i+1] == $code[$i+2]) {
                return false;
            }
        }

        // Überprüfung auf sich wiederholende Muster
        for ($i = 1; $i <= strlen($code) / 2; $i++) {
            if (substr($code, 0, $i) == str_repeat(substr($code, 0, $i), strlen($code) / $i)) {
                return false;
            }
        }

        return true;
    }    

    public function loadCodes(){
        $codes = [];
        if ($collection = $this->modx->getCollection($this->classKey)){
            foreach ($collection as $object){
                $codes[] = $object->get('code');    
            }
        }
        return $codes;
    }

    public function getMaxPossibleCodes(){
        // Maximale Anzahl an möglichen eindeutigen Codes unter Berücksichtigung der is_secure Funktion
        $max_possible_codes = 0;
        for ($i = 0; $i < 10 ** $this->CodeLength; $i++) {
            $code = str_pad(strval($i), $this->CodeLength, '0', STR_PAD_LEFT);
            if ($this->is_secure($code)) {
                $max_possible_codes++;
            }
        } 
        return $max_possible_codes;        
    }

    public function createCodes($amount){

        $existing_codes = $this->loadCodes();
        $new_codes = []; // Liste für neue Codes

        // Zähler für die Anzahl der generierten Codes, einschließlich der bereits vorhandenen Codes
        $existing_count = $generated_count = count($existing_codes);

        // Maximale Anzahl an möglichen eindeutigen Codes unter Berücksichtigung der is_secure Funktion
        $max_possible_codes = $this->getMaxPossibleCodes();

        while (count($new_codes) < $amount) { 
            $new_code = $this->generate_code();

            // Überprüfen, ob der Code bereits in der vorhandenen Liste oder den neuen Codes ist oder unsicher ist
            while (in_array($new_code, $existing_codes) || in_array($new_code, $new_codes) || !$this->is_secure($new_code)) {
                $new_code = $this->generate_code(); // Generiere einen neuen Code, wenn der aktuelle bereits existiert oder unsicher ist
            }

            $new_codes[] = $new_code; // Füge den sicheren und eindeutigen Code zur Liste der neuen Codes hinzu

            // Erhöhe den Zähler für generierte Codes
            $generated_count++;
        }

        // Zufällige Werte für die beiden neuen Parameter
        $time_setting = 0; // 0 --> False, 1 --> True
        $blocked = 1; // 1 --> True        

        // Speichern der aktualisierten Codes in der CSV-Datei im spezifizierten Ordner
        foreach ($new_codes as $code) {
            if ($object = $this->modx->newObject($this->classKey)){
                $object->fromArray(['code'=>$code,'time_setting'=>$time_setting,'blocked'=>$blocked]); 
                $object->save();
            }
            if ($object = $this->modx->newObject('mvDoorAccesscodeMember')){
                $object->fromArray(['code'=>$code]); 
                $object->save();
            }  
        }
        $result = [];
        // Ausgabe der Anzahl der generierten Codes und der verbleibenden möglichen Codes
        $result['total'] = $generated_count;
        $result['remaining_possible_codes'] = $max_possible_codes - $generated_count;
        $result['new_codes'] = implode(', ',$new_codes);
        $result['new_count'] = count($new_codes);
    
        return $result;
    }

    public function post() {
        $properties = $this->getProperties();
        $action = $this->getProperty('processaction');
        $amount = $this->getProperty('amount',0);

        $beforePost = $this->beforePost();
        if ($beforePost !== true && $beforePost !== null) {
            return $this->failure($beforePost === false ? $this->errorMessage : $beforePost);
        }
        $objectArray = [];
        switch ($action) {
            case 'createCodes':
                $objectArray = $this->createCodes($amount);               
                break;
            default:
                break;    
        }
        
        $this->afterPost($objectArray);
        return $this->success('',$objectArray);
    }      



    public function afterPost(array &$objectArray) {
 
    }

    public function afterPut(array &$objectArray) {
 
    }    

    public function verifyAuthentication() {

        if (!$this->modx->hasPermission('fbuch_view_doorcodes')) {
            return false;
        }
        return true;
    }

    protected function prepareListQueryBeforeCount(xPDOQuery $c) {
         
        return $c;
    }

    protected function prepareListObject(xPDOObject $object) {

        $output = $object->toArray('', false, true);
 
        return $output;
    }

}
