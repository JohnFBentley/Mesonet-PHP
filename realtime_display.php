<?php
include("Common/header.php");
include("Common/menu.php");
include("db_login.php");
include("station_name.php");
include("inputValidation.php");

// 2010-10-25 dnb The dew point calculations provided by the MySQL stored function
//                dewpt(t,h) were replaced with a more accurate formula implemented
//                in PHP. The MySQL stored function rh(h) is still used to sanity
//                check all relative humidity values.
// 2010-12-07 dnb Removed dew point limits of below 0 and above 50 Celsius
// 2010-12-07 dnb Provided separate dew point constants for above or below 0 Celsius
// 2011-02-01 dnb Modified Dew point calculation to show N/A if RH > 100 not RH >= 100
// 2011-07-01 dnb Removed display of stations contained in $retired array

// Column header/Name info for the "Felt" temperature column (i.e. "Heat Index" or "Windchill")
$fh = array();
$fh['ht'] = 'HtIdx';
$fh['wc'] = 'Wdchl';
// Current data from Public table
$cd = array();
// Today's Extremes
$te = array();
// Yesterday's Extremes
$ye = array();

// Query string for Current Data returns these variables
// $cd[$station]['TS']
// $cd[$station]['AirT']
// $cd[$station]['DewPt']
// $cd[$station]['RH']
// $cd[$station]['WndDir']
// $cd[$station]['WndSpd']
// $cd[$station]['SL_Pressure']
// $cd[$station]['Precip']
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

// Query strings for Today's Extremes returns these variables
// $te[$station]['TS']
// $te[$station]['AirT_TMx']
// $te[$station]['AirT_Max']
// $te[$station]['AirT_TMn']
// $te[$station]['AirT_Min']
// $te[$station]['DewPt_TMx']
// $te[$station]['DewPt_Max']
// $te[$station]['DewPt_TMn']
// $te[$station]['DewPt_Min']
// $te[$station]['RH_TMx']
// $te[$station]['RH_Max']
// $te[$station]['RH_TMn']
// $te[$station]['RH_Min']
// $te[$station]['WndSpd_TMx']
// $te[$station]['WndSpd_Max']
$te_query = 'select ' .
            'StationKey,' .
            'TS,' .
            'AirT_2m_TMx as AirT_TMx,' .
            'AirT_2m_Max as AirT_Max,' .
            'AirT_2m_TMn as AirT_TMn,' .
            'AirT_2m_Min as AirT_Min,' .
            'DewPt_2m_TMx as DewPt_TMx,' .
            'DewPt_2m_Max as DewPt_Max,' .
            'DewPt_2m_TMn as DewPt_TMn,' .
            'DewPt_2m_Min as DewPt_Min,' .
            'RH_2m_TMx as RH_TMx,' .
            'RH_2m_Max as RH_Max,' .
            'RH_2m_TMn as RH_TMn,' .
            'RH_2m_Min as RH_Min,' .
            'WndSpd_10m_TMx as WndSpd_TMx,' .
            'WndSpd_10m_Max as WndSpd_Max ' .
            'from extremes_tday order by StationKey asc';

