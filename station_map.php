<?php include("Common/header.php"); ?>
<?php include("Common/menu.php"); ?>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 col-lg-offset-2">
		<div class="panel panel-default" style="background-color: rgba(255, 255, 255, 0.4);">
			<div class="panel-heading" style="background-color: rgba(255, 255, 255, 0.4);">
				<h3 style="text-align: center;font-weight: bold;color:navy;">Select one of our stations below</h3>
			</div>
			<div class="panel-body" style="background-color: rgba(220, 240, 255, 0.7);">
				<img class="center-block" src="images/station_map_new.png" alt="Station Map"  border="0" usemap="#StationMap" />
				<!-- <img src="images/station_map_new.png" height="360" width="800"  alt="Station Map"  border="0" usemap="#StationMap" /> -->
				<!-- <map name="StationMap" id="stationmap"> -->
				<map name="StationMap">
					<area shape="circle" coords="148,238,8" href="station_data.php?station=mobileusa" alt="USA Campus CHILI Station" title="USA Campus Station" />
					<area shape="circle" coords="141,244,8" href="station_data.php?station=mobileusaw" alt="USA West CHILI Station" title="USA West Station" />
					<area shape="circle" coords="163,344,10" href="station_data.php?station=disl" alt="DISL CHILI Station" title="DISL Station"/>
					<area shape="circle" coords="257,306,10" href="station_data.php?station=elberta" alt="Elberta CHILI Station" title="Elberta Station" />
					<area shape="circle" coords="229,268,6" href="station_data.php?station=robertsdale" alt="Robertsdale Station" title="Robertsdale Station" />
					<area shape="circle" coords="79,317,10" href="station_data.php?station=pascagoula" alt="Pascagoula CHILI Station" title="Pascagoula Station" />
					<area shape="circle" coords="79,216,10" href="station_data.php?station=agricola" alt="Agricola CHILI Station" title="Agricola Station" />
					<area shape="circle" coords="178,156,10" href="station_data.php?station=mtvernon" alt="Mt. Vernon CHILI Station" title="Mt. Vernon Station" />
					<area shape="circle" coords="284,172,10" href="station_data.php?station=atmore" alt="Atmore CHILI Station" title="Atmore Station" />
					<area shape="circle" coords="217,199,10" href="station_data.php?station=bayminette" alt="Bay Minette CHILI Station" title="Bay Minette Station" />
					<area shape="circle" coords="63,136,10" href="station_data.php?station=leakesville" alt="Leakesville CHILI Station" title="Leakesville Station" />
					<area shape="circle" coords="108,285,10" href="station_data.php?station=grandbay" alt="Grand Bay CHILI Station" title="Grand Bay Station" />
					<area shape="circle" coords="159,273,10" href="station_data.php?station=mobiledr" alt="Mobile Dog River CHILI Station" title="Mobile Dog River Station" />
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
</div>

<?php include("Common/footer.php"); ?>
