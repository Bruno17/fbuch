<?php

include dirname(__DIR__).'/BaseController.php';

class MyControllerSetupFindOrphans extends BaseController {

    //public $classKey = 'fbuchBoot';

    public function post() {
        $properties = $this->getProperties();
        
        $objectArray = [];

        $this->fbuchCorePath = dirname(__FILE__,7) . '/core/components/fbuch/';
        $this->fbuchAssetsPath = dirname(__FILE__,7) . '/assets/components/fbuch/';
        $this->fbuchBuildPath = dirname(__FILE__,7) . '/_build/';

        $this->config = json_decode($this->getConfig(),true);
        $this->found=[]; 

        $snippets = $this->findElements('snippets');
 
        $chunks = $this->findElements('chunks');
        //$this->makeTree(); 

        print_r($this->found);

        return $this->success('',$objectArray);
        
    }

    public function findMe($word, $content) {
        $len = strlen($word);
        $pos = strpos($content, $word);

        if ($pos !== false) {
            $char = substr($content,$pos+$len,1);
            
            if (IntlChar::isalnum($char)){
                return substr($content,$pos-2,$len+7); 
            }

            if ($pos>0){
                $char = substr($content,$pos-1,1);
                if (IntlChar::isalnum($char)){
                    return 'xx_' . substr($content,$pos-1,$len+5); 
                }                
            }
             
            return true;
        }

        return false;
    }  
    
    public function findElements($type){
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

                $toSearch = ['name' => $element['name'],'type'=>$type];

                $this->searchInElements($toSearch,'templates');
 
                $this->searchInResources($toSearch,$this->found['resources']); 
                $folder = $this->fbuchCorePath . 'migxconfigs';
                $this->searchInFolder($toSearch,$folder);
                $folder = $this->fbuchCorePath . 'model/fbuch';
                $this->searchInFolder($toSearch,$folder);                
  
            }
            
        }
    }

    public function makeTree(){
        foreach ($this->found as $name => $found) {
            $found_in = $found['found_in'];
            if (count($found_in)>0){
                foreach ($found_in as $word => $value){
                    $this->found[$name]['found_in'][$word]['found_in']=$this->found[$word]['found_in'];      
                }
            }
        }  
    }

    public function searchInElements($toSearch,$type){
        
        $word = $toSearch['name'];
        $folder = $this->fbuchCorePath . 'elements/' . $type . '/';
        $elements = $this->config['package']['elements'][$type];
        
        if (is_array($elements)) {
            foreach ($elements as $element){
                $file = $element['file'];
                if ($content = file_get_contents($folder.$file)){
                    if ($result = $this->findMe($word,$content)){
                        if ($result === true){
                            $found_in = $type . ':' . $element['name'];
                            if (!in_array($found_in,$this->found[$toSearch['type']][$toSearch['name']])){
                                $this->found[$toSearch['type']][$toSearch['name']][] = $found_in; 
                            }                            
                        }
                        
                    }
                }
            }
        }                          
    }

    public function searchInResources($toSearch){
        
        $word = $toSearch['name'];
        if ($resources = $this->modx->getCollection('modResource',['deleted'=>0])) {
            foreach ($resources as $resource){
                $pagetitle = $resource->get('pagetitle');
                if ($content = $resource->get('content')){
                    if ($result = $this->findMe($word,$content)){
                        if ($result === true){
                            $found_in = 'resources:' . $pagetitle;
                            if (!in_array($found_in,$this->found[$toSearch['type']][$toSearch['name']])){
                                $this->found[$toSearch['type']][$toSearch['name']][] = $found_in; 
                            }    
                        }
                        
                    }
                }
            }
        }                          
    }    

    public function searchInFolder($toSearch,$folder){
        $word = $toSearch['name'];
        $this->files=[];
        $this->dirWalk($folder);
        foreach ($this->files as $file => $folder){
            $filepath = $folder . '/' . $file;
            $content = file_get_contents($filepath);
            
            if ($result = $this->findMe($word,$content)){
                if ($result === true){
                    $found_in = 'files:' . $filepath;
                    if (!in_array($found_in,$this->found[$toSearch['type']][$toSearch['name']])){
                        $this->found[$toSearch['type']][$toSearch['name']][] = $found_in; 
                    }   
                }
                
            }            
        }       
    }

    
    public function getConfig(){
        return file_get_contents($this->fbuchBuildPath.'config.json');        
    }

        /**
         * Recursively search directories for certain file types
         * Adapted from boen dot robot at gmail dot com's code on the scandir man page
         * @param $dir - dir to search (no trailing slash)
         * @param mixed $types - null for all files, or a comma-separated list of strings
         *                       the filename must include (e.g., '.php,.class')
         * @param bool $recursive - if false, only searches $dir, not it's descendants
         * @param string $baseDir - used internally -- do not send
         */
        public function dirWalk($dir, $types = null, $recursive = false, $baseDir = '') {

            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    // $this->output .= "\n" , $dir;
                    //$this->output .= "\n", $file;
                    if (is_file($dir . '/' . $file)) {
                        if ($types !== null) {
                            $found = false;
                            $typeArray = explode(',', $types);
                            foreach ($typeArray as $type) {
                                if (strstr($file, $type)) {
                                    $found = true;
                                }
                            }
                            if (!$found) {
                                continue;
                            }
                        }
                        // $this->{$callback}($dir, $file);
                        $this->addFile($dir, $file);
                    } elseif ($recursive && is_dir($dir . '/' . $file)) {
                        $this->dirWalk($dir . '/' . $file, $types, $recursive, $baseDir . '/' . $file);
                    }
                }
                closedir($dh);
            }
        }  
        
        public function addFile($dir, $file) {
            $this->files[$file] = $dir;
        }        

    public function verifyAuthentication() {
        if (!$this->modx->user->isMember('Administrator')) {
            return false;
        }
        return true;
    }    
    
}