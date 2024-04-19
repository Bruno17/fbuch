<?php

class MyControllerResourceTree extends fbuchRestController {
    
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

    public function prepareTreeNodes($tree){
        $newnodes = [];
        foreach ($tree as $node) {
            $node['haschildren'] = 0;
            if (isset($node['children']) && is_array($node['children'])){
                $node['children'] = $this->prepareTreeNodes($node['children']);
                if (count($node['children']) > 0){
                    $node['haschildren'] = 1;    
                }
            }
            switch ($node['class_key']){
                case 'modWebLink' :
                    $node['link'] = $node['content'];
                    break;
                default :
                $node['link'] = $node['uri'];    
                $node['content'] = '';    
            }
            $node['menutitle'] = empty($node['menutitle']) ? $node['pagetitle'] : $node['menutitle'];
            $node['attributes'] = [];
            if (!empty($node['link_attributes'])){
                $attrs = str_replace('"','',$node['link_attributes']);
                $attributes = explode(' ',$attrs);
                foreach ($attributes as $attribute){
                    $attr = explode('=',$attribute);
                    if (!empty($attr[0]) && !empty($attr[1])){
                        $node['attributes'][$attr[0]] = $attr[1]; 
                    }
                    
                }
            }
            
            $newnodes[] = $node;
        }
        return $newnodes;
    }    
    
    public function getList() {
        $total = 0;

        $scriptProperties['parents'] = '0';
        $scriptProperties['context'] = $this->modx->context->get('key');
        $scriptProperties['checkPermissions'] = 'load';
        $scriptProperties['return'] = 'data';
        $scriptProperties['prepareSnippet'] = 'fbuchPrepareTreeNode';
        $tree = $this->modx->runSnippet('pdoMenu',$scriptProperties);
        
        $list = $this->prepareTreeNodes($tree);
 
        return $this->collection($list,$total);
    }    

}
