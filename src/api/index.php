<?php
include("functions.php");

$requiredInput = array("destICAO");

$reservedCodes = array("1200", "7500", "7700", "7600", "7000", "7777", "1000", "2000", "7007", "0024","0033","0450","1177","4520","7001","7002","7003","7004","7005","7006","7010","7401", "0002","7776","0010","0011","0012","0013","0440","2620","2677","3660","4517","4572","5077","6170","7045","7366","2200");

$tableName = "squawks";
$db = new SQLite3('database.sqlite3');

foreach ($requiredInput as $required) {
    if (!isset($_GET[$required])) {
        die("A required input was not supplied! (" . $required . ")");
    } else {
        $$required = $_GET[$required];
    }
}

if (isset($_GET["depICAO"])) {
    $depICAO = $_GET["depICAO"];
}

runICAOChecks();

$destCountryCode = substr($destICAO, 0, 2);
$destCountryCodeFirst = substr($destICAO, 0, 1);

$queriesToExecute = [];

// 1st Search for the full destination airport code (& departure airport code if given)
// 2nd Search for the destination airport code
// 3rd - Havn't found a match for the Full ICAO or the 2 letter country code. Lets try the first letter
// 4th - Fallback. Havn't found a squawk, so lets use ORCAM


if (hasDepartureAirport()) {
    //1
    $queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destICAO ."' AND depCode='" . $depICAO ."'";

    //2
    $queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."' AND depCode='" . $depICAO ."'";

    //3
    $queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCodeFirst ."' AND depCode='" . $depICAO ."'";

    // Incase of "other"s
    $queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode IS NULL AND depCode='" . $depICAO ."'";
}

$queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destICAO ."'";

$queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."'";

$queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCodeFirst ."'";

//4

$destCountryCode = "ORCAM";

$queriesToExecute[] = "SELECT * FROM " . $tableName . " WHERE destCode='" . $destCountryCode ."'";

$counter=0;
// Run queries in the selected order
var_dump($queriesToExecute);
foreach ($queriesToExecute as $query) {
    $counter++;
    $results = $db->query($query);
    $numRows = 0;
    $resultsArray = array();
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $numRows++;
        $resultsArray[] = $row;
    }
    if ($numRows > 0) {
        // Use this range
        $possibleRanges = count($resultsArray) - 1;
        $selectedRange = $resultsArray[rand(0, $possibleRanges)];
        $range = parseSquawkRange($selectedRange['range']);
        echo $query;
        if (count($queriesToExecute) == $counter) {
            $a = findSquawk($range, true);
        } else {
            $a = findSquawk($range);
        }

        if ($a) {
            echo $a;
            exit();
        }
    }
}
