<?php

function parseSquawkRange($input){
  $split = explode("-", $input);
  return $split;

}

function outputSquawk($range){
    global $reservedCodes;

    // Pick a code at random from the range
    $number = rand($range[0], $range[1]);
    $output = 0;

    // Check if the code it has given is 3 digits. If so, append a 0 at the start
    if($number < 1000){
      $output = "0" . $number;
    }else{
      $output = $number;
    }

    // Check if it is reserved
    if(array_search($output, $reservedCodes)){
      // Get another!
      outputSquawk($range);
    }else{
      // Output the Squawk Code
      echo $output;
      exit();
    }
}

function runICAOChecks(){
  global $destICAO;
  global $db;

  if(strlen($destICAO) != 4){
    die();
  }
  if(!ctype_upper($destICAO)){
    die();
  }

  $GLOBALS['destICAO'] = htmlspecialchars($destICAO);
  $GLOBALS['destICAO'] = $db::escapeString($destICAO);
}




 ?>
