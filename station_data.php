<?php
// 2018-03-12 DBM removed echo of invalid request arguments to mitigate XSS attacks
// 2017-10-18 DBM added input validation for $ts
include("Common/header.php");
include("common/menu.php");
include("db_login.php");
include("station_info.php");
include("station_name.php");
include("inputValidation.php");

$connection = mysql_connect($db_host,$db_username,$db_password);
if (!$connection) {
   die("Could not connect to the database: " . mysql_error());
}
$db_select = mysql_select_db($db_database);
if (!$db_select) {
   die("Could not select the database: " . mysql_error());
}

if (!empty($_GET['station'])) {
   $station = $_GET['station'];
   $table = $station . "_202";
   if (!empty($station_name[$station])) {
      $location = $station_name[$station];
   } else {
      die("Invalid station was specified - aborting");
   }
}
else {
   die("No station was specified - aborting");
}

if (!empty($_GET['req'])) {
   $req = $_GET['req'];
} else {
   $req = "";
}

if (!empty($_GET['dt'])) {
   $dt = $_GET['dt'];
} else {
   $dt = "";
}

if (!empty($_GET['tm'])) {
   $tm = $_GET['tm'];
} else {
   $tm = "";
}

if (!empty($dt) && !empty($tm)) {
    $ts = $dt . " " . $tm;
    if (!validateDateTime($ts)){
      echo("Invalid date/time ");
      $query = "select * from " . $table . " order by ts desc limit 1";
    }
    if (!empty($req)) {
      if ($req == 'next') {
        $query = "select * from " . $table . " where ts > \"" . $ts . "\" order by ts asc limit 1";
      }
      elseif ($req == 'prev') {
        $query = "select * from " . $table . " where ts < \"" . $ts . "\" order by ts desc limit 1";
      }
      else {
        $query = "select * from " . $table . " order by ts desc limit 1";
      }
    } else {
      $query = "select * from " . $table . " where ts = \"" . $ts . "\"";
    }
}
else {
   $query = "select * from " . $table . " order by ts desc limit 1";
}

$result = mysql_query($query);
if (!$result) {
    die("Could not query the database: " . mysql_error());
}

if (mysql_num_rows($result) == 0) {
    if (!empty($req)) {
        if ($req == 'next') {
            $errmsg = "There is no later data available for the " . $location . " station";
        }
        elseif ($req == 'prev') {
            $errmsg = "There is no earlier data available for the " . $location . " station";
        }
        $query = "select * from " . $table . " where ts = \"" . $ts . "\"";
        $result = mysql_query($query);
        if (!$result) {
            die("Could not query the database: " . mysql_error());
        }
    }
    else {
        $errmsg = "There is no data available for the " . $location . " station at " . $dt . " " . $tm . ", Displaying current data";
        $query = "select * from " . $table . " order by ts desc limit 1";
        $result = mysql_query($query);
        if (!$result) {
            die("Could not query the database: " . mysql_error());
        }
        if (mysql_num_rows($result) == 0) {
        	$errmsg = "There is no data available for the " . $location . " station";
        }
    }
}

