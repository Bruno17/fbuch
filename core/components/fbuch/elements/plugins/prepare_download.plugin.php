<?php
$dl_center_download_parent = 11;
$dl_center_download_resource = 10;
$dl_center_maildownload_resource = 3;

$dl_center_download_parent = 29;
$dl_center_download_resource = 30;
$dl_center_maildownload_resource = 28;

$packageName = 'fbuch';

$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
$modx->addPackage($packageName, $modelpath);

switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        $id = $modx->resource->get('id');
        $parent = $modx->resource->get('parent');
        
        if ($id == $dl_center_maildownload_resource){
            $code = $modx->getOption('code', $_REQUEST, '');
            $classname = 'mvMail';
            if (!empty($code)){
                if ($mail = $modx->getObject($classname,array('hash'=>$code))){
                    $_SESSION['current_dl_mail'] = $mail->toArray();  
                } else {
                    unset($_SESSION['current_dl_mail']);
                }
            }else{
                unset($_SESSION['current_dl_mail']);
            }
        }

        //$path = 'viacordownloads/';
        if ($parent == $dl_center_download_parent) {
            $filepath = $modx->getOption('dl_filepath', $_GET, '');
            $filename = $modx->getOption('dl_filename', $_GET, '');
            $fullpath = $modx->getOption('base_path');
            $has_access = false;

            if (!empty($filename) && file_exists($fullpath . $filepath . $filename)) {

                $extension = substr(strrchr($filename, "."), 1);
                $content_type = 'application/pdf';
                $classname = 'modContentType';
                if ($object = $modx->getObject($classname, array('file_extensions' => '.' . $extension))) {
                    $content_type = $object->get('mime_type');
                    $modx->resource->set('contentType', $content_type);
                    $modx->resource->set('content', $filepath . $filename);
                    $modx->resource->set('uri', $modx->resource->cleanAlias($filepath . $filename));
                    
                    if (isset($_SESSION['current_dl_mail'])){
                        $documents = $modx->fromJson($_SESSION['current_dl_mail']['documents']);
                        if (is_array($documents)){
                            foreach ($documents as $document){
                                if ($filename == $document['file']){
                                    $has_access = true;
                                }
                            }
                        }    
                        
                    }

                }


                /*
                if ($dlogMedia_o = $object->getOne('Media')) {
                $filetype_id = $dlogMedia_o->get('filetype_id');
                $filename = $path . $object->get('filename');
                
                if($rg_collection = $dlogMedia_o->getMany('ResourceGroups')){
                //user needs to have access to all resourcegroups, where this download is connected to
                $user_resourcegroups = $modx->user->getResourceGroups();
                $has_access = true;
                foreach ($rg_collection as $rg_o){
                $rg = $rg_o->get('resourcegroup_id');
                if (!in_array($rg,$user_resourcegroups)){
                $has_access = false;    
                }
                }
                if (!$has_access){
                $modx->sendUnauthorizedPage(); 
                }
                } 
                if (!empty($filename)) {
                $modx->resource->set('contentType', $filetype_id);
                $modx->resource->set('content', $filename);
                $modx->resource->set('uri', $modx->resource->cleanAlias($filename));
                }

                }
                */

            }
            if (!$has_access) {
                $modx->sendUnauthorizedPage();
            }

        }
        break;
    case 'OnPageNotFound':

        /* handle redirects */
        $search = $_SERVER['REQUEST_URI'];
        $base_url = $modx->getOption('base_url');
        if ($base_url != '/') {
            $search = str_replace($base_url, '', $search);
        }

        /* get resource to redirect to */
        //return false;

        /* figure out archiving */
        $params = explode('?', $search);
        $search = isset($params[0]) ? $params[0] : '';
        $params = isset($params[1]) ? $params[1] : '';

        $parts = explode('/', $search);

        if (isset($parts[1]) && $parts[1] == 'downloads') {
            $filename = str_replace('/downloads/', '', $search);
            $filepath = 'filedownloads/attachments/';
            $fullpath = $modx->getOption('base_path') . $filepath;

            $extension = substr(strrchr($filename, "."), 1);
            if (file_exists($fullpath . $filename)) {
                $_REQUEST['dl_filename'] = $_GET['dl_filename'] = $filename;
                $_REQUEST['dl_filepath'] = $_GET['dl_filepath'] = $filepath;
                //$pageid = $media_object->get('filetype_id');
                $pageid = $dl_center_download_resource;
                //$modx->toPlaceholders($object->toArray(), 'dlog');
                $modx->sendForward($pageid);
            }

        }

        return;
        break;
}