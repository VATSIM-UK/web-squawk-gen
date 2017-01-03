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


$destCountryCode = substr($destICAO, 0, 2);


// 1st Search for the full destination airport code

$query = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destICAO ."'";
$results = $db->query($query);
$numRows = 0;
while($row = $results->fetchArray()){
  $numRows++;
}

if($numRows > 0){
  // Use this range
  $row = $results->fetchArray();  // Take first result
  $range = parseSquawkRange($row['range']);
  outputSquawk($range);
}

// 2nd Search for the destination airport code

$query = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."'";
$results = $db->query($query);
$numRows = 0;
$resultsArray = array();
while($row = $results->fetchArray()){
  $numRows++;
  $resultsArray[] = $row;
}

if($numRows > 0){
  // Use this range

  $possibleRanges = count($resultsArray) - 1;
  $selectedRange = $resultsArray[rand(0, $possibleRanges)];
  $range = parseSquawkRange($selectedRange['range']);
  outputSquawk($range);
}

// 3rd - Havn't found a match for the Full ICAO or the 2 letter country code. Lets try the first letter

$destCountryCode = substr($destICAO, 0, 1);

$query = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."'";
$results = $db->query($query);
$numRows = 0;
$resultsArray = array();
while($row = $results->fetchArray()){
  $numRows++;
  $resultsArray[] = $row;
}

if($numRows > 0){
  // Use this range

  $possibleRanges = count($resultsArray) - 1;
  $selectedRange = $resultsArray[rand(0, $possibleRanges)];
  $range = parseSquawkRange($selectedRange['range']);
  outputSquawk($range);
}

// 4th - Fallback. Havn't found a squawk, so lets use ORCAM

$destCountryCode = "ORCAM";

$query = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."'";
$results = $db->query($query);
$numRows = 0;
$resultsArray = array();
while($row = $results->fetchArray()){
  $numRows++;
  $resultsArray[] = $row;
}

if($numRows > 0){
  // Use this range

  $possibleRanges = count($resultsArray) - 1;
  $selectedRange = $resultsArray[rand(0, $possibleRanges)];
  $range = parseSquawkRange($selectedRange['range']);
  outputSquawk($range);
}

 ?>