$ninefifths = 9/5;
$mm2inches = 0.0393700787;
$mb2inHg =  0.029529983071;
$wpsqm2lymin = 0.00143197;
$mps2Mph = 2.2369363;
while ($result_row = mysql_fetch_assoc($result)){
    $Timestamp = explode(" ", $result_row['TS']);
    $dt               = $Timestamp[0];
    $tm               = $Timestamp[1];
    $Door             = $result_row['Door'];
    $Batt             = $result_row['Batt'];
    $Precip_TB3_Tot   = $result_row['Precip_TB3_Tot'];
    $Precip_TX_Tot    = $result_row['Precip_TX_Tot'];
    $Precip_TB3_Today = $result_row['Precip_TB3_Today'];
    $Precip_TX_Today  = $result_row['Precip_TX_Today'];
    $SoilSfcT         = $result_row['SoilSfcT'];
    $SoilT_5cm        = $result_row['SoilT_5cm'];
    $SoilT_10cm       = $result_row['SoilT_10cm'];
    $SoilT_20cm       = $result_row['SoilT_20cm'];
    $SoilT_50cm       = $result_row['SoilT_50cm'];
    $SoilT_100cm      = $result_row['SoilT_100cm'];
    $AirT_1pt5m       = $result_row['AirT_1pt5m'];
    $AirT_2m          = $result_row['AirT_2m'];
    $AirT_9pt5m       = $result_row['AirT_9pt5m'];
    $AirT_10m         = $result_row['AirT_10m'];
    $RH_2m            = $result_row['RH_2m'];
    $RH_10m           = $result_row['RH_10m'];
    $Pressure_1       = $result_row['Pressure_1'];
    $Pressure_2       = $result_row['Pressure_2'];
    $TotalRadn        = $result_row['TotalRadn'];
    $QuantRadn        = $result_row['QuantRadn'];
    $WndDir_2m        = $result_row['WndDir_2m'];
    $WndDir_10m       = $result_row['WndDir_10m'];
    $WndSpd_2m        = $result_row['WndSpd_2m'];
    $WndSpd_10m       = $result_row['WndSpd_10m'];
    $WndSpd_Vert      = $result_row['WndSpd_Vert'];
    $WndSpd_2m_Max    = $result_row['WndSpd_2m_Max'];
    $WndSpd_10m_Max   = $result_row['WndSpd_10m_Max'];
    $SoilType         = $result_row['SoilType'];
    $Temp_C           = $result_row['Temp_C'];
    $wfv              = $result_row['wfv'];
    $SoilCond_tc      = $result_row['SoilCond_tc'];
    $SoilWaCond_tc    = $result_row['SoilWaCond_tc'];
}
mysql_free_result($result);

