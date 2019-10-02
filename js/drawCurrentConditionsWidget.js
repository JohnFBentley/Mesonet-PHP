
var stationData = JSON.parse("{}");
currentStation = "";

function reqListener() {
	console.log(this.responseText);
}
	
var oReq = new XMLHttpRequest();
oReq.onload = function() {
  stationData = JSON.parse(this.responseText);
};
oReq.open("get", "realTest.php", false);
	
oReq.send();

//var stationData = {
//        agricola : {
//                name : "Agricola",
//                AirT : 69.94,
//                DewPt : 69.94,
//                RH : 100.00,
//                WndDir : 85.49,
//                WindSpd : 0.00,
//                Precip : 0.36,
//                SL_Pressure : 30.10,
//                TS : "2019-09-19 21:43:38" 
//        },
//        bayminette : {
//                name : "Bay Minette",
//                AirT : 72.22,
//                DewPt : 70.71,
//                RH : 95.01,
//                WndDir : 89.98,
//                WndSpd : 4.02,
//                Precip : 0.10,
//                SL_Pressure : 30.08,
//                TS : "2019-09-19 21:43:38" 
//        },
//        mobiledr : {
//                name : "Mobile(Dog River)",
//                AirT : 71.55,
//                DewPt : 71.44,
//                RH: 99.65,
//                WndDir : 69.31,
//                WndSpd : 0.00,
//                Precip : 3.90,
//                SL_Pressure : 30.10,
//                TS : "2019-09-19 21:43:38"
//        },
//        mobileusaw : {
//                name : "USA Campus West",
//                AirT : 70.75,
//                DewPt : 70.75,
//                RH : 100.00,
//                WndDir : 35.73,
//                WndSpd : 1.10,
//                Precip : 4.85,
//                SL_Pressure : 30.10,
//                TS : "2019-09-19 21:43:38"
//            }
//        };

