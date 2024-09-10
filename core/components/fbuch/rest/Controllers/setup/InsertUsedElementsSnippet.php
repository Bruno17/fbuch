<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupInsertUsedElementsSnippet extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        
        $objectArray = [];

        $this->fbuchCorePath = dirname(__FILE__,7) . '/core/components/fbuch/';
        $this->fbuchAssetsPath = dirname(__FILE__,7) . '/assets/components/fbuch/';
        $this->fbuchBuildPath = dirname(__FILE__,7) . '/_build/';

        $this->config = json_decode($this->getConfig(),true);
        $this->found=[]; 

        $snippets = $this->insertToElements('snippets');
 
        $chunks = $this->insertToElements('chunks');
        //$this->makeTree(); 

        return $this->success('',$objectArray);
        
    }

    public function findMe($word, $content) {
        $len = strlen($word);
        $pos = strpos($content, $word);

        if ($pos !== false) {
            return true;
        }

        return false;
    }  
    
    public function insertToElements($type){
        if (!isset($this->found[$type])){
            $this->found[$type] == []; 
        }

        $elements = $this->config['package']['elements'][$type];
        if (is_array($elements)) {
            foreach ($elements as $element){
                $element_name = $element['name'];
                if (!isset($this->found[$type][$element_name])){
                    $this->found[$type][$element_name] = []; 
                }                

                $this->insertToElement($element,$type);
            }
            
        }
    }

    public function insertToElement($element,$type){

        switch ($type) {
            case 'chunks':
                $word = '[[fbuch_is_element_used? &type=`chunks` &name=`' . $element['name'] . '`]]';
                break;
            case 'snippets':
                $word = '$modx->runSnippet("fbuch_is_element_used" , ["type" => "chunks","name" => "' . $element['name'] . '"]);';
                $word = str_replace('"',"'",$word);        
                break;
        }
        
        $folder = $this->fbuchCorePath . 'elements/' . $type . '/';
        $file = $element['file'];
        if ($content = file_get_contents($folder.$file)){
            if ($result = $this->findMe($word,$content)){
                if ($result !== true){
                                       
                }
                
            }
        }                  
    }

    public function getConfig(){
        return file_get_contents($this->fbuchBuildPath.'config.json');        
    }

    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}