<?php
include("functions.php");

$requiredInput = array("destICAO");

$reservedCodes = array("1200", "7500", "7600", "7000", "7777", "1000", "2000", "7007", "0024","0033","0450","1177","4520","7001","7002","7003","7004","7005","7006","7010","7401", "0002","7776","0010","0011","0012","0013","0440","2620","2677","3660","4517","4572","5077","6170","7045","7366");

$tableName = "squawks";

$db = new SQLite3('database.sqlite3');


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