// Query strings for Yesterday's Extremes returns these variables
// $ye[$station]['TS']
// $ye[$station]['AirT_TMx']
// $ye[$station]['AirT_Max']
// $ye[$station]['AirT_TMn']
// $ye[$station]['AirT_Min']
// $ye[$station]['DewPt_TMx']
// $ye[$station]['DewPt_Max']
// $ye[$station]['DewPt_TMn']
// $ye[$station]['DewPt_Min']
// $ye[$station]['Precip']
// $ye[$station]['RH_TMx']
// $ye[$station]['RH_Max']
// $ye[$station]['RH_TMn']
// $ye[$station]['RH_Min']
// $ye[$station]['WndSpd_TMx']
// $ye[$station]['WndSpd_Max']
$ye_query = 'select ' .
            'StationKey,' .
            'TS,' .
            'AirT_2m_TMx as AirT_TMx,' .
            'AirT_2m_Max as AirT_Max,' .
            'AirT_2m_TMn as AirT_TMn,' .
            'AirT_2m_Min as AirT_Min,' .
            'DewPt_2m_TMx as DewPt_TMx,' .
            'DewPt_2m_Max as DewPt_Max,' .
            'DewPt_2m_TMn as DewPt_TMn,' .
            'DewPt_2m_Min as DewPt_Min,' .
            'Precip_TB3_Today as Precip,' .
            'RH_2m_TMx as RH_TMx,' .
            'RH_2m_Max as RH_Max,' .
            'RH_2m_TMn as RH_TMn,' .
            'RH_2m_Min as RH_Min,' .
            'WndSpd_10m_TMx as WndSpd_TMx,' .
            'WndSpd_10m_Max as WndSpd_Max ' .
            'from extremes_yday order by StationKey asc';

// Set up database connection and selection
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

// Collect Today's extremes data for each station
$query = $te_query;
$result = mysql_query($query);
if (!$result) {
  die("Could not query the database: " . mysql_error());
}
if (mysql_num_rows($result) == 0) {
  die("There is no data available in the extremes_tday table");
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
      $te[$StationKey][$key] = $value;
    }
  }
}

