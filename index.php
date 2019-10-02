<?php include("Common/header.php"); ?>
<?php include("Common/menu.php"); ?>
  
<body style="padding-bottom: 100px">
    <div class="row">
	<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
		<div class="panel panel-default">
			<div class="panel-body" style="background-color:#bbddff;">
				<img class="img-responsive" src="images/SAMesonetLongLogo-transparent.png" />
			</div>		
		</div>
        </div>
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0">
		<div class="panel panel-default">
			<div class="panel-body" style="background-color:lavender;">
				<h4 class='center'>Like our site?  Like our data?</h4>
				<h4 class='center'>Please consider making a <a href="donate.php" style="font-weight:bold;">contribution</a>!</h4>
				<hr>
				<div class='center'>
					<a href="http://www.facebook.com/pages/South-Alabama-Mesonet/140015933698"><img src="images/facebook.jpg" height=60 /></a> &nbsp;
					<a href="donate.php"><img src="images/donate.png" height=60 /></a>
					<a href="http://www.southalabama.edu"><img src="images/USA-logo.gif" height=60 /></a>
				</div>
			</div>		
		</div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
            <div class="panel panel-default" style="background-color: rgba(255, 255, 255, 0.4);">
			<div class="panel-heading" style="background-color: rgba(255, 255, 255, 0.4);">
				<h3 style="text-align: center;font-weight: bold;color:navy;">Select one of our stations below</h3>
			</div>
			<div class="panel-body" style="background-color: rgba(220, 240, 255, 0.7);">
				<img class="center-block img-responsive" src="images/station_map_new.png" alt="Station Map"  border="0" usemap="#StationMap" />
				<!-- <img src="images/station_map_new.png" height="360" width="800"  alt="Station Map"  border="0" usemap="#StationMap" /> -->
				<!-- <map name="StationMap" id="stationmap"> -->
				<map name="StationMap">
                                        <area shape="circle" coords="79,216,10" href="#" alt="Agricola CHILI Station" title="Agricola Station" onclick="drawStation('agricola', 'Agricola')" />
                                        <area shape="circle" coords="217,199,10" href="#" alt="Bay Minette CHILI Station" title="Bay Minette Station" onclick="drawStation('bayminette', 'Bay Minette')" />
                                        <area shape="circle" coords="159,273,10" href="#" alt="Mobile Dog River CHILI Station" title="Mobile Dog River Station" onclick="drawStation('mobiledr', 'Mobile(Dog River)')" />
                                        <area shape="circle" coords="141,244,8" href="#" alt="USA West CHILI Station" title="USA West Station" onclick="drawStation('mobileusaw', 'USA Campus West')" />                              
                                        
                                        <!--<area shape="circle" coords="79,216,10" href="station_data.php?station=agricola" alt="Agricola CHILI Station" title="Agricola Station" />
                                        <area shape="circle" coords="217,199,10" href="station_data.php?station=bayminette" alt="Bay Minette CHILI Station" title="Bay Minette Station" />
                                        <area shape="circle" coords="159,273,10" href="station_data.php?station=mobiledr" alt="Mobile Dog River CHILI Station" title="Mobile Dog River Station" />                                        
                                        <area shape="circle" coords="141,244,8" href="station_data.php?station=mobileusaw" alt="USA West CHILI Station" title="USA West Station" />-->
                                        <area shape="circle" coords="148,238,8" href="station_data.php?station=mobileusa" alt="USA Campus CHILI Station" title="USA Campus Station" />                                        
					<area shape="circle" coords="163,344,10" href="station_data.php?station=disl" alt="DISL CHILI Station" title="DISL Station"/>
					<area shape="circle" coords="257,306,10" href="station_data.php?station=elberta" alt="Elberta CHILI Station" title="Elberta Station" />
					<area shape="circle" coords="229,268,6" href="station_data.php?station=robertsdale" alt="Robertsdale Station" title="Robertsdale Station" />
					<area shape="circle" coords="79,317,10" href="station_data.php?station=pascagoula" alt="Pascagoula CHILI Station" title="Pascagoula Station" />					
					<area shape="circle" coords="178,156,10" href="station_data.php?station=mtvernon" alt="Mt. Vernon CHILI Station" title="Mt. Vernon Station" />
					<area shape="circle" coords="284,172,10" href="station_data.php?station=atmore" alt="Atmore CHILI Station" title="Atmore Station" />					
					<area shape="circle" coords="63,136,10" href="station_data.php?station=leakesville" alt="Leakesville CHILI Station" title="Leakesville Station" />
					<area shape="circle" coords="108,285,10" href="station_data.php?station=grandbay" alt="Grand Bay CHILI Station" title="Grand Bay Station" />					
					<area shape="circle" coords="166,213,10" href="station_data.php?station=saraland" alt="Saraland CHILI Station" title="Saraland Station" />
					<area shape="circle" coords="205,344,10" href="station_data.php?station=gasque" alt="Gasque CHILI Station" title="Gasque Station" />
					<area shape="circle" coords="229,255,6" href="station_data.php?station=loxley" alt="Loxley CHILI Station" title="Loxley Station" />
					<area shape="circle" coords="245,315,10" href="station_data.php?station=foley" alt="Foley CHILI Station" title="Foley CHILI Station" />
					<area shape="circle" coords="200,277,10" href="station_data.php?station=fairhope" alt="Fairhope CHILI Station" title="Fairhope Station" />
					<area shape="circle" coords="278,199,10" href="station_data.php?station=walnuthill" alt="Walnut Hill CHILI Station" title="Walnut Hill Station" />
					<area shape="circle" coords="336,187,10" href="station_data.php?station=jay" alt="Jay CHILI Station" title="Jay Station" />
					<area shape="circle" coords="363,109,10" href="station_data.php?station=castleberry" alt="Castleberry CHILI Station" title="Castleberry Station" />
					<area shape="circle" coords="425,139,10" href="station_data.php?station=dixie" alt="Dixie CHILI Station" title="Dixie Station" />
					<area shape="circle" coords="463,112,10" href="station_data.php?station=andalusia" alt="Andalusia CHILI Station" title="Andalusia Station" />
					<area shape="circle" coords="495,175,10" href="station_data.php?station=florala" alt="Florala CHILI Station" title="Florala Station" />
					<area shape="circle" coords="527,126,10" href="station_data.php?station=kinston" alt="Kinston CHILI Station" title="Kinston Station" />
					<area shape="circle" coords="593,162,10" href="station_data.php?station=geneva" alt="Geneva CHILI Station" title="Geneva Station" />
					<area shape="circle" coords="700,132,10" href="station_data.php?station=ashford_n" alt="Ashford North CHILI Station" title="Ashford North Station" />
					<area shape="circle" coords="265,155,10" href="station_data.php?station=poarch" alt="Poarch Creek CHILI Station" title="Poarch Creek Station" />
				</map>
                                
                        </div>
            </div> 
        </div>
        
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-0 col-lg-4 col-lg-offset-0">
            <div class="panel panel-default">
		<div class="panel-heading" style="background-color:#008bca;color:white;">
                    <h2 class="panel-title" style="text-align:center;font-weight:bold;">Live Mesonet Observations</h2>
                    <h2 class="panel-title" style="text-align:center;font-size:small;">Choose a Station Below</h2>
		</div>							 
                <div id="canvasDiv" class="panel-body" style="background-color:#FFF">
                    <canvas id="myCanvas" width="600" height="400"></canvas>
                        <div>
                            <div class="col-xs-6 text-left">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Station <span class="caret"></span></button>
					<ul id="stationMenu" class="dropdown-menu" role="menu">
					</ul>
                            </div>
                            <div class="col-xs-6 text-right">
                                <a id="widgetFullDataLink" class="btn btn-default" href="station_data.php" onclick="goToFullData('station', currentStation)" >Full Data</a>                                
                            </div>
                        </div>
                            
                </div>                   
            </div>
        </div>
    </div>
		

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	<!--<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 col-lg-offset-2">-->
                <div class="panel panel-default">
			<div class="panel-heading" style="background-color:#007196;color:white;">
					<h2 class="panel-title" style="text-align:center;font-weight:bold;">Regional Observations</h2>
			</div>							 
			<div class="panel-body" style="background-color:#FFF">
				<img id="regionalImage" class="img-responsive" src="http://weather.southalabama.edu/images/surface/GulfCoast_CHILI_Temp.png" alt="Gulf Coast Observations"/>
				<div class="btn-group">
			   	<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Data Set <span class="caret"></span></button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#" onClick="changeRegional(0)">Temperature</a></li>
						<li><a href="#" onClick="changeRegional(1)">Dewpoint</a></li>
						<li><a href="#" onClick="changeRegional(2)">Pressure</a></li>
					</ul>
				</div>   
			</div>
		</div>
		<!--<div class="panel panel-info">
			<div class="panel-heading">
				<h2 class="panel-title" style="text-align:center;font-weight:bold;"><a href="mesonet_info.php">USA Mesonet History</a></h2>
			</div>
			<div class="panel-body" style="background-color:#EEE">
				<p style="font-size: small;">The South Alabama Mesonet is a network of <a href="station_map.php">26 automated weather stations</a>, located in the north-central Gulf Coast area and was founded in 2004 by University of South Alabama Meteorology professor Dr. Sytske Kimball. The weather stations collect 16 meteorological- and soil- quantities including temperature, rainfall, wind speed and direction, soil temperature and humidity. Measurements are made automatically every minute of the day, every day of the year. The Mesonet is entirely funded through grants and donations to support routine maintenance (removing vegetation and animal nests), sensor replacement/repair, calibration of instruments, and to pay our student workers.</p>
				<p style="font-size: small;">Mesonet weather station data have inspired a wide variety of research projects at the University of South Alabama addressing local weather events and phenomena including cold fronts, landfall of tropical storms, sea breezes, nocturnal cooling, and local wind and rainfall climatologies. The weather stations and their data are used in the Meteorology curriculum at the University of South Alabama. Meteorology majors can take a class in Meteorological Instrumentation, get involved in weather station maintenance, social media outreach, and participate in research projects using Mesonet data. The Mobile National Weather Service forecast office uses the data on a frequent basis to issue severe weather and flash flood warnings and to perform post weather event analyses. Thirteen stations are located on local public school campuses and are used for K-12 outreach activities. The Mesonet serves the wider Gulf Coast community in a wide range of applications including agriculture, ecosystem monitoring, fire weather, recreation, renewable energy, air quality and health, emergency management, litigation, and climate change.</p>
                        </div>
                </div>-->
	</div>
    </div>

    
    <?php //include("Common/footer.php"); ?>
    <!--[if lt IE 7]><script type="text/javascript" src="iehoverfix.js"></script><![endif]-->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.mousewheel.min.js" type="text/javascript"></script>
    <script src="js/jquery.timeentry.min.js" type="text/javascript"></script>
    <script src="js/drawCurrentConditionsWidget.js" type="text/javascript"></script>
    <script src="js/chili.js" type="text/javascript"></script>
    <script src="js/changeRegionalImage.js" type="text/javascript"></script>
    
  </body>
  
</html>