function drawStation(stationAbbrev, stationName) {
        currentStation = stationAbbrev;
	var stationTemp = Math.round(Number(stationData[stationAbbrev]["AirT"]));
	var stationDew  = Math.round(Number(stationData[stationAbbrev]["DewPt"]));
	var stationRH   = Math.round(Number(stationData[stationAbbrev]["RH"]));
	var stationWinD = Math.round(Number(stationData[stationAbbrev]["WndDir"]));
	var stationWinS = Math.round(Number(stationData[stationAbbrev]["WndSpd"]));
	var stationPrec = Number(stationData[stationAbbrev]["Precip"]);
	var stationPres = Number(stationData[stationAbbrev]["SL_Pressure"]);
	var stationTS   = stationData[stationAbbrev]["TS"];
	
	var canvas = $("#myCanvas");
	var context = canvas.get(0).getContext('2d');
	canvas.attr("width", canvas.width());
	canvas.attr("height", canvas.height());

	var canvasDIV = $("#canvasDiv");
	divWidth = canvasDIV.width() - 10;
	canvas.attr("width", divWidth);
	canvas.attr("height", divWidth);
		
	context.clearRect(0, 0, canvas.width(), canvas.height());
	context.closePath();

	//==============================================================
	// Set up variables for customization later
	var totHeight = canvas.height();
	var totWidth  = canvas.width();
	
	var tempTop = totHeight * 0.20;
	var tempLeft = totWidth * 0.08;
	var tempBottom = totHeight * 0.70;
	var tempRight = totWidth * 0.13;
	var tempBulb = tempBottom + ((tempRight - tempLeft) * 0.4);
	var lowTemp = -30.0;
	var highTemp = 120.0;

	var humidityBot  = totHeight * 0.95;
	var humidityDesc = totHeight * 0.87;
	var humidityRHX  = totWidth * 0.15;
	var humidityDewX = totWidth * 0.45;

	var precipDesc   = humidityDesc;
	var precipBot    = humidityBot;
	var precipDayX   = totWidth * 0.75;

	var slope = (tempTop - tempBottom) / (highTemp - lowTemp);
	var inter = tempBottom - (slope * lowTemp);

	var windCentX = totWidth * 0.65;
	var windCentY = (tempTop + tempBottom) / 2.0;
	var windLength = totHeight * 0.25;
	var lengthFrac = 5.0;
	var barbLength = windLength / lengthFrac;
	
	var largeFontSize = (totHeight * 0.06).toFixed(0);
	var medFontSize   = (totHeight * 0.04).toFixed(0);
	var smallFontSize = (totHeight * 0.03).toFixed(0);
	
	var thickLine = (totHeight * 0.01).toFixed(0);
	var thinLine  = (thickLine / 4.0).toFixed(0);
	var medLine   = ((Number(thickLine) + Number(thinLine)) / 2.0).toFixed(0);
	
	//==============================================================
	// Make the title of the canvas
	context.beginPath();
	context.font = largeFontSize.toString() + 'pt Calibri';
	context.fillStyle = 'blue';
	context.textAlign = "center";
	context.fillText(stationName, totWidth * 0.50, totHeight * 0.065);
	context.closePath();
	
	context.beginPath();
	context.font = medFontSize.toString() + 'pt Calibri';
	context.fillStyle = 'purple';
	context.textAlign = "center";
	context.fillText("Valid at: " + stationTS + " CST", totWidth * 0.50, totHeight * 0.125);
	context.closePath();

	//==============================================================
	// Plot the base rectangle / bulb around the whole temperature thing
	context.beginPath();
	context.rect(tempLeft, tempTop, tempRight-tempLeft, tempBottom-tempTop);
	context.lineWidth = thinLine;
	context.strokeStyle = "black";
	context.stroke();
	context.closePath();

	context.beginPath();
	context.arc((tempLeft+tempRight)/2.0, tempBulb, (tempBulb-tempBottom) * 2.0, 2*Math.PI, false);
	context.fillStyle = "red";
	context.fill();
	context.lineWidth = thinLine;
	context.strokeStyle = "black";
	context.stroke();
	context.closePath();


	//==============================================================
	// Plot the current temperature
	context.beginPath();
	var currSpot = stationTemp * slope + inter;
	context.rect(tempLeft, currSpot, tempRight-tempLeft, tempBottom-currSpot);
	context.fillStyle = "red";
	context.fill();
	context.closePath();
	context.font = smallFontSize.toString() + 'pt Calibri';
	context.textAlign = "right";
	context.fillStyle = "red";
	context.fillText(stationTemp, tempLeft - (smallFontSize / 2.0), currSpot + (smallFontSize / 2.0));

	//==============================================================
	// Create the temperature scale
	for (var i = lowTemp + 10; i <= highTemp; i += 20) {
		context.beginPath();
		
		var currSpot = slope * i + inter;
		
		context.moveTo(tempLeft, currSpot);
		context.lineTo(tempRight, currSpot);
		context.lineWidth = thinLine;
		context.strokeStyle = "LightGray";
		context.stroke();
		context.closePath();

		context.font = smallFontSize.toString() + 'pt Calibri';
		context.fillStyle = "Gray";
		context.textAlign = "left";
		context.fillText(i, tempRight + (smallFontSize / 2.0), currSpot + (smallFontSize / 2.0));
	}


	//================================================================
	// Output the humidity and rainfall dataspots
	context.font = medFontSize.toString() + 'pt Calibri';
	context.textAlign = "center";
	context.fillStyle = "brown";
	context.fillText("Rel. Hum.", humidityRHX, humidityDesc);
	context.font = largeFontSize.toString() + 'pt Calibri';
	context.fillText(stationRH.toString()+"%", humidityRHX, humidityBot);

	context.font = medFontSize.toString() + 'pt Calibri';
	context.fillStyle = "green";
	context.fillText("Dew Pt.", humidityDewX, humidityDesc);
	context.font = largeFontSize.toString() + 'pt Calibri';
	context.fillText(stationDew.toString(), humidityDewX, humidityBot);
	

	context.font = medFontSize.toString() + 'pt Calibri';
	context.fillStyle = "blue";
	context.fillText("Today's Rain", precipDayX, precipDesc);
	context.font = largeFontSize.toString() + 'pt Calibri';
	context.fillText(stationPrec.toString() + " in.", precipDayX, precipBot);
	
	
	//==============================================================
	// Set up wind rose plot
	context.beginPath();
	context.moveTo(windCentX - windLength, windCentY);
	context.lineTo(windCentX + windLength, windCentY);
	context.lineWidth = thinLine;
	context.strokeStyle = "gray";
	context.stroke();
	context.closePath();

	context.font = smallFontSize.toString() + "pt Calibri";
	context.textAlign = "center";
	context.fillStyle = "gray";
	context.fillText("W", windCentX - windLength - 2 * thickLine, windCentY + 1.5 * thickLine);
	context.fillText("E", windCentX + windLength + 2 * thickLine, windCentY + 1.5 * thickLine);
	context.fillText("N", windCentX, windCentY - windLength - thickLine);
	context.fillText("S", windCentX, windCentY + windLength + 3 * thickLine);

	context.beginPath();
	context.moveTo(windCentX, windCentY - windLength);
	context.lineTo(windCentX, windCentY + windLength);
	context.lineWidth = thinLine;
	context.strokeStyle = "gray";
	context.stroke();
	context.closePath();
	
	context.beginPath();
	var xShift = windLength * Math.sin(stationWinD * Math.PI / 180.0);
	var yShift = -1.0 * windLength * Math.cos(stationWinD * Math.PI / 180.0);
	context.moveTo(windCentX, windCentY);
	context.lineTo(windCentX + xShift, windCentY + yShift);
	context.lineWidth = thickLine;
	context.strokeStyle = "black";
	context.stroke();
	context.closePath();

	context.beginPath();
	context.arc(windCentX, windCentY, (totHeight * 0.02), 0, 2 * Math.PI, false);
	context.fillStyle = "white";
	context.fill();
	context.lineWidth = thickLine;
	context.strokeStyle = "black";
	context.stroke();
	context.closePath();

	var numHalf = Math.round(stationWinS / 5);
	var numFlag = Math.floor(numHalf / 10);
	numHalf = numHalf - 10 * numFlag;
	var numFull = Math.floor(numHalf / 2);
	numHalf = numHalf - numFull * 2;
	
	var fracShift = 1.0;
	var standardFrac = 1.0 / 15.0;

	var xBarb = -1.0 * yShift / lengthFrac;
	var yBarb = xShift / lengthFrac;
	
	if (numFlag > 0) {
		for (i = 0; i < numFlag; i++){
			context.fillStyle = "black";
			context.beginPath();
			context.moveTo(windCentX + xShift * fracShift, windCentY + yShift * fracShift);
			context.lineTo(windCentX + (xShift * (fracShift - 0.5 * standardFrac)) + xBarb, windCentY + (yShift * (fracShift - 0.5 * standardFrac)) + yBarb);
			context.lineTo(windCentX + xShift * (fracShift - standardFrac), windCentY + yShift * (fracShift - standardFrac));
			context.lineWidth = medLine;
			context.strokeStyle = "black";
			context.stroke();					
			context.closePath();
			context.fill();
			fracShift -= standardFrac * 2.0;
		}
	}
	
	if (numFull > 0) {
		for (i = 0; i < numFull; i++) {
			context.beginPath();
			context.moveTo(windCentX + xShift * fracShift, windCentY + yShift * fracShift);
			context.lineTo(windCentX + xShift * fracShift + xBarb, windCentY + yShift * fracShift + yBarb);
			context.lineWidth = medLine;
			context.strokeStyle = "black";
			context.stroke();
			context.closePath();
			fracShift -= standardFrac;
		}
	}
	
	if (numHalf > 0) {
		context.beginPath();
		context.moveTo(windCentX + xShift * fracShift, windCentY + yShift * fracShift);
		context.lineTo(windCentX + xShift * fracShift + (xBarb * 0.5), windCentY + yShift * fracShift + (yBarb * 0.5));
		context.lineWidth = medLine;
		context.strokeStyle = "black";
		context.stroke();
		context.closePath();
		fracShift -= standardFrac;
	}
	
};