// Collect Yesterday's extremes data for each station
$query = $ye_query;
$result = mysql_query($query);
if (!$result) {
  die("Could not query the database: " . mysql_error());
}
if (mysql_num_rows($result) == 0) {
  die("There is no data available in the extremes_yday table");
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
      $ye[$StationKey][$key] = $value;
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
foreach (array_keys($te) as $station) {
  foreach ($te[$station] as $key => $value) {
	if ($value === NULL) {
	  $te[$station][$key] = "N/A";
	}
  }
}
foreach (array_keys($ye) as $station) {
  foreach ($ye[$station] as $key => $value) {
	if ($value === NULL) {
	  $ye[$station][$key] = "N/A";
	}
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

// Prepare English unit versions of the "Today's Extremes" data values
foreach (array_keys($te) as $station) {
  // Maximum Wind Speed at 10m
  if (is_numeric($te[$station]['WndSpd_Max'])) {
    $te_en[$station]['WndSpd_Max'] = $te[$station]['WndSpd_Max'] * $mps2Mph;
  }
  // Maximum Air Temperature at 2m
  if (is_numeric($te[$station]['AirT_Max'])) {
    $te_en[$station]['AirT_Max'] = round($ninefifths * $te[$station]['AirT_Max'] + 32,2);
  }
  // Minimum Air Temperature at 2m
  if (is_numeric($te[$station]['AirT_Min'])) {
    $te_en[$station]['AirT_Min'] = round($ninefifths * $te[$station]['AirT_Min'] + 32,2);
  }
  // Maximum Dew Point at 2m
  if (is_numeric($te[$station]['DewPt_Max'])) {
    $te_en[$station]['DewPt_Max'] = round($ninefifths * $te[$station]['DewPt_Max'] + 32,2);
  }
  // Minimum Dew Point at 2m
  if (is_numeric($te[$station]['DewPt_Min'])) {
    $te_en[$station]['DewPt_Min'] = round($ninefifths * $te[$station]['DewPt_Min'] + 32,2);
  }
}

// Prepare English unit versions of the "Yesterday's Extremes" data values
foreach (array_keys($ye) as $station) {
  // Total Precipitation
  if (is_numeric($ye[$station]['Precip'])) {
    $ye_en[$station]['Precip'] = $ye[$station]['Precip'] * $mm2inches;
  }	
  // Maximum Wind Speed at 10m
  if (is_numeric($ye[$station]['WndSpd_Max'])) {
    $ye_en[$station]['WndSpd_Max'] = $ye[$station]['WndSpd_Max'] * $mps2Mph;
  }
  // Maximum Air Temperature at 2m
  if (is_numeric($ye[$station]['AirT_Max'])) {
    $ye_en[$station]['AirT_Max'] = round($ninefifths * $ye[$station]['AirT_Max'] + 32,2);
  }
  // Minimum Air Temperature at 2m
  if (is_numeric($ye[$station]['AirT_Min'])) {
    $ye_en[$station]['AirT_Min'] = round($ninefifths * $ye[$station]['AirT_Min'] + 32,2);
  }
  // Maximum Dew Point at 2m
  if (is_numeric($ye[$station]['DewPt_Max'])) {
    $ye_en[$station]['DewPt_Max'] = round($ninefifths * $ye[$station]['DewPt_Max'] + 32,2);
  }
  // Minimum Dew Point at 2m
  if (is_numeric($ye[$station]['DewPt_Min'])) {
    $ye_en[$station]['DewPt_Min'] = round($ninefifths * $ye[$station]['DewPt_Min'] + 32,2);
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
foreach (array_keys($te) as $station) {
  foreach ($te[$station] as $key => $value) {
    if (is_numeric($value)) {
      $te[$station][$key] = sprintf('%.2f',$value);
    }
  }
}
foreach (array_keys($ye) as $station) {
  foreach ($ye[$station] as $key => $value) {
    if (is_numeric($value)) {
      $ye[$station][$key] = sprintf('%.2f',$value);
    }
  }
}
?>
<?php #include("Common/menu.php"); ?>
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
<div class="panel" style="background-color:rgba(255, 255, 255, 0.4);">
<div class="panel-heading">
<h1 style="text-align:center; font-weight:bold;">USA Mesonet Realtime Display</h1>
<?php
if (date("T") == 'CST') {
  $now=date("m/d H:i:s a");
} else {
  $now=date("m/d H:i:s a",strtotime('-1 hour'));
}
?>
<h2 style="text-align:center; color:purple; font-weight:bold;"><?php echo $now . " CST";?></h2>
<h5 style="text-align:center; font-weight:bold;">
Non&#45;mobile device users please hover the mouse over the maximum or minimum value to obtain a date&#47;time stamp of when the value occurred
</h5>
</div>
<div class="panel-body" style="background-color:rgba(255, 255, 255, 1.0);">
<table class="table table-hover table-bordered table-responsive real-table">
<thead>
<tr class="rowlt">
<th id="<?php echo($felt=='ht'?'wdchl':'htidx')?>"><a id="<?php echo($felt=='ht'?'wdchl':'htidx')?>" href=realtime_display.php?unit=<?php echo($unit)?>&felt=<?php echo($felt=="ht"?"wc":"ht")?>>Display <?php echo($felt=="ht"?"Windchill":"Heat Index")?></a></th>
<th colspan="4" style="text-align:center;">&#64;&nbsp;<?php echo($unit=="en"?"6.56 ft":"2 meters")?></th>
<th colspan="2" style="text-align:center;">&#64;&nbsp;<?php echo($unit=="en"?"32.81 ft":"10 meters")?></th>
<th>SeaLv</th>
<th colspan="5" style="text-align:center;">Today's Extremes (<span class="hi">Hi</span>/<span class="lo">Lo</span>)</th>
<th colspan="5" style="text-align:center;">Yesterday's Extremes (<span class="hi">Hi</span>/<span class="lo">Lo</span>)</th>
</tr>
<tr class="rowlt">
<th>Location/Obs time</th>
<th style="text-align:center;">Temp</th>
<th style="text-align:center;"><?php echo($fh[$felt])?></th>
<th style="text-align:center;">DewPt</th>
<th style="text-align:center;">Hum</th>
<th style="text-align:center;">WdDir</th>
<th style="text-align:center;">WdSpd</th>
<th style="text-align:center;">Press</th>
<th style="text-align:center;">Rain</th>
<th style="text-align:center;">WdSpd</th>
<th style="text-align:center;">Temp</th>
<th style="text-align:center;">DewPt</th>
<th style="text-align:center;">Hum</th>
<th style="text-align:center;">Rain</th>
<th style="text-align:center;">WdSpd</th>
<th style="text-align:center;">Temp</th>
<th style="text-align:center;">DewPt</th>
<th style="text-align:center;">Hum</th>
</tr>
<tr style="text-align:center;" class="rowlt">
<td><div class="stationdata"><a href=realtime_display.php?unit=<?php echo($unit=="en"?"mt":"en")?>&felt=<?php echo($felt)?>>Switch to <?php echo($unit=="en"?"Metric":"English")?> units</a></div></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td>&#37;</td>
<td>&deg;</td>
<td><?php echo($unit=="en"?"mph":"m&#47;s")?></td>
<td><?php echo($unit=="en"?"inHg":"mb")?></td>
<td><?php echo($unit=="en"?"in":"mm")?></td>
<td><?php echo($unit=="en"?"mph":"m&#47;s")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td>&#37;</td>
<td><?php echo($unit=="en"?"in":"mm")?></td>
<td><?php echo($unit=="en"?"mph":"m&#47;s")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td><?php echo($unit=="en"?"&deg;F":"&deg;C")?></td>
<td>&#37;</td>
</tr>
</thead>
<tbody>
<?php 
      foreach (array_keys($cd) as $station) {
          if ($cd[$station]['TS']=='N/A' || (strtotime('now') - strtotime($cd[$station]['TS'])) > 86400) {
          echo("<tr class=\"rowdk\">");
?>
<td class="stacked"><table><tr><td style="font-weight:bold;"><?php echo $station_name[$station]?></td></tr><tr><td style="font-weight:bold;color:#C00;"><em>Station Offline</em></td></tr></table></td>
<td colspan="17"></td>
</tr>
<?php   } else { echo("<tr class=\"rowlt\">"); ?>
<td class="stacked"><table><tr><td style="font-weight:bold;"><?php echo $station_name[$station]?></td></tr><tr><td style="font-style:italic;color:#B0B;"><?php echo $cd[$station]['TS']?></td></tr></table></td>
<td style="font-weight:bold;" <?php echo $cd[$station]['AirT']=='N/A'?'class="na"':'class="temp"'?>><?php echo $cd[$station]['AirT']?></td>
<td <?php echo $cd[$station][$fh[$felt]]=='N/A'?'class="na"':'class="' . $felt . '"'?>><?php echo $cd[$station][$fh[$felt]]?></td>
<td <?php echo $cd[$station]['DewPt']=='N/A'?'class="na"':'class="hum"'?>><?php echo $cd[$station]['DewPt']?></td>
<td <?php echo $cd[$station]['RH']=='N/A'?'class="na"':'class="hum"'?>><?php echo $cd[$station]['RH']?></td>
<td <?php echo $cd[$station]['WndDir']=='N/A'?'class="na"':'class="wind"'?>><?php echo $cd[$station]['WndDir']?></td>
<td <?php echo $cd[$station]['WndSpd']=='N/A'?'class="na"':'class="wind"'?>><?php echo $cd[$station]['WndSpd']?></td>
<td <?php echo $cd[$station]['SL_Pressure']=='N/A'?'class="na"':'class="pres"'?>><?php echo $cd[$station]['SL_Pressure']?></td>
<td <?php echo $cd[$station]['Precip']=='N/A'?'class="na"':'class="rain"'?>><?php echo $cd[$station]['Precip']?></td>
<td <?php echo $te[$station]['WndSpd_Max']=='N/A'?'class="na"':'class="wind" title="WndSpd_Max &#64; ' . $te[$station]['WndSpd_TMx'] . '"'?>>
    <?php echo $te[$station]['WndSpd_Max']?>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $te[$station]['AirT_Max']=='N/A'?'class="na"':'class="hi" title="AirT_Max &#64; ' . $te[$station]['AirT_TMx'] . '"'?>>
          <?php echo $te[$station]['AirT_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $te[$station]['AirT_Min']=='N/A'?'class="na"':'class="lo" title="AirT_Min &#64; ' . $te[$station]['AirT_TMn'] . '"'?>>
          <?php echo $te[$station]['AirT_Min']?>
      </td>
    </tr>
  </table>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $te[$station]['DewPt_Max']=='N/A'?'class="na"':'class="hi" title="DewPt_Max &#64; ' . $te[$station]['DewPt_TMx'] . '"'?>>
          <?php echo $te[$station]['DewPt_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $te[$station]['DewPt_Min']=='N/A'?'class="na"':'class="lo" title="DewPt_Min &#64; ' . $te[$station]['DewPt_TMn'] . '"'?>>
          <?php echo $te[$station]['DewPt_Min']?>
      </td>
    </tr>
  </table>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $te[$station]['RH_Max']=='N/A'?'class="na"':'class="hi" title="RH_Max &#64; ' . $te[$station]['RH_TMx'] . '"'?>>
          <?php echo $te[$station]['RH_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $te[$station]['RH_Min']=='N/A'?'class="na"':'class="lo" title="RH_Min &#64; ' . $te[$station]['RH_TMn'] . '"'?>>
          <?php echo $te[$station]['RH_Min']?>
      </td>
    </tr>
  </table>
</td>
<td <?php echo $ye[$station]['Precip']=='N/A'?'class="na"':'class="rain"'?>><?php echo $ye[$station]['Precip']?></td>
<td <?php echo $ye[$station]['WndSpd_Max']=='N/A'?'class="na"':'class="wind" title="WndSpd_Max &#64; ' . $ye[$station]['WndSpd_TMx'] . '"'?>>
    <?php echo $ye[$station]['WndSpd_Max']?>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $ye[$station]['AirT_Max']=='N/A'?'class="na"':'class="hi" title="AirT_Max &#64; ' . $ye[$station]['AirT_TMx'] . '"'?>>
          <?php echo $ye[$station]['AirT_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $ye[$station]['AirT_Min']=='N/A'?'class="na"':'class="lo" title="AirT_Min &#64; ' . $ye[$station]['AirT_TMn'] . '"'?>>
          <?php echo $ye[$station]['AirT_Min']?>
      </td>
    </tr>
  </table>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $ye[$station]['DewPt_Max']=='N/A'?'class="na"':'class="hi" title="DewPt_Max &#64; ' . $ye[$station]['DewPt_TMx'] . '"'?>>
          <?php echo $ye[$station]['DewPt_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $ye[$station]['DewPt_Min']=='N/A'?'class="na"':'class="lo" title="DewPt_Min &#64; ' . $ye[$station]['DewPt_TMn'] . '"'?>>
          <?php echo $ye[$station]['DewPt_Min']?>
      </td>
    </tr>
  </table>
</td>
<td class="stacked">
  <table>
    <tr>
      <td <?php echo $ye[$station]['RH_Max']=='N/A'?'class="na"':'class="hi" title="RH_Max &#64; ' . $ye[$station]['RH_TMx'] . '"'?>>
          <?php echo $ye[$station]['RH_Max']?>
      </td>
    </tr>
    <tr>
      <td <?php echo $ye[$station]['RH_Min']=='N/A'?'class="na"':'class="lo" title="RH_Min &#64; ' . $ye[$station]['RH_TMn'] . '"'?>>
          <?php echo $ye[$station]['RH_Min']?>
      </td>
    </tr>
  </table>
</td>
</tr>
<?php   }
      }?>
</tbody>
</table>
</div>
</div>
</div> 
</div> 

<?php include("Common/footer.php"); ?>
