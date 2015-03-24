<?php
date_default_timezone_set('America/Sao_Paulo');
include_once("./lib/ImageModify.php");
class Crop {
    function index($width= 800, $heigth = 600){
        $path = "temp/";
        $uploadAction = "?".http_build_query(array(
            'destination'   =>  "temp/",
            'mod'           =>  "open",
            'width'         =>  $width,
            'height'        =>  $heigth,
            'max-height'    => $_REQUEST['max-height']
        ));
        include_once "./view/crop-upload.php";
    }

    function open($files){
        if ($files["error"] == UPLOAD_ERR_OK) {
            $path = $_REQUEST['destination'];
            $extencion = ".".end(explode('.', $files["name"]));
            $name = md5(str_shuffle($files["name"]).microtime());
            $destination = $path.$name.$extencion;
            $imageModify = new ImageModify();
            $imageModify->setModeResize(1); //Propotional

            $imageModify
                ->openByFile($files)
                ->resize($_REQUEST['width'],$_REQUEST['height'], "#ffffff")
                ->saveImage($destination);

            $uploadAction = "index.php?".http_build_query(array(
                    'destination'   =>  "temp/tmb/",
                    'mod'           =>  "save",
                    'width'         =>  $_REQUEST['width'],
                    'height'        =>  $_REQUEST['height'],
                    'file'          =>  $destination,
                    'max-height'    => $_REQUEST['max-height']
                ));
            include_once "./view/crop-resize.php";
        }
    }

    function save($options){
        $files = $options['file'];
        if (is_file($files)) {
            $path = $options['destination'];
            $name = end(explode('/', $files));
            $destination = $path.$name;
            $file = $name;

            include( './lib/m2brimagem.class.php' );
            $oImg = new m2brimagem( $files );
            if( $oImg->valida() == 'OK' )
            {
                $oImg->posicaoCrop( $_POST['x'], $_POST['y'] );
                $oImg->redimensiona( $_POST['w'], $_POST['h'], 'crop' );
                $oImg->grava( $destination );
                unlink($files);
                if($_POST['h'] > $_REQUEST['max-height']){
                    $imageModify = new ImageModify();
                    $imageModify->setModeResize($imageModify::RESIZE_MODE_AR_HEIGHT); //Propotional
                    $imageModify
                        ->open(realpath($destination))
                        ->resize($_POST['w'],$_REQUEST['max-height'], "#ffffff")
                        ->saveImage(realpath($destination));
                }

                include_once('view/save.php');
            }
        }
    }
    function indexResize($width= 800, $heigth = 600){
        $path = "temp/";
        $uploadAction = "?".http_build_query(array(
                'destination'   =>  "temp/tmb/",
                'mod'           =>  "resize",
                'width'         =>  $width,
                'height'        =>  $heigth
            ));
        include_once "./view/crop-upload.php";
    }
    function resize($files, $noresize = false){
        if ($files["error"] == UPLOAD_ERR_OK) {
            $path = $_REQUEST['destination'];
            $extencion = ".".end(explode('.', $files["name"]));
            $name = md5(str_shuffle($files["name"]).microtime());
            $destination = $path.$name.$extencion;
            $file = $name.$extencion;
            $imageModify = new ImageModify();
            $imageModify->setModeResize(5); //Propotional
            if($noresize){
                $imageModify
                    ->openByFile($files)
                    ->saveImage($destination);
            } else {
                $imageModify
                    ->openByFile($files)
                    ->resize($_REQUEST['width'],$_REQUEST['height'], "#ffffff")
                    ->saveImage($destination);
            }


            $uploadAction = "index.php?".http_build_query(array(
                    'destination'   =>  $_REQUEST['destination'],
                    'mod'           =>  "save",
                    'width'         =>  $_REQUEST['width'],
                    'height'        =>  $_REQUEST['height'],
                    'file'          =>  $file
                ));
            include_once "./view/save.php";
        }
    }

}