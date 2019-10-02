<?php
/*include("db_login.php");
include("station_name.php");

$cd = array();

$cd_query = 'select ' .
            'StationKey,' .
            'TS,' .
            'AirT_2m as AirT,' .
            'dewpt(AirT_2m,RH_2m) as DewPt,' .
            'rh(RH_2m) as RH,' .
            'WndDir_10m as WndDir,' .
            'WndSpd_10m as WndSpd,' .
            'Press_sealev_1 as SL_Pressure,' .
            'Precip_TB3_Today as Precip ' .
            'from public order by StationKey asc';


$connection = mysql_connect($db_host,$db_username,$db_password);
if (!$connection) {
  die("Could not connect to the database: " . mysql_error());
}
$db_select = mysql_select_db($db_database);
if (!$db_select) {
  die("Could not select the database: " . mysql_error());
}

// Collect Current data for each station
$query = $cd_query;
$result = mysql_query($query);
if (!$result) {
  die("Could not query the database: " . mysql_error());
}
if (mysql_num_rows($result) == 0) {
  die("There is no data available in the public table");
}
while ($row = mysql_fetch_assoc($result)) {
  if (in_array($row['StationKey'],$retired)) {
    continue;
  }
  $first = TRUE;
  foreach ($row as $key => $value) {
    if ($first) {
      $StationKey = $value;
      $first = FALSE;
    } else {
      $cd[$StationKey][$key] = $value;
    }
  }
}

// If Wind Direction is slightly negative adjust it
foreach (array_keys($cd) as $station) {
  if ($cd[$station]['WndDir']!== NULL && $cd[$station]['WndDir'] < 0) {
  	if ($cd[$station]['WndDir'] < -2) {
  	  $cd[$station]['WndDir'] = "N/A";
  	} else {
  	  $cd[$station]['WndDir'] += 360;
  	}
  }
}

// Prepare the variables to be displayed by setting any NULL values to "N/A"
foreach (array_keys($cd) as $station) {
  foreach ($cd[$station] as $key => $value) {
	if ($value === NULL) {
	  $cd[$station][$key] = "N/A";
	}
  }
}

// Remove all entries more than 1 hour old from the $cd.
foreach (array_keys($cd) as $station) {
  if ($cd[$station]['TS']=='N/A' || (strtotime('now') - strtotime($cd[$station]['TS'])) > 86400) {
    unset($cd[$station]);
  }
}



// Define some useful conversion constants
$fiveninths = 5/9;
$ninefifths = 9/5;
$mm2inches = 0.0393700787;
$mb2inHg =  0.029529983071;
$wpsqm2lymin = 0.00143197;
$mps2Mph = 2.2369363;
// Define constants for Dew Point calculation (Deg C)
$a = 17.271;
$b = 237.7;
// Define constants for Heat Index calculation (Deg F)
$hi1 = 42.379;
$hi2 = 2.04901523;
$hi3 = 10.14333127;
$hi4 = 0.22475541;
$hi5 = 0.00683783;
$hi6 = 0.05481717;
$hi7 = 0.00122874;
$hi8 = 0.00085282;
$hi9 = 0.00000199;
// Define constants for Wind Chill calculation (Deg F)
$wc1 = 35.74;
$wc2 = 0.6215;
$wc3 = 35.75;
$wc4 = 0.4275;
 
// Calculate Dew point for "Current" data values
foreach (array_keys($cd) as $station) {
  // Dew Point at 2m
  if ($cd[$station]['AirT'] == "N/A" ||
      $cd[$station]['RH'] == "N/A" ||
      $cd[$station]['AirT'] >= 60 ||
      // $cd[$station]['AirT'] < 0 ||
      $cd[$station]['RH'] > 100 ||
      $cd[$station]['RH'] < 1) {
    $cd[$station]['DewPt'] = "N/A";
  } else {
  // Define constants for Dew Point calculation (Deg C)
    if ($cd[$station]['AirT'] < 0 ) {
      $a = 22.452;
      $b = 272.55;
    } else {
      $a = 17.502;
      $b = 240.97;
    }
    $gamma = (($a * $cd[$station]['AirT'])/($b + $cd[$station]['AirT'])) + log($cd[$station]['RH'] / 100);
    $cd[$station]['DewPt'] = ($b * $gamma)/($a - $gamma);
  }
}

// Copy data arrays for English unit conversions
$cd_en = $cd;
$te_en = $te;
$ye_en = $ye;

// Prepare English unit versions of the "Current" data values
foreach (array_keys($cd) as $station) {
  // Air Temperature at 2m
  if (is_numeric($cd[$station]['AirT'])) {
    $cd_en[$station]['AirT'] = round($ninefifths * $cd[$station]['AirT'] + 32,2);
  }
  // Dew Point at 2m
  if (is_numeric($cd[$station]['DewPt'])) {
    $cd_en[$station]['DewPt'] = $cd[$station]['DewPt'] * $ninefifths + 32;
  }
  // Wind Speed at 10m
  if (is_numeric($cd[$station]['WndSpd'])) {
    $cd_en[$station]['WndSpd'] = $cd[$station]['WndSpd'] * $mps2Mph;
  }
  // Sea Level Pressure
  if (is_numeric($cd[$station]['SL_Pressure'])) {
    $cd_en[$station]['SL_Pressure'] = $cd[$station]['SL_Pressure'] * $mb2inHg;
  }
  // Total Precipitation Today
  if (is_numeric($cd[$station]['Precip'])) {
    $cd_en[$station]['Precip'] = $cd[$station]['Precip'] * $mm2inches;
  }
}


// Set display units
if (!empty($_GET['unit'])) {
   $unit = strtolower($_GET['unit']);
   if ($unit != "en" && $unit != "mt") {
   	$unit = "en";
   }
} else {
   $unit = "en";
}
if ($unit=='en') {
  $cd = $cd_en;
  $te = $te_en;
  $ye = $ye_en;
}

// Determine if Heat Index or Wind Chill was specified in URL
if (!empty($_GET['felt'])) {
   $felt = strtolower($_GET['felt']);
   if ($felt != "ht" && $felt != "wc") {
   	$felt = "dy";
   }
} else {
   $felt = "dy";
}

// If neither Heat Index nor Wind Chill was specified
// then choose one based on current conditions
if ($felt == "dy") {
  $tottemp = 0;
  $totwdsp = 0;
  $stncnt = 0;
  foreach (array_keys($cd_en) as $station) {
    if ($cd_en[$station]['AirT'] == "N/A" || $cd_en[$station]['WndSpd'] == "N/A") continue;
    ++$stncnt;
    $tottemp += $cd_en[$station]['AirT'];
    $totwdsp += $cd_en[$station]['WndSpd'];
  }
  // Windchill Temperature is only defined for temperatures
  // at or below 50 degrees F and wind speeds above 3 mph
  if (($tottemp / $stncnt) <= 50 && ($totwdsp / $stncnt) > 3) {
    $felt = "wc";
  } else {
    $felt = "ht";
  }
}

// Calculate either the Heat Index OR the Windchill for each station
if ($felt == "ht") {
  // Heat Index calculation per NWS
  foreach (array_keys($cd_en) as $station) {
    if ($cd_en[$station]['AirT'] == "N/A" ||
        $cd_en[$station]['DewPt'] == "N/A" ||
        $cd_en[$station]['RH'] == "N/A" ||
        $cd_en[$station]['AirT'] <= 80 ||
        $cd_en[$station]['DewPt'] <= 54 ||
        $cd_en[$station]['RH'] <= 40) {
      $cd_en[$station]['HtIdx'] = "N/A";
    } else {
      $t  = $cd_en[$station]['AirT'];
      $t2 = $t * $t;
      $r  = $cd_en[$station]['RH'];
      $r2 = $r * $r;
      $cd_en[$station]['HtIdx'] = $hi2 * $t + 
                                  $hi3 * $r - 
                                  $hi4 * $t * $r - 
                                  $hi5 * $t2 - 
                                  $hi6 * $r2 + 
                                  $hi7 * $t2 * $r + 
                                  $hi8 * $t * $r2 - 
                                  $hi9 * $t2 * $r2 - $hi1;
      if ($cd_en[$station]['HtIdx'] < $cd_en[$station]['AirT']) {
        $cd_en[$station][$fh[$felt]] = "N/A";
      }
    }
  }
} else {
  // Windchill calculation per NWS
  foreach (array_keys($cd_en) as $station) {
    if ($cd_en[$station]['AirT'] == "N/A" || $cd_en[$station]['WndSpd'] == "N/A") {
      $cd_en[$station][$fh[$felt]] = "N/A";
      continue;
    }
    if ($cd_en[$station]['AirT'] > 50.0 || $cd_en[$station]['WndSpd'] <= 3.0) {
      $cd_en[$station][$fh[$felt]] = "N/A";
      continue;
    }
    $cd_en[$station][$fh[$felt]] = $wc1 + 
                                   $wc2 * $cd_en[$station]['AirT'] - 
                                   $wc3 * pow($cd_en[$station]['WndSpd'], 0.16) + 
                                   $wc4 * $cd_en[$station]['AirT'] * pow($cd_en[$station]['WndSpd'], 0.16);
  }
}

foreach (array_keys($cd_en) as $station) {
  if ($cd_en[$station][$fh[$felt]] != "N/A") {
    if ($unit=='en') {
      $cd[$station][$fh[$felt]] = $cd_en[$station][$fh[$felt]];
    } else {
      $cd[$station][$fh[$felt]] = round((($cd_en[$station][$fh[$felt]] - 32) * $fiveninths), 2);
    }
  } else {
    $cd[$station][$fh[$felt]] = "N/A";
  }
}
// Set all numeric values to 2 decimal places
foreach (array_keys($cd) as $station) {
  foreach ($cd[$station] as $key => $value) {
    if (is_numeric($value)) {
      $cd[$station][$key] = sprintf('%.2f',$value);
    }
  }
}
*/
$cd = array(
        "agricola" => array("name" => "Agricola",
                            "AirT" => 69.94,
                            "DewPt" => 69.94,
                            "RH" => 100.00,
                            "WndDir" => 85.49,
                            "WindSpd" => 0.00,
                            "Precip" => 0.36,
                            "SL_Pressure" => 30.10,
                            "TS" => "2019-09-19 21:43:38"),
        "bayminette" => array("name" => "Bay Minette",
                              "AirT" => 72.22,
                              "DewPt" => 70.71,
                              "RH" => 95.01,
                              "WndDir" => 89.98,
                              "WndSpd" => 4.02,
                              "Precip" => 0.10,
                              "SL_Pressure" => 30.08,
                              "TS" => "2019-09-19 21:43:38" ),
        "mobiledr" => array("name" => "Mobile(Dog River)",
                            "AirT" => 71.55,
                            "DewPt" => 71.44,
                            "RH"=> 99.65,
                            "WndDir" => 69.31,
                            "WndSpd" => 0.00,
                            "Precip" => 3.90,
                            "SL_Pressure" => 30.10,
                            "TS" => "2019-09-19 21:43:38" ),
        "mobileusaw" => array("name" => "USA Campus West",
                              "AirT" => 70.75,
                              "DewPt" => 70.75,
                              "RH" => 100.00,
                              "WndDir" => 35.73,
                              "WndSpd" => 1.10,
                              "Precip" => 4.85,
                              "SL_Pressure" => 30.10,
                              "TS" => "2019-09-19 21:43:38" )                                    
        );

$jsonObject = json_encode($cd);

echo $jsonObject;

?>