// Apply rounding, calculate English equivalent measurements, and set NULL values to "N/A"
if (!is_null($Precip_TB3_Tot)) {
   $Precip_TB3_Tot_en = round($Precip_TB3_Tot * $mm2inches,3);
   $Precip_TB3_Tot    = round($Precip_TB3_Tot,3);
} else {
   $Precip_TB3_Tot = "N/A";
   $Precip_TB3_Tot_en = "N/A";
}
if (!is_null($Precip_TX_Tot)) {
   $Precip_TX_Tot_en = round($Precip_TX_Tot * $mm2inches,3);
   $Precip_TX_Tot    = round($Precip_TX_Tot,3);
} else {
   $Precip_TX_Tot = "N/A";
   $Precip_TX_Tot_en = "N/A";
}
if (!is_null($Precip_TB3_Today)) {
   $Precip_TB3_Today_en = round($Precip_TB3_Today * $mm2inches,3);
   $Precip_TB3_Today    = round($Precip_TB3_Today,3);
} else {
   $Precip_TB3_Today = "N/A";
   $Precip_TB3_Today_en = "N/A";
}
if (!is_null($Precip_TX_Today)) {
   $Precip_TX_Today_en = round($Precip_TX_Today * $mm2inches,3);
   $Precip_TX_Today    = round($Precip_TX_Today,3);
} else {
   $Precip_TX_Today = "N/A";
   $Precip_TX_Today_en = "N/A";
}
if (!is_null($SoilSfcT)) {
   $SoilSfcT_en = round($ninefifths * $SoilSfcT + 32,2);
   $SoilSfcT    = round($SoilSfcT,2);
} else {
   $SoilSfcT = "N/A";
   $SoilSfcT_en = "N/A";
}
if (!is_null($SoilT_5cm)) {
   $SoilT_5cm_en = round($ninefifths * $SoilT_5cm + 32,2);
   $SoilT_5cm    = round($SoilT_5cm,2);
} else {
   $SoilT_5cm = "N/A";
   $SoilT_5cm_en = "N/A";
}
if (!is_null($SoilT_10cm)) {
   $SoilT_10cm_en = round($ninefifths * $SoilT_10cm + 32,2);
   $SoilT_10cm    = round($SoilT_10cm,2);
} else {
   $SoilT_10cm = "N/A";
   $SoilT_10cm_en = "N/A";
}
if (!is_null($SoilT_20cm)) {
   $SoilT_20cm_en = round($ninefifths * $SoilT_20cm + 32,2);
   $SoilT_20cm    = round($SoilT_20cm,2);
} else {
   $SoilT_20cm = "N/A";
   $SoilT_20cm_en = "N/A";
}
if (!is_null($SoilT_50cm)) {
   $SoilT_50cm_en = round($ninefifths * $SoilT_50cm + 32,2);
   $SoilT_50cm    = round($SoilT_50cm,2);
} else {
   $SoilT_50cm = "N/A";
   $SoilT_50cm_en = "N/A";
}
if (!is_null($SoilT_100cm)) {
   $SoilT_100cm_en = round($ninefifths * $SoilT_100cm + 32,2);
   $SoilT_100cm    = round($SoilT_100cm,2);
} else {
   $SoilT_100cm = "N/A";
   $SoilT_100cm_en = "N/A";
}
if (!is_null($Temp_C)) {
   $Temp_C_en = round($ninefifths * $Temp_C + 32,2);
   $Temp_C    = round($Temp_C,2);
} else {
   $Temp_C = "N/A";
   $Temp_C_en = "N/A";
}
if (!is_null($AirT_1pt5m)) {
   $AirT_1pt5m_en = round($ninefifths * $AirT_1pt5m + 32,2);
   $AirT_1pt5m    = round($AirT_1pt5m,2);
} else {
   $AirT_1pt5m = "N/A";
   $AirT_1pt5m_en = "N/A";
}
if (!is_null($AirT_2m)) {
   $AirT_2m_en = round($ninefifths * $AirT_2m + 32,2);
   $AirT_2m    = round($AirT_2m,2);
} else {
   $AirT_2m = "N/A";
   $AirT_2m_en = "N/A";
}
if (!is_null($AirT_9pt5m)) {
   $AirT_9pt5m_en = round($ninefifths * $AirT_9pt5m + 32,2);
   $AirT_9pt5m    = round($AirT_9pt5m,2);
} else {
   $AirT_9pt5m = "N/A";
   $AirT_9pt5m_en = "N/A";
}
if (!is_null($AirT_10m)) {
   $AirT_10m_en = round($ninefifths * $AirT_10m + 32,2);
   $AirT_10m    = round($AirT_10m,2);
} else {
   $AirT_10m = "N/A";
   $AirT_10m_en = "N/A";
}
if (is_null($RH_2m)) {
   $RH_2m = "N/A";
} else {
   $RH_2m = round($RH_2m,2);
}
if (is_null($RH_10m)) {
   $RH_10m = "N/A";
} else {
   $RH_10m = round($RH_10m,2);
}
if (!is_null($Pressure_1)) {
   $Pressure_1_en = round($Pressure_1 * $mb2inHg,2);
   $Pressure_1    = round($Pressure_1,2);
} else {
   $Pressure_1 = "N/A";
   $Pressure_1_en = "N/A";
}
if (!is_null($Pressure_2)) {
   $Pressure_2_en = round($Pressure_2 * $mb2inHg,2);
   $Pressure_2    = round($Pressure_2,2);
} else {
   $Pressure_2 = "N/A";
   $Pressure_2_en = "N/A";
}
if (!is_null($TotalRadn)) {
   $TotalRadn_en = round($TotalRadn * $wpsqm2lymin,5);
   $TotalRadn    = round($TotalRadn,5);
} else {
   $TotalRadn = "N/A";
   $TotalRadn_en = "N/A";
}
if (is_null($QuantRadn)) {
   $QuantRadn = "N/A";
} else {
   $QuantRadn = round($QuantRadn,2);
}
if (is_null($WndDir_2m)) {
   $WndDir_2m = "N/A";
} else {
   $WndDir_2m = round($WndDir_2m,1);
}
if (is_null($WndDir_10m)) {
   $WndDir_10m = "N/A";
} else {
   $WndDir_10m = round($WndDir_10m,1);
}
if (!is_null($WndSpd_2m)) {
   $WndSpd_2m_en = round($WndSpd_2m * $mps2Mph,2);
   $WndSpd_2m    = round($WndSpd_2m,2);
} else {
   $WndSpd_2m = "N/A";
   $WndSpd_2m_en = "N/A";
}
if (!is_null($WndSpd_10m)) {
   $WndSpd_10m_en = round($WndSpd_10m * $mps2Mph,2);
   $WndSpd_10m    = round($WndSpd_10m,2);
} else {
   $WndSpd_10m = "N/A";
   $WndSpd_10m_en = "N/A";
}
if (!is_null($WndSpd_Vert)) {
   $WndSpd_Vert_en = round($WndSpd_Vert * $mps2Mph,2);
   $WndSpd_Vert    = round($WndSpd_Vert,2);
} else {
   $WndSpd_Vert = "N/A";
   $WndSpd_Vert_en = "N/A";
}
if (!is_null($WndSpd_2m_Max)) {
   $WndSpd_2m_Max_en = round($WndSpd_2m_Max * $mps2Mph,2);
   $WndSpd_2m_Max    = round($WndSpd_2m_Max,2);
} else {
   $WndSpd_2m_Max = "N/A";
   $WndSpd_2m_Max_en = "N/A";
}
if (!is_null($WndSpd_10m_Max)) {
   $WndSpd_10m_Max_en = round($WndSpd_10m_Max * $mps2Mph,2);
   $WndSpd_10m_Max    = round($WndSpd_10m_Max,2);
} else {
   $WndSpd_10m_Max = "N/A";
   $WndSpd_10m_Max_en = "N/A";
}
if (is_null($Batt)) {
   $Batt = "N/A";
} else {
   $Batt = round($Batt,2);
}
if (is_null($wfv)) {
   $wfv = "N/A";
} else {
   $wfv = round($wfv * 100,2);
}
if (is_null($SoilCond_tc)) {
   $SoilCond_tc = "N/A";
} else {
   $SoilCond_tc = round($SoilCond_tc,2);
}
if (is_null($SoilWaCond_tc)) {
   $SoilWaCond_tc = "N/A";
} else {
   $SoilWaCond_tc = round($SoilWaCond_tc,2);
}
if (is_null($SoilType)) {
   $SoilType = "N/A";
} elseif ($SoilType == 1) {
   $SoilType = "Sand";
} elseif ($SoilType == 2) {
   $SoilType = "Silt";
} elseif ($SoilType == 3) {
   $SoilType = "Clay";
} else {
   $SoilType = "N/A";
}
if (is_null($Door)) {
   $Door = "N/A";
} elseif ($Door == 1) {
   $Door = "Open";
} else {
   $Door = "Closed";
}


