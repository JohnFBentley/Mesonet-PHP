function changeRegional(map) {
    if (map === 0){
        document.getElementById("regionalImage").src = "http://weather.southalabama.edu/images/surface/GulfCoast_CHILI_Temp.png";            
    } else if (map === 1){
        document.getElementById("regionalImage").src = "http://weather.southalabama.edu/images/surface/GulfCoast_CHILI_Dewpoint.png";
    } else if (map === 2){
        document.getElementById("regionalImage").src = "http://weather.southalabama.edu/images/surface/GulfCoast_CHILI_Pressure.png"; 
    }
            
}


