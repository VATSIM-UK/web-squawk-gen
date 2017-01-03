<?php
include("functions.php");

$requiredInput = array("depICAO", "destICAO");

$reservedCodes = array("1200", "7500", "7600", "7000", "7777");

foreach($requiredInput as $required){
  if(!isset($_GET[$required])){
    die("A required input was not supplied! (" . $required . ")");
  }else{
    $$required = $_GET[$required];
  }
}



$depCountryCode = substr($depICAO, 0, 2);
$destCountryCode = substr($destICAO, 0, 2);


 ?>