?>
<?php #include("Common/menu.php"); ?>
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1">
<div class="panel" style="background-color:rgba(230, 230, 230, 0.85);">
<div class="panel-heading">
<?php
echo "
<h2 class=\"center\" style=\"font-weight:bold;\">Meteorological Data for $location Station</h2>
";
$Lat = sprintf('%.4f',round(abs($station_info[$station]['Lat']),4));
$ns = latdir($station_info[$station]['Lat']);
$Lon = sprintf('%.4f',round(abs($station_info[$station]['Lon']),4));
$ew = londir($station_info[$station]['Lon']);
$Elev = sprintf('%.2f',round($station_info[$station]['Elev'],2));
$commiss = " Commissioned: " . $station_info[$station]['Begin'];
if (isset($station_info[$station]['End'])) {
    $commiss .= ", Decommissioned: " . $station_info[$station]['End'];
}
echo "<h5 class=\"center\" style='color:darkgreen;font-weight:bold;'>Latitude: {$Lat}&deg;{$ns}, Longitude: {$Lon}&deg;{$ew}, Elevation: {$Elev}m, {$commiss} </h5>";
if (isset($Timestamp)) {
    echo "<h4 class=\"center\" style='color:purple;font-weight:bold;'>Measurements recorded at: " . $dt . " " . $tm . " CST</h4>";
}
if ($station == 'mobileusaw' ) {
  echo "<div class='sponsor'>The USA Mesonet project would like to thank the SGA College of Nursing Senators for sponsoring the USA Campus West station</div>";
}
if (isset($errmsg)) {
    echo "<h4><em>$errmsg</em></h4>";
}
echo "
<div class=\"center\"><a class='btn btn-default' href=station_metadata.php?station=$station>Display Metadata for $location Station</a></div><br />
<form name='input' action='station_data.php' method='get'>
Time: <input type='text' name='tm' size='6' id='timepicker' value=\"$tm\">
Date: <input type='text' name='dt' size='8' id='datepicker' value=\"$dt\">
Station: <select name='station'>
";
foreach ($station_name as $key => $value) {
   if ($station == $key ) {
      echo "<option selected='selected' value=\"$key\">$value</option>";
   } else {
      echo "<option value=\"$key\">$value</option>";
   }
}
echo "
</select>
<input type='submit' value='Go' />
</form>
<div class='btn-group btn-group-sm' role='group'>
<a class='btn btn-default' href='station_data.php?station=$station&req=prev&dt=$dt&tm=$tm'><span class='glyphicon glyphicon-backward' aria-hidden='true'></span></a>
<a class='btn btn-default' href='station_data.php?station=$station'><span class='glyphicon glyphicon-time'></span></a>
<a class='btn btn-default' href='station_data.php?station=$station&req=next&dt=$dt&tm=$tm'><span class='glyphicon glyphicon-forward' aria-hidden='true'></span></a>
</div>
</div>
";