function chooseStation(value1, value2) {
	drawStation(value1, value2);
};

function goToFullData(paramName, paramValue)
{
    url = document.getElementById("widgetFullDataLink").href;
    if (paramValue == null) {
        paramValue = '';
    }
    var pattern = new RegExp('\\b('+paramName+'=).*?(&|#|$)');
    if (url.search(pattern)>=0) {
        return url.replace(pattern,'$1' + paramValue + '$2');
    }
    url = url.replace(/[?#]$/,'');
    url = url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
    document.getElementById("widgetFullDataLink").href = url;
};

if ("mobileusaw" in stationData) {
    drawStation("mobileusaw", "USA Campus West");
} else {
    drawStation("mobiledr", "Mobile(Dog River)");
};

var nameList = {"agricola":"Agricola", "andalusia":"Andalusia", "ashford":"Ashford", "atmore":"Atmore", "bayminette":"Bay Minette", "castleberry":"Castleberry", "dixie":"Dixie", "elberta":"Elberta", "fairhope":"Fairhope", "florala":"Florala", "gasque":"Gasque", "geneva":"Geneva", "grandbay":"Grand Bay", "jay":"Jay", "kinston":"Kinston", "leakesville":"Leakesville", "loxley":"Loxley", "mobiledr":"Mobile (Dog River)", "mobileusaw":"USA Campus West", "mtvernon":"Mount Vernon", "pascagoula":"Pascagoula", "poarch":"Poarch Creek", "robertsdale":"Robertsdale", "saraland":"Saraland", "foley":"Foley", "ashford_n":"Ashford North" };

var dropMenuStation = document.getElementById("stationMenu");

for (var name1 in stationData) {
	var newElement = document.createElement("li");
	dropMenuStation.appendChild(newElement);
	var newLink = document.createElement("a");
	newElement.appendChild(newLink);
	newLink.href = "#";
	newLink.value = name1;
	newLink.innerHTML = nameList[name1];
	newLink.addEventListener("click", function(e){ var targ = e.target || e.srcElement; console.log(e); drawStation(targ.value, targ.innerHTML); }, false);
} 


