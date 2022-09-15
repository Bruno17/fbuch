<?php

$dl_center_download_parent = 29;
$dl_center_download_resource = 30;
$dl_center_maildownload_resource = 28;

$packageName = 'fbuch';

$packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
$modelpath = $packagepath . 'model/';
$modx->addPackage($packageName, $modelpath);

function downloadfile($filePath,$filename) {
    global $modx;
    //echo $filename;die();
    
    $pathinfo = pathinfo($filePath);
    
    //print_r($pathinfo);die();

    $extension = $pathinfo['extension'];
    
    ob_end_clean(); //added to fix ZIP file corruption
    ob_start(); //added to fix ZIP file corruption

    header('Pragma: public');  // required
    header('Expires: 0');  // no cache
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');
    header('Content-Description: File Transfer');
    header('Content-Type:'); //added to fix ZIP file corruption
    header('Content-Type: "application/force-download"');
    header('Content-Disposition: attachment; filename="' . $pathinfo['basename'] . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . (string) (filesize($filePath))); // provide file size
    header('Connection: close');
    sleep(1);

    //Close the session to allow for header() to be sent
    session_write_close();
    ob_flush();
    flush();
    
    $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
    $buffer = '';
    $handle = @fopen($filePath, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle) && connection_status() == 0) {
        $buffer = @fread($handle, $chunksize);
        if (!$buffer) {
            die();
        }
        echo $buffer;
        ob_flush();
        flush();
    }
    fclose($handle);            

    exit;    
}

switch ($modx->event->name) {
    case 'OnLoadWebDocument':
        $id = $modx->resource->get('id');
        $alias = $modx->resource->get('alias');
        $parent = $modx->resource->get('parent');
        
        if ($alias == 'mail-downloads'){
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

        break;
    case 'OnPageNotFound':

        /* handle redirects */
        $search = $_SERVER['REQUEST_URI'];
        $base_url = $modx->getOption('base_url');
        if ($base_url != '/') {
            $search = str_replace($base_url, '', $search);
        }

        /* figure out archiving */
        $params = explode('?', $search);
        $search = isset($params[0]) ? $params[0] : '';
        $params = isset($params[1]) ? $params[1] : '';

        $parts = explode('/', $search);

        if (isset($parts[1]) && $parts[1] == 'downloads') {
            $filename = str_replace('/downloads/', '', $search);
            $filepath = 'filedownloads/attachments/';
            $fullpath = $modx->getOption('base_path') . $filepath;
            $file = $fullpath . $filename;
            if (file_exists($file)) {
                if (isset($_SESSION['current_dl_mail'])){
                    $has_access = false;
                    $documents = $modx->fromJson($_SESSION['current_dl_mail']['documents']);
                    //print_r($documents);die();
                    if (is_array($documents)){
                        foreach ($documents as $document){
                            if ($filename == $document['file']){
                                $has_access = true;
                                //echo 'has_access';die();
                            }
                        }
                    }
                    
                    if (!$has_access) {
                        $modx->sendUnauthorizedPage();
                    }            
                    
                    if ($has_access){
                        downloadfile($file,$filename);
                    }
                    
                }                

            }

        }

        return;
        break;
}