echo "
<div class=\"panel-body\" style=\"background-color:rgba(255, 255, 255, 1.0);\">
<table class='table table-hover table-bordered'>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=Precip_TB3_Tot&dt=$dt><img border='0' title='Graph Precip_TB3_Tot' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Precipitation over the last minute (TB3):</td><td>$Precip_TB3_Tot</td><td>mm</td><td>$Precip_TB3_Tot_en</td><td>in</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=Precip_TX_Tot&dt=$dt><img border='0' title='Graph Precip_TX_Tot' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Precipitation over the last minute (TX):</td><td>$Precip_TX_Tot</td><td>mm</td><td>$Precip_TX_Tot_en</td><td>in</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=Precip_TB3_Today&dt=$dt><img border='0' title='Graph Precip_TB3_Today' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Precipitation since midnight (TB3):</td><td>$Precip_TB3_Today</td><td>mm</td><td>$Precip_TB3_Today_en</td><td>in</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=Precip_TX_Today&dt=$dt><img border='0' title='Graph Precip_TX_Today' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Precipitation since midnight (TX):</td><td>$Precip_TX_Today</td><td>mm</td><td>$Precip_TX_Today_en</td><td>in</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=SoilSfcT&dt=$dt><img border='0' title='Graph SoilSfcT' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Surface Temperature:</td><td>$SoilSfcT</td><td>&#176;C</td><td>$SoilSfcT_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=SoilT_5cm&dt=$dt><img border='0' title='Graph SoilT_5cm' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 5cm:</td><td>$SoilT_5cm</td><td>&#176;C</td><td>$SoilT_5cm_en</td><td>&#176;F</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=SoilT_10cm&dt=$dt><img border='0' title='Graph SoilT_10cm' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 10cm:</td><td>$SoilT_10cm</td><td>&#176;C</td><td>$SoilT_10cm_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=SoilT_20cm&dt=$dt><img border='0' title='Graph SoilT_20cm' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 20cm:</td><td>$SoilT_20cm</td><td>&#176;C</td><td>$SoilT_20cm_en</td><td>&#176;F</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=SoilT_50cm&dt=$dt><img border='0' title='Graph SoilT_50cm' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 50cm:</td><td>$SoilT_50cm</td><td>&#176;C</td><td>$SoilT_50cm_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=SoilT_100cm&dt=$dt><img border='0' title='Graph SoilT_100cm' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 100cm:</td><td>$SoilT_100cm</td><td>&#176;C</td><td>$SoilT_100cm_en</td><td>&#176;F</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=Temp_C&dt=$dt><img border='0' title='Graph Temp_C' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Temperature 100cm [Hydraprobe]:</td><td>$Temp_C</td><td>&#176;C</td><td>$Temp_C_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=wfv&dt=$dt><img border='0' title='Graph wfv' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Water Content:</td><td>$wfv</td><td>&#37;</td><td></td><td></td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=SoilCond_tc&dt=$dt><img border='0' title='Graph SoilCond_tc' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Conductivity (Temperature Corrected):</td><td>$SoilCond_tc</td><td>siemens/m</td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=SoilWaCond_tc&dt=$dt><img border='0' title='Graph SoilWaCond_tc' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Soil Water Conductivity (Temperature Corrected):</td><td>$SoilWaCond_tc</td><td>siemens/m</td><td></td><td></td></tr>
<tr class='rowdk'><td></td><td>Soil Type:</td><td>$SoilType</td><td></td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=AirT_1pt5m&dt=$dt><img border='0' title='Graph AirT_1pt5m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Temperature (1.5m):</td><td>$AirT_1pt5m</td><td>&#176;C</td><td>$AirT_1pt5m_en</td><td>&#176;F</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=AirT_2m&dt=$dt><img border='0' title='Graph AirT_2m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Temperature (2m):</td><td>$AirT_2m</td><td>&#176;C</td><td>$AirT_2m_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=AirT_9pt5m&dt=$dt><img border='0' title='Graph AirT_9pt5m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Temperature (9.5m):</td><td>$AirT_9pt5m</td><td>&#176;C</td><td>$AirT_9pt5m_en</td><td>&#176;F</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=AirT_10m&dt=$dt><img border='0' title='Graph AirT_10m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Temperature (10m):</td><td>$AirT_10m</td><td>&#176;C</td><td>$AirT_10m_en</td><td>&#176;F</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=RH_2m&dt=$dt><img border='0' title='Graph RH_2m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Relative Humidity (2m):</td><td>$RH_2m</td><td>&#37;</td><td></td><td></td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=RH_10m&dt=$dt><img border='0' title='Graph RH_10m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Relative Humidity (10m):</td><td>$RH_10m</td><td>&#37;</td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=Pressure_1&dt=$dt><img border='0' title='Graph Pressure_1' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Pressure (1):</td><td>$Pressure_1</td><td>hPa</td><td>$Pressure_1_en</td><td>inHg</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=Pressure_2&dt=$dt><img border='0' title='Graph Pressure_2' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Air Pressure (2):</td><td>$Pressure_2</td><td>hPa</td><td>$Pressure_2_en</td><td>inHg</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=TotalRadn&dt=$dt><img border='0' title='Graph TotalRadn' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Total Radiation:</td><td>$TotalRadn</td><td>W/m&#178;</td><td>$TotalRadn_en</td><td>ly/min</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=QuantRadn&dt=$dt><img border='0' title='Graph QuantRadn' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Quantum Radiation:</td><td>$QuantRadn</td><td>&#181;E/m&#178;/s</td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=WndDir_2m&dt=$dt><img border='0' title='Graph WndDir_2m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Direction (2m):</td><td>$WndDir_2m</td><td>&#176;</td><td></td><td></td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=WndDir_10m&dt=$dt><img border='0' title='Graph WndDir_10m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Direction (10m):</td><td>$WndDir_10m</td><td>&#176;</td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=WndSpd_2m&dt=$dt><img border='0' title='Graph WndSpd_2m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Speed (2m):</td><td>$WndSpd_2m</td><td>m/s</td><td>$WndSpd_2m_en</td><td>Mph</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=WndSpd_10m&dt=$dt><img border='0' title='Graph WndSpd_10m' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Speed (10m):</td><td>$WndSpd_10m</td><td>m/s</td><td>$WndSpd_10m_en</td><td>Mph</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=WndSpd_Vert&dt=$dt><img border='0' title='Graph WndSpd_Vert' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Vertical Wind Speed:</td><td>$WndSpd_Vert</td><td>m/s</td><td>$WndSpd_Vert_en</td><td>Mph</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=WndSpd_2m_Max&dt=$dt><img border='0' title='Graph WndSpd_2m_Max' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Speed Maximum (2m):</td><td>$WndSpd_2m_Max</td><td>m/s</td><td>$WndSpd_2m_Max_en</td><td>Mph</td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=WndSpd_10m_Max&dt=$dt><img border='0' title='Graph WndSpd_10m_Max' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Wind Speed Maximum (10m):</td><td>$WndSpd_10m_Max</td><td>m/s</td><td>$WndSpd_10m_Max_en</td><td>Mph</td></tr>
<tr class='rowdk'><td><a href=station_graph.php?station=$station&var=Batt&dt=$dt><img border='0' title='Graph Battery Voltage' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Battery Voltage:</td><td>$Batt</td><td>Volts</td><td></td><td></td></tr>
<tr class='rowlt'><td><a href=station_graph.php?station=$station&var=Door&dt=$dt><img border='0' title='Graph Enclosure Door' src='images/chart_symbol.jpg' width='25' height='19' /></a></td><td>Enclosure Door:</td><td>$Door</td><td>State</td><td></td><td></td></tr>
</table>
";
?>
</div>
</div>
</div> 
</div> 

<?php include("Common/footer.php"); ?>
