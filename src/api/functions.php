<?php

class AllocationDB extends SQLite3
{
    public function __construct()
    {
        $this->open('allocations.sqlite3');

        // Check for setup
        $this->exec('CREATE TABLE IF NOT EXISTS `recent_allocations` (
                        	`id`	INTEGER PRIMARY KEY AUTOINCREMENT,
                        	`squawk`	TEXT NOT NULL,
                        	`allocated_at`	TEXT NOT NULL
                        )');
    }
}

function parseSquawkRange($input)
{
    $split = explode("-", $input);
    return $split;
}

function findSquawk($range, $final = false)
{
    $shouldSearchRange = true;
    while ($shouldSearchRange) {
        $result = outputSquawk($range, $final);
        if ($result != 2) {
            $shouldSearchRange = false;
        }
    }

    return $result;
}

function initAllocationDB()
{
    if (!file_exists('.noallocation')) {
        return new AllocationDB();
    }
    return null;
}

$timesTried = 0;
$squawkFound = false;
function outputSquawk($range, $final = false)
{
    // Response of false = Couldn't find a squawk in range
    // Response of 2 (int) = Keep searching, random squawk generated not suitible
    // Response of true (i.e a squawk) = Squawk found (and allocated)
    global $reservedCodes, $allocationDB, $timesTried, $squawkFound;
    $timesTried++;
    $bypassAllocatedCheck = false;
    $rangeOfRange = abs($range[1]-$range[0]) + 1;

    if (($timesTried >= 40 || $timesTried > $rangeOfRange) && $final) {
        // Just generate a random one...
        $bypassAllocatedCheck = true;
        $number = rand(0, 7777);
    } elseif ($timesTried >= 40 || $timesTried > $rangeOfRange) {
        return false;
    } else {
        // Pick a code at random from the range
        $number = rand($range[0], $range[1]);
    }
    
    $output = 0;

    // Check if the code it has given is 3 digits. If so, append a 0 at the start
    if ($number < 1000) {
        $output = "0" . $number;
    } else {
        $output = $number;
    }

    // Check if code is within the octal range
    $splitCode = str_split($number);
    foreach ($splitCode as $digit) {
        if ($digit > 7) {
            // Get another!
            return 2;
        }
    }


    // Check if it is reserved
    if (array_search($output, $reservedCodes)) {
        // Get another!
        return 2;
    }

    if ($bypassAllocatedCheck) {
        return $output;
    }


    $allocationTableName = "recent_allocations";
    if (!file_exists('.noallocation')) {
        // Check for recent allocation
        $allocationDB = initAllocationDB();
        $res = $allocationDB->query("SELECT allocated_at FROM ".$allocationTableName." WHERE squawk='".$output."'");
        if ($arr = $res->fetchArray(SQLITE3_NUM)) {
            if (timestampExpired($arr[0])) {
                $allocationDB->exec("UPDATE ".$allocationTableName." SET allocated_at='".date('Y-m-d H:i')."' WHERE squawk = '".$output."'");
                $allocationDB->close();
                return 2;
            }
            // Get another!
            $allocationDB->close();
            outputSquawk($range);
        } else {
            // Output the Squawk Code
            $allocationDB->exec("INSERT INTO ".$allocationTableName." (squawk, allocated_at) VALUES ('".$output."','".date('Y-m-d H:i')."')");
            $allocationDB->close();
            return $output;
        }
    } else {
        return $output;
    }
}

function runICAOChecks()
{
    global $destICAO;
    global $depICAO;
    global $db;
    $destICAO = strtoupper($destICAO);
    $depICAO = strtoupper($depICAO);
    if (strlen($destICAO) != 4) {
        die();
    }
    if ($depICAO != "" && strlen($destICAO) != 4) {
        die();
    }
    $GLOBALS['destICAO'] = $db::escapeString(htmlspecialchars($destICAO));
    $GLOBALS['depICAO'] = $db::escapeString(htmlspecialchars($depICAO));
}

function hasDepartureAirport()
{
    global $depICAO;
    if ($depICAO != "") {
        return true;
    }
    return false;
}

function timestampExpired($timestamp)
{
    $alloactedAt = strtotime($timestamp);
    $diff = round((time()-$alloactedAt)/60);

    $diffRequired = 45; //in min

    if ($diff >= $diffRequired) {
        return true;
    }
    return false;
}
