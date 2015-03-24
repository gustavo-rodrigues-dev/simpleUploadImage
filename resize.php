<?php
include_once("controller/Crop.php");
$Crop = new Crop();
$mod = (isset($_REQUEST['mod']))? $_REQUEST['mod']  : false;
if(isset($_FILES["file"]) && $mod == 'resize'){
    $Crop->resize($_FILES["file"], $_REQUEST['noresize']);
} else{
    $Crop->indexResize();
}