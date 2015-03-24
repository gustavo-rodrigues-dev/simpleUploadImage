<?php
include_once("controller/Crop.php");
$Crop = new Crop();

$mod = (isset($_REQUEST['mod']))? $_REQUEST['mod']  : false;
if(isset($_FILES["file"]) && $mod == 'open'){
    $Crop->open($_FILES["file"]);
} elseif($mod == "save"){
    $Crop->save($_REQUEST);


} else{
    $Crop->index();
}