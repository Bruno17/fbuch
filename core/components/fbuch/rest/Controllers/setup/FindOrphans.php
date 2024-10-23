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
        $this->used_elements = $this->getUsedElements();
        $this->elements_found_in_level = [];
    
        $this->level = 4;
        
        if ($this->level > 1){
            $this->readOrphans();
            //print_r($this->elements_found_in_level);
        }

        $this->findElements('snippets');
        $this->findElements('chunks');

        $this->writeOrphans();

        return $this->success('',$objectArray);
        
    }



    public function findMe($word, $content) {
        $len = strlen($word);
        $pos = strpos($content, $word);

        if ($pos !== false) {
            $char = substr($content,$pos+$len,1);
            
            if (IntlChar::isalnum($char) || $char=='_'){
                $content = substr($content,$pos+1);
                return $this->findMe($word,$content); 
            }

            if ($pos>0){
                $char = substr($content,$pos-1,1);
                if (IntlChar::isalnum($char) || $char=='_'){
                    $content = substr($content,$pos+1);
                    return $this->findMe($word,$content); 
                }                
            }
             
            return true;
        }

        return false;
    }  

    public function addFound($toSearch,$type,$used_in){
        $level = $this->level;
        if (!isset($this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type])){
            $this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type] = [];    
        } 
        if (!isset($this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type][$level])){
            $this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type][$level] = [];    
        }                                          
        if (!in_array($used_in,$this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type][$level])){
            $this->found[$toSearch['type']][$toSearch['name']]['used_in'][$type][$level][] = $used_in; 
        }   
    }    
    
    public function findElements($type){
        if (!isset($this->found[$type])){
            $this->found[$type] == []; 
        }

        $elements = $this->config['package']['elements'][$type];
        if (is_array($elements)) {
            foreach ($elements as $element){
                $element_name = $element['name'];
                if ($element_name == 'mv_tpl_details'){
                    echo $element_name;
                }
                if (!isset($this->found[$type][$element_name])){
                    $this->found[$type][$element_name] = []; 
                }
                if (!isset($this->found[$type][$element_name]['used_in'])){
                    $this->found[$type][$element_name]['used_in'] = [];    
                }
                if (isset($this->used_elements[$type][$element_name]['used'])){
                    $this->found[$type][$element_name]['used'] = $this->used_elements[$type][$element_name]['used'];    
                }                                                

                $toSearch = ['name' => $element['name'],'type'=>$type];

                if ($this->level == 1){
                    $this->searchInElements($toSearch,'templates');
    
                    $this->searchInResources($toSearch,$this->found['resources']); 
                    $folder = $this->fbuchCorePath . 'migxconfigs';
                    $this->searchInFolder($toSearch,$folder);
                    $folder = $this->fbuchCorePath . 'model/fbuch';
                    $this->searchInFolder($toSearch,$folder);                
                    $folder = $this->fbuchCorePath . 'rest/Controllers';
                    $this->searchInFolder($toSearch,$folder);
                    $folder = $this->fbuchAssetsPath . 'quasar';
                    $this->searchInFolder($toSearch,$folder,true);                    
                } else {
                    $this->searchInElements($toSearch,'chunks');
                    $this->searchInElements($toSearch,'snippets');                      
                }

                        
            }
            
        }
    }

    public function makeTree(){
        foreach ($this->found as $name => $found) {
            $used_in = $found['used_in'];
            if (count($used_in)>0){
                foreach ($used_in as $word => $value){
                    $this->found[$name]['used_in'][$word]['used_in']=$this->found[$word]['used_in'];      
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
                $used_in = $element['name'];
                if ($this->level > 1){
                    if (!in_array($used_in,$this->elements_found_in_level[$type])){
                        continue;        
                    }                
                }
                $file = $element['file'];
                if ($content = file_get_contents($folder.$file)){
                    if ($result = $this->findMe($word,$content)){
                        
                        if ($result === true){
                            $this->addFound($toSearch,$type,$used_in);
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
                $used_in = $resource->get('id') . ' (' . $resource->get('pagetitle') . ')';
                if ($content = $resource->get('content')){
                    if ($result = $this->findMe($word,$content)){
                        $type = 'resources';
                        if ($result === true){
                            $this->addFound($toSearch,$type,$used_in); 
                        }
                       
                    }
                }
                if ($content = $resource->get('pagetitle')){
                    if ($result = $this->findMe($word,$content)){
                        $type = 'resources';
                        if ($result === true){
                            $this->addFound($toSearch,$type,$used_in); 
                        }
                       
                    }
                }                
                if ($content = $resource->getTVValue('scripts')){
                    if ($result = $this->findMe($word,$content)){
                        $type = 'resources';
                        if ($result === true){
                            $this->addFound($toSearch,$type,$used_in); 
                        }
                       
                    }
                } 
                if ($content = $resource->getTVValue('headscripts')){
                    if ($result = $this->findMe($word,$content)){
                        $type = 'resources';
                        if ($result === true){
                            $this->addFound($toSearch,$type,$used_in); 
                        }
                       
                    }
                }                               
            }
        }                          
    }    

    public function searchInFolder($toSearch,$folder,$recursive=false){
        $word = $toSearch['name'];
        $this->files=[];
        $this->dirWalk($folder,null,$recursive);
        foreach ($this->files as $file => $folder){
            $filepath = $folder . '/' . $file;
            $content = file_get_contents($filepath);
            
            if ($result = $this->findMe($word,$content)){
                $used_in = $filepath;
                $type = 'files';
                if ($result === true){
                    $this->addFound($toSearch,$type,$used_in);   
                }
               
            }            
        }       
    }

    
    public function getConfig(){
        return file_get_contents($this->fbuchBuildPath.'config.json');        
    }

    public function getUsedElements(){
        $result_folder = $this->fbuchCorePath . 'customchunks/';
        $result_file = $result_folder . 'used_elements.json';
        $used_elements = [];
        
        if (file_exists($result_file)){
            $old_content = json_decode(file_get_contents($result_file),true);   
            if (is_array($old_content)){
                $used_elements = $old_content;        
            }
        }
        return $used_elements;      
    } 

    public function readOrphans(){
        $result_folder = $this->fbuchCorePath . 'customchunks/';
        $result_file = $result_folder . 'used_and_unused_elements.json';
        $orphans = [];

        $this->chunks_found_in = [];
        $this->snippets_found_in = [];
        
        $level = $this->level-1;
        
        if (file_exists($result_file)){
            $old_content = json_decode(file_get_contents($result_file),true);   
            if (is_array($old_content)){
                $this->found = $orphans = $old_content;
                if (is_array($orphans['snippets'])){
                    foreach ($orphans['snippets'] as $name => $value){
                        if (is_array($value['used_in'])){
                            foreach ($value['used_in'] as $type => $type_value){
                                if (is_array($type_value[$level]) && count($type_value[$level])){
                                    $this->elements_found_in_level['snippets'][$name] = $name; 
                                }
                            }
                        }
                    }                    
                }
                if (is_array($orphans['chunks'])){
                    foreach ($orphans['chunks'] as $name => $value){
                        if (is_array($value['used_in'])){
                            foreach ($value['used_in'] as $type => $type_value){
                                if (is_array($type_value[$level]) && count($type_value[$level])){
                                    $this->elements_found_in_level['chunks'][$name] = $name;    
                                }
                            }
                        }
                    }                    
                }                
            }
        }
        return $orphans;      
    } 

    public function extractUnusedElements($orphans,$type){
        
        if (is_array($this->found[$type])){
            if (!isset($orphans[$type])){
                $orphans[$type] = [];
            }
            foreach ($this->found[$type] as $name => $value){
                if (empty($value['used_in']) && empty($value['used'])){
                    $orphans[$type][] = $name;
                }
            }
        }
        return $orphans;
    }

    public function writeOrphans(){       
        $result_folder = $this->fbuchCorePath . 'customchunks/';
        $orphans = [];
        $orphans = $this->extractUnusedElements($orphans,'snippets');
        $orphans = $this->extractUnusedElements($orphans,'chunks');
 
        $content = json_encode($orphans,JSON_PRETTY_PRINT);
        $result_file = $result_folder . 'unused_elements.json';
        file_put_contents($result_file,$content);

        $content = json_encode($this->found,JSON_PRETTY_PRINT);
         $result_file = $result_folder . 'used_and_unused_elements.json';
        file_put_contents($result_file,$content); 
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