<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
<head>
    <title>Fermi All-Sky Variability Analysis (FAVA)</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

	<link rel="icon" type="image/png" href="./img/favicon2.png">

    <!-- jQuery -->
    <script type="text/javascript" src="./js/jquery-1.12.0.min.js"></script>

    <!-- Bootstrap core js -->
    <script src="./js/bootstrap.min.js"></script>

    <!-- Reset css -->
    <link rel="stylesheet" hrmouseef="./css/reset.css" type="text/css" />

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap theme -->
    <link href="./css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- NASA theme -->
    <link rel="stylesheet" href="./css/NASA.css">


    <!-- Highcharts -->
	<script src="./js/Highcharts/highcharts.js"></script>
	<script src="./js/Highcharts/highcharts-more.js"></script>
	<script src="./js/Highcharts/modules/exporting.js"></script>

    <!-- Highcharts -->
    <script type="text/javascript" src="./js/kartograph.js"></script>
    <script type="text/javascript" src="./js/table2CSV.js" > </script> 

</head>

<!-- custom css -->
<style type="text/css">

    /* The selection tool tip */
    #tip {
        border: solid;
        border-radius: 5px;
        background-color: rgba(255,255,255,.90);
        /*background-color: white;   */
        /*opacity: 0.8;*/
        border: 2px solid rgba(0, 0, 0, .9);
        position: absolute;
        height: 90px;
        width: auto;    
        top: 100px;
        box-shadow: 0 1px 2px rgba(0,0,0,.4), 0 1px 0 rgba(255,255,255,.5) inset;
        text-align: left;
        color: #000;
        padding: 10px 10px 0px 10px;
        visibility: hidden;
        /*font-weight: bold;*/
        font-size: 14px;
        line-height: 125%;
    }

    /* The red selection circle */
    #Selection {
        border: solid;
        border-radius: 15px;
        border-width: 2px;
        border-color: red;
        opacity: 0.75;
        position: absolute;
        height: 15px;
        width: 15px;    
        top: 100px;
        visibility: hidden;
    }


	.TimebinData td { 
		height: 100px 
	};

	/* Removing the hover coloring from bootstrap tables */
    .table tbody tr:hover td,
    .table tbody tr:hover th {
      background-color: transparent;
    }

	#proj { 
		background:url(./img/FAVAFlareSummary_NoFlares.png);
		background-repeat: no-repeat;
		background-position: 55% 55%; 
		background-size: 98% 99%;
	}

	#footer { 
		float:left;
		padding:25px 0 10px 10px;
		width:99%}
	}


</style>


<body id="body-plain">

	<script type="text/javascript" src="./js/dat.gui.js"></script>
	<script type="text/javascript">

        var GlobeOpt = function() {
            this.lon0 = 0;
            this.lat0 = 0;
            this.lat1 = 0;
            this.lat2 = 0;
            this.dist = 10;
            this.up = 0;
            this.tilt = 0;
            this.proj = 'aitoff';
            // this.proj = 'mercator';
            this.projstr = '+proj=lcc +lat_1=0 +lat_0=0 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +a=6378249.2 +b=6356515 +towgs84=-168,-60,320,0,0,0,0 +pm=paris +units=m',
            //this.flip = 0;
            this.offsetx = 0;
            this.offsety = 0;
            this.startx = 0;
            this.starty = 0;
            this.deltalon = 0;
            this.deltalat = 0;
            this.isdragged = false;
            this.firstclick = true;
        };

        var P;
        frame = 0;

        var globeopt = new GlobeOpt();
        var url = location.href.split('#');
        if (url.length>1) globeopt.proj = url[1];


        var points_CanvasProjection = new Array();
        var userPoint_CanvasProjection = new Array();
        var mouseCoords;

        var paths = new Array();
        var Record = 0;
        var TerminalOutput = new String;
        var disableLabels = 0;
        var disableGalacticPlane = 0;
        var userData = new Array();
        //userData = [];

        var previousRowElement;
        var sourceName;
        var sourceRA;
        var sourceDec;
        var sourceGlon;
        var sourceGlat;

        var lines = 100;
        var offset = 0;

        var numberOfSources;
        var classSelectURLParameters = '';
        var keywordSearchURLParameters = '';

        var numberOfTableRows;

        var width = 1000;
        var height = 500;


		function createCanvas(id,w,h) {
		    if (document.getElementById(id) != null) {
		        var ctx = document.getElementById(id).getContext("2d");
		        // ctx.clearRect(0,0,w,h+20);
				ctx.clearRect(0,0,w,h);

		        return ctx;
		    }
		    var canvas = document.createElement("canvas");
		    canvas.setAttribute("id", id);
		    canvas.setAttribute("width", w+"px");
		    canvas.setAttribute("height", h+"px");
		    canvas.setAttribute("margin", "auto");

		    $('#map-parent').append(canvas);
		    var ctx = canvas.getContext("2d");
		    return ctx;
		};

		function showMap(p, paths, points, mouseCoords, userData) {
		    P = new kartograph.proj[p](globeopt);
		    xy = P.project(13,14);

		    if (isNaN(xy[0]) || isNaN(xy[1])) {
		        console.error(p, P, xy);
		        return;
		    };

		    var
		    lon, lat, i,
		    //w = $('#map-parent').width(),
		    // w = 800,
		    // h = 400,
		    w = width,
		    h = height,

		    grat = 15,
		    sea = P.sea(),
		    bbox = P.world_bbox(),
		    view,
		    // ctx = createCanvas("proj", w,h+20),
		    ctx = createCanvas("proj", w,h),
		    len = ctx.measureText(p.toUpperCase()).width;

		    console.log(w);
		    console.log(h);

		    ctx.beginPath();
		    ctx.lineWidth = 1.0;

		    // Create a transparent background
		    // ctx.fillStyle ="#fff";
		    ctx.fillStyle = "rgba(255, 255, 255, 0.0)";

		    view = new kartograph.View(bbox, w, h, 10);
		    for (i=0;i<sea.length;i++) {
		        xy = view.project(sea[i]);
		        if (i==0) ctx.moveTo(xy[0], xy[1]);
		        else ctx.lineTo(xy[0], xy[1]);
		    }
		    ctx.stroke();
		    ctx.fill();
		    ctx.closePath();

		   // graticule
		    ctx.beginPath();
		    ctx.lineWidth = 0.2;
		    ctx.strokeStyle = "rgba(255, 255, 255, 1.0)";
		    for (lat=0;lat<90;lat+=grat) {
		        var lats = lat == 0 ? [0] : [lat,-lat];
		        for (var l in lats) {
		            var lat_ = lats[l];
		            var line = [];
		            for (lon=-180;lon<180;lon++) {
		                line.push([lon,lat_]);
		            }
		            for (var i=0;i<line.length-1;i++) {
		                p0 = line[i];
		                p1 = line[i+1];
		                d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		                if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 30) {
		                    p0 = view.project(P.project(p0[0],p0[1]));
		                    p1 = view.project(P.project(p1[0],p1[1]));
		                    ctx.moveTo(p0[0],p0[1]);
		                    ctx.lineTo(p1[0],p1[1]);
		                }
		            }
		        }
		    }
		    ctx.stroke();
		    ctx.fill();
		    ctx.closePath();


		   // graticule
		    ctx.beginPath();
		    ctx.lineWidth = 0.2;
		    for (lat=0;lat<90;lat+=grat) {
		        var lats = lat == 0 ? [0] : [lat,-lat];
		        for (var l in lats) {
		            var lat_ = lats[l];
		            var line = [];
		            for (lon=-180;lon<180;lon++) {
		                line.push([lon,lat_]);
		            }
		            for (var i=0;i<line.length-1;i++) {
		                p0 = line[i];
		                p1 = line[i+1];
		                d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		                if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 30) {
		                    p0 = view.project(P.project(p0[0],p0[1]));
		                    p1 = view.project(P.project(p1[0],p1[1]));
		                    ctx.moveTo(p0[0],p0[1]);
		                    ctx.lineTo(p1[0],p1[1]);
		                }
		            }
		        }
		    }
		    ctx.stroke();
		    ctx.fill();
		    ctx.closePath();

		    // graticule
		    ctx.beginPath();
		    ctx.lineWidth = 0.2;
		    for (lon=0;lon<181;lon+=grat) {
		        var lons = lon == 0 || lon == 180 ? [lon] : [lon,-lon];
		        $.each(lons, function(l, lon_) {
		            var line = [];
		            for (lat=-90+(lon % 90 == 0 ? 0 : grat);lat<90-(lon%90 == 0 ? 0 : grat)+1;lat+=0.25) {
		                line.push([lon_,lat]);
		            }
		            for (var i=0;i<line.length-1;i++) {
		                p0 = line[i];
		                p1 = line[i+1];
		                d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		                if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 100) {
		                    p0 = view.project(P.project(p0[0],p0[1]));
		                    p1 = view.project(P.project(p1[0],p1[1]));
		                    ctx.moveTo(p0[0],p0[1]);
		                    ctx.lineTo(p1[0],p1[1]);

		                }
		            }
		        });
		    }
		    ctx.stroke();
		    ctx.fill();
		    ctx.closePath();


		    // Paths
		    ctx.lineWidth = 1;
		    ctx.beginPath();
		    var cl, line, p0, p1, d;
		    for (cl=0; cl<paths.length; cl++) {
		        line = paths[cl];
		        for (i=0; i<line.length-1; i++) {
		            p0 = line[i];
		            p1 = line[i+1];
		            d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		            if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 100) {
		                p0 = view.project(P.project(p0[0],p0[1]));
		                p1 = view.project(P.project(p1[0],p1[1]));
		                ctx.moveTo(p0[0],p0[1]);
		                ctx.setLineDash([0]);
		                ctx.lineTo(p1[0],p1[1]);
		            }
		        }
		    }
		    ctx.stroke();
		    ctx.fill()
		    ctx.closePath();


		    // Celestial Coordinates 
		    // ctx.lineWidth = 1;
		    // var i, point, p0;
		    // points_CanvasProjection = new Array();
		    // for (i=0; i<points.length; i++) {
		    //     point = points[i];
		    //     point_CanvasProjection = view.project(P.project(parseFloat(point.RAJ2000), parseFloat(point.DEJ2000)));
		    //     points_CanvasProjection.push(point_CanvasProjection);
		    //     if (P._visible(parseFloat(point.RAJ2000),parseFloat(point.DEJ2000))) {
		    //         ctx.beginPath()
		    //         ctx.fillStyle = "rgba(0,0,0," + parseFloat(point.Size) * 0.115 + ")";
		    //         //ctx.strokeStyle = "rgba(0,0,0,0.0)";
		    //         ctx.arc(point_CanvasProjection[0], point_CanvasProjection[1], parseFloat(point.Size)/3, 0, 2 * Math.PI, false);
		    //         //ctx.stroke();
		    //         ctx.fill()
		    //         ctx.closePath();
		    //     }
		    // }
		    // ctx.closePath();

		    // Galactic Coordinates
		    ctx.lineWidth = 1;
		    var i, point, p0;
		    points_CanvasProjection = new Array();
		    for (i=0; i<points.length; i++) {
		        point = points[i];
		        point_CanvasProjection = view.project(P.project(360.-parseFloat(point.GLON), parseFloat(point.GLAT)));
		        points_CanvasProjection.push(point_CanvasProjection);
		        if (P._visible(360.-parseFloat(point.GLON), parseFloat(point.GLAT))) {
		            ctx.beginPath()
		            if (point.ASSOC1 === 'none') {
		            	// ctx.fillStyle = "rgba(0,103,0," + parseFloat(point.Size) * 0.115 + ")";
		            	ctx.fillStyle = "rgba(0,179,0, 1.0)";

						// ctx.lineWidth = '0.25';
						// ctx.strokeStyle = "rgba(0, 103, 0, 1.0)"
						
		            } else {
		            	// ctx.fillStyle = "rgba(0, 66, 255," + parseFloat(point.Size) * 0.115 + ")";
						ctx.fillStyle = "rgba(154, 180, 255, 1.0)";

						// ctx.lineWidth = '0.25';
						// ctx.strokeStyle = "rgba(34, 88, 188, 1.0)"
		            }
		            ctx.arc(point_CanvasProjection[0], point_CanvasProjection[1], parseFloat(point.Size)/3, 0, 2 * Math.PI, false);
		            ctx.fill()

		            // Create a black outline
		            ctx.lineWidth = '0.25';
					ctx.strokeStyle = 'black';
					ctx.stroke();

					// Close the path
					ctx.closePath();
		        }
		    }
		    ctx.closePath();

		    // Convert the user supplied coordinates into the canvas projection
		    // if (typeof sourceRA !== 'undefined' && typeof sourceDec !== 'undefined') {
		    //     if (P._visible(parseFloat(sourceRA),parseFloat(sourceDec))) {
		    //         userPoint_CanvasProjection = view.project(P.project(parseFloat(sourceRA), parseFloat(sourceDec)));
		    //     }
		    // }

		    if (typeof sourceGlon !== 'undefined' && typeof sourceGlat !== 'undefined') {
		        if (P._visible(parseFloat(sourceGlon),parseFloat(sourceGlat))) {
		            userPoint_CanvasProjection = view.project(P.project(360.-parseFloat(sourceGlon), parseFloat(sourceGlat)));
		        }
		    }


		    // User Submitted Data
		    if (userData.length > 0) {
		        ctx.lineWidth = 1;
		        ctx.beginPath();
		        var cl, line, p0, p1, d;
		        for (cl=0; cl<userData.length; cl++) {
		            line = userData[cl];
		            for (i=0; i<line.length-1; i++) {
		                p0 = line[i];
		                p1 = line[i+1];
		                d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		                if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 100) {
		                    p0 = view.project(P.project(p0[0],p0[1]));
		                    p1 = view.project(P.project(p1[0],p1[1]));
		                    ctx.moveTo(p0[0],p0[1]);
		                    ctx.setLineDash([0]);
		                    ctx.lineTo(p1[0],p1[1]);
		                }
		            }
		        }
		        ctx.stroke();
		        ctx.fill()
		        ctx.closePath();
		    }
		   
		    // Galactic Plane
		    // if (disableGalacticPlane == 0) {
		    //     ctx.beginPath();
		    //     ctx.lineWidth = 0.2;
		    //     var cl, line, p0, p1, d;
		    //     for (cl=0; cl<galacticPlane.length; cl++) {
		    //         line = galacticPlane[cl];
		    //         for (i=0; i<line.length-1; i++) {
		    //             p0 = line[i];
		    //             p1 = line[i+1];
		    //             d = P.clon ? Math.abs(P.clon(p0[0])-P.clon(p1[0])) : 0;
		    //             if (P._visible(p0[0],p0[1]) && P._visible(p1[0],p1[1]) && d < 100) {
		    //                 p0 = view.project(P.project(p0[0],p0[1]));
		    //                 p1 = view.project(P.project(p1[0],p1[1]));
		    //                 ctx.moveTo(p0[0],p0[1]);
		    //                 ctx.lineTo(p1[0],p1[1]);
		    //             }
		    //         }
		    //     }  
		    //     ctx.stroke();
		    //     ctx.fill();
		    //     ctx.closePath();
		    //  }


		    // ctx.fillStyle = "rgba(0,100,0,0.5)"; //blue
		    // ctx.beginPath();
		    // ctx.arc(45,45,15,0,Math.PI*2,true);
		    // ctx.fill();                        
		    // ctx.closePath();

		    // Points
		    // ctx.fillStyle = "green";
		    // p0 = view.project(P.project(358.539,46.0915));   
		    // if (P._visible(358,46.0915)) {
		    //     ctx.beginPath()
		    //     ctx.arc(p0[0], p0[1], 10, 0, 2 * Math.PI, false);
		    //     ctx.fill()
		    //     ctx.stroke();
		    // }

		    return points_CanvasProjection, userPoint_CanvasProjection;
		};

		function renderFrame() {

		    points_CanvasProjection, userPoint_CanvasProjection = showMap(globeopt.proj, paths, points, mouseCoords, userData);

		    return points_CanvasProjection, userPoint_CanvasProjection;
		};

	    function ExportMap() {

	        var canvas = document.getElementById("proj");
	        var img    = canvas.toDataURL("image/png");
	        // document.write('<img src="'+img+'" target=\"_blank\""/>');

	        var generator = window.open('', 'png');
	        generator.document.write('<img src="'+img+'"/>');
	        generator.document.close();
	    }

	    function Resize() {

	        width=1000;
	        height=600;

	        htmlCanvas = document.getElementById('proj');
	        context = htmlCanvas.getContext('2d');
	        htmlCanvas.width = width;
	        htmlCanvas.height = height;

	        renderFrame();
	    }
	    	    	    
	    function GetUrlValue(VarSearch){
	        var SearchString = window.location.search.substring(1);
	        var VariableArray = SearchString.split('&');
	        for(var i = 0; i < VariableArray.length; i++){
	            var KeyValuePair = VariableArray[i].split('=');
	            if(KeyValuePair[0] == VarSearch){
	                return KeyValuePair[1];
	            }
	        }
	    }

		function submit() {

			if ( (document.getElementById('raInput').value.length != 0) && (document.getElementById('decInput').value.length != 0) ) {
				var ra = document.getElementById('raInput').value
				var dec = document.getElementById('decInput').value
				var url = "./LightCurve.php?ra=" + ra + "&dec=" + dec
				window.open(url, '_self');
			}
		}

		// Call the database
		function queryDB_2FAV(typeOfRequest) {

			console.log('Querying the database...')

			if (typeOfRequest === 'TimebinData') {

				// Set up the variables
				var data;
		        var weekNumber = [];
		        var startMET = [];
		        var stopMET = [];
		        var startDate = [];
		        var stopDate = [];

				// Setup the URL
				var typeOfRequest = 'TimebinData'
				var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

				var URL = "queryDB_2FAV.php?typeOfRequest=" + typeOfRequest_urlEncoded;

				console.log(URL);
				$.ajax({url: URL, success: function(responseText){

					data = JSON.parse(responseText);

				// $.each(data, function(i, datum) {

						// weekNumber.push(datum.weekNumber);
						// startMET.push(datum.startMET);
						// stopMET.push(datum.stopMET);
						// startDate.push(datum.startDate)
						// stopDate.push(datum.stopDate);

				// }

					console.log('Timebin data recieved.')
					console.log(data.length + ' weeks')

					// Update the fava analysis overview table
					document.getElementById('table_weekNumber').innerHTML = data.length.toString();


					// Fill the primary table
					fillTable(data);

				}});

			}

			if (typeOfRequest === 'FlareList') {

				// Setup the URL
				var typeOfRequest = 'FlareList'
				var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

				var URL = "queryDB_2FAV.php?typeOfRequest=" + typeOfRequest_urlEncoded;

				console.log(URL);
				$.ajax({url: URL, success: function(responseText){

					data = JSON.parse(responseText);

					console.log('flarelist data recieved.')
					console.log(data.length + ' flares')


					var points = [];
					var point = {};
					var numberOfAssocaitedSources = 0
					var numberOfUnassocaitedSources = 0


					// Loop through each data entry and extract the values
					for (var i=0, size=data.length; i<size; i++) {

						// Get the current source
					    sourceRecord = data[i];

					    // Save the neccessary source properties
		  			  //   var radius;
					    // var sigma = parseFloat(sourceRecord['sigma'])
					    // if (sigma < 0) {
					    // 	radius = 6
					    // } else {
					    // 	radius = sigma * 1.5;
					    // }

					    var radius = 8;

					    // Determine the number of associated and unassociated sources
					    if (sourceRecord['fglassoc'] === 'none') {
					    	numberOfUnassocaitedSources = numberOfUnassocaitedSources + 1
					    } else {
							numberOfAssocaitedSources = numberOfAssocaitedSources + 1
					    }

					    point = {Source_Name: sourceRecord['flareID'], ASSOC1: sourceRecord['fglassoc'], RAJ2000: sourceRecord['best_ra'], DEJ2000: sourceRecord['best_dec'], GLON: sourceRecord['gall'], GLAT: sourceRecord['galb'], Size: radius }

					    // Add the point to the points array
					    points.push(point);

					}

			        // Get the map data
			        // console.log('Calling queryDB(MapData)')
			        // points = queryDB('MapData');

			        // Load the map data
			        window.points = points;

			        // Update the GUI
			        window.gui = new dat.GUI({ 
			            autoPlace: false,
			            width: 330,
			            hideable: false,
			            resizable: false
			        });


			        $('.k-gui').append(gui.domElement);

			        gui.remember(globeopt);
			        var proj = [];
			        $.each(kartograph.proj, function(p) {
			            proj.push(p);
			        })
			        proj = proj.sort();
			        window.projopts = {
			            lon0: [-180,180, 1],
			            lat0: [-90, 90],
			            lat1: [-90, 90],
			            lat2: [-90, 90],
			            dist: [1.01, 10, 0.01],
			            up: [-180,180],
			            tilt: [-40,0],
			            projstr: 'str'
			        };


			        var updateGUI = function() {
			            // reset gui
			            try {
			                for (var i=gui.__controllers.length-1; i>=0; i--) {
			                    gui.remove(gui.__controllers[i]);
			                }
			            } catch (e) {}
			            gui.add(globeopt, 'proj', proj).onChange(function() {
			                updateGUI();
			                renderFrame();
			            });
			            $.each(projopts, function(key, val) {
			                if (kartograph.proj[globeopt.proj].parameters.indexOf(key) >= 0) {
			                    var s = val == 'str' ? gui.add(globeopt, key) : gui.add(globeopt, key, val[0], val[1]);
			                    if (val != 'str' && val.length == 3) s.step(val[2]);
			                    s.onChange(renderFrame);
			                }
			            });
			            $('#k-proj-title').html(kartograph.proj[globeopt.proj].title);
			            var url = location.href.split('#');
			            location.href = url[0]+'#'+globeopt.proj;
			        };


			        // Update the FAVA analysis overview table
					document.getElementById('table_favaDetections').innerHTML = data.length.toString();
					document.getElementById('table_associatedDetections').innerHTML = numberOfAssocaitedSources.toString();
					document.getElementById('table_unassociatedDetections').innerHTML = numberOfUnassocaitedSources.toString();

					var c = document.getElementById("AssociatedCanvas");
					var ctx = c.getContext("2d");
					ctx.beginPath();
					ctx.arc(31,10,3,0,2*Math.PI);
					ctx.fillStyle = "rgba(154, 180, 255, 1.0)";
					ctx.fill()
					ctx.lineWidth = '0.25';
					ctx.strokeStyle = 'black';
					ctx.stroke();
					ctx.closePath();


					var c = document.getElementById("unassociatedCanvas");
					var ctx = c.getContext("2d");
					ctx.beginPath();
					ctx.arc(15,10,3,0,2*Math.PI);
					ctx.fillStyle = "rgba(0,179,0, 1.0)"
					ctx.fill()
		            ctx.lineWidth = '0.25';
					ctx.strokeStyle = 'black';

					
					ctx.stroke();
					ctx.closePath();


			        // updateGUI();

			        // Draw the map
			        renderFrame();


				}});

			}

		}




		function fillTable(data) {

			// Setup the row array
			var row = new Array(), j = -1;

			// Create the header string
			var header = '<tr> \
			<th style="text-align: center;">Week Number</th> \
			<th style="text-align: center;">Start MET</th> \
			<th style="text-align: center;">Stop MET</th> \
			<th style="text-align: center;">Start Date</th> \
			<th style="text-align: center;">Stop Date</th> \
			</tr>'

			// Loop through each data entry and add columns to the corresponding row entry
			for (var i=0, size=data.length; i<size; i++) {

			    sourceRecord = data[i];
			    row[++j] = '<tr>';

			    for (var key in sourceRecord) {

					row[++j] ='<td style="text-align: center;">';

					if (key === 'week') {
						row[++j] = '<a href="./Sources.php?week=' + sourceRecord[key] + '">' + sourceRecord[key] + '</a>';
					} else {
			        	row[++j] = sourceRecord[key];
					}

			        row[++j] = '</td>';
			    }

			}

			// Add the header to the start of the array
			row.unshift(header);

			// Join the row array into one long string and place it inside the table element
			$('#dataTable').html(row.join('')); 


		}

		// function populateMap(data) {

		// 	console.log('Populating Map');

		// 	var points = [];
		// 	var point = {};

		// 	console.log(data.length);

		// 	// Loop through each data entry and extract the values
		// 	for (var i=0, size=data.length; i<size; i++) {

		// 		// Get the current source
		// 	    sourceRecord = data[i];

		// 	    // Save the neccessary source properties




		$(function() {
	        	        	    
			queryDB_2FAV('TimebinData');
			queryDB_2FAV('FlareList');

	        $('#map-parent').click(function (e) {

               // Hide any visible tool tips
                var tip = document.getElementById('tip');
                tip.style.visibility = "hidden";
                var Selection = document.getElementById('Selection');
                Selection.style.visibility = "hidden";   

	        });


            $('#map-parent').dblclick(function (e) {

                var canvas = document.getElementById("proj");
                offsetLet = canvas.offsetLeft;
                offsetTop = canvas.offsetTop;

                globeopt.startx = e.pageX - offsetLet;
                globeopt.starty = e.pageY - offsetTop;

                // Object IDs
                var mouseXPosition = e.pageX - offsetLet;
                var mouseYPosition = e.pageY - offsetTop;

                mouseCoords = [mouseXPosition,mouseYPosition];
                
                // Get all the current points on the map
                points_CanvasProjection, userPoint_CanvasProjection = renderFrame();

                var d, i, point;
                d = 100
                var distances = new Array();
               
                for (i=0; i<points_CanvasProjection.length; i++) {

                    point_CanvasProjection = points_CanvasProjection[i];
                    point = points[i]
                    d = Math.sqrt( Math.pow(mouseXPosition - point_CanvasProjection[0], 2) + Math.pow(mouseYPosition - point_CanvasProjection[1], 2) );

                    if (d < 5) {

                        var tip = document.getElementById('tip');
                        tip.style.visibility = "visible";
                        tip.style.left = (e.pageX - 95) + "px";
                        tip.style.top = (e.pageY - 105) + "px";

                        var week = point.Source_Name.split("_")[0];
                        var flare = point.Source_Name.split("_")[1];

                        // var lightcurveLink = "<a href=\"./LightCurve.php?ra=" + point.RAJ2000 + "&dec=" + point.DEJ2000 + "\">FAVA Lightcurve</a>";
                        var sourceReportLink = "<a href=\"./SourceReport.php?week=" + week + "&flare=" + flare + "\">FAVA Source Report</a>";
                        document.getElementById("tip").innerHTML =   '2FAV_'  + point.Source_Name + '<BR>RA: ' + point.RAJ2000 + ', Dec: ' + point.DEJ2000 + '<BR>Association: ' + point.ASSOC1 + '<BR>' + sourceReportLink

                        var Selection = document.getElementById('Selection');
                        Selection.style.visibility = "visible";
                        Selection.style.left = (point_CanvasProjection[0] + offsetLet - 7) + "px";
                        Selection.style.top = (point_CanvasProjection[1] + offsetTop - 8) + "px";
                        
                        break;

                    } else {
                        // Do nothing for now
                    }

                }
            });

			$('#map-parent').mouseover(function() {
				$('#map-tip').show();
			})

			$('#map-parent').mouseout(function() {
				$('#map-tip').hide();
			})


		});

	</script>

	<!-- main starts here -->		
	<div id="main" style="width:1650px">	

	    <!-- Start NASA Container -->
	    <div id="nasa-container" style="margin:8px 0 0 10px">

	        <!-- Start NASA Banner -->
	        <div id="nasa-banner-plain">

	            <!-- Left - Logo -->
	            <div class="nasa-logo">
	                <a href="http://www.nasa.gov/"><img src="http://fermi.gsfc.nasa.gov/ssc/inc/img/nasa_logo.gif" width="140" height="98" border="0" alt="NASA Logo"></a>
	            </div>
	        
	            <!-- Middle - Affiliations -->
	            <div id="nasa-affiliation">
	                <h1><a href="http://www.nasa.gov/">National Aeronautics and Space Administration</a></h1>
	                <h2><a href="http://www.nasa.gov/goddard">Goddard Space Flight Center</a></h2>
	            </div>
	            
	            <!-- Right - Search and Links -->
	            <div id="nasa-search-links">
	                <div id="header-links">
	                    <a href="/ssc/">FSSC</a> &bull; <a href="http://heasarc.gsfc.nasa.gov/">HEASARC</a> &bull; <a href="http://science.gsfc.nasa.gov/">Sciences and Exploration</a>
	                </div>
	            </div>

	        </div>
	        <!-- End NASA Banner -->

	        <!-- Start Mission Banner Graphic -->
<!-- 	        <div id="mission-banner-plain">
	            <a href="/ssc/"><img src="http://fermi.gsfc.nasa.gov/ssc/inc/img/fssc_banner.jpg" width="952" height="100" alt="Fermi Science Support Center" /></a>
	        </div> -->
	        <!-- End Mission Banner Graphic -->

	    <!-- End NASA Container -->
	    </div>

		<!-- Header starts here -->
		<div>
			<div style="float: left; padding-top:12px; padding-left:25px"><img middle; style="width: 100%; height: 100%" src="./img/Fermi_Small.png"></div>
			<div style="margin-left: 25px;padding-left: 75px; padding-bottom:20px; padding-top: 5px">
				<H2>Fermi All-sky Variability Analysis (FAVA)</H2>
			</div>
		</div>
		<!-- Header ends here -->

		<!-- sidebar start here -->
	    <div style="width:300px; margin-left:25px; float:left;" id="coordinateInput">

			<!-- Light Curve Generator start here -->		
			<div class="panel panel-default">
				<div class="panel-heading">
			        <h3 class="panel-title">Light Curve Generator</h3>
			     </div>
			     <div class="panel-body">

					<div style="width: 50px; float: left; margin-left:10px; line-height: 2.4;">
						RA:
						<BR>
						Dec:
					</div>

					<div style="margin-left: 75px;">
						<input id="raInput" type="text" class="input-small" placeholder="17.761" id="inputKey" style="margin-bottom: 10px;">
						<BR>
						<input id="decInput" type="text" class="input-small" placeholder="-29.008" id="inputKey" style="margin-bottom: 10px;">
					</div>

					<center>
						<div style="width: 150px; margin-left:125px;" id="submitButtonDiv">
					    	<button form="submitForm" type="button" onclick="submit()" name="submitButton" id="submitButton" subvalue="True" class="btn btn-primary">Submit</button>
					    </div>
					    <div id="ajaxSpinner" style="width: 150px; margin-left:125px; display:none">	
					   		
					    </div>

				    </center>

		      	</div>
		    </div>
			<!-- Light Curve Generator ends here -->		

			<!-- FAVA Analysis Overview start here -->		
			<div class="panel panel-default"  style="height: 195px;">
				<div class="panel-heading">
			        <h3 class="panel-title">FAVA Analysis Overview</h3>
			     </div>
			     <!-- <div class="panel-body"> -->

					<center>

			            <table class="table table-striped">
			            <!-- <table class="table"> -->
			              <tbody>					
  								<tr><td>Weeks Analyzed</td><td td id="table_weekNumber"></td></tr>			
  								<tr><td>FAVA Detections (>6&sigma;)</td><td td id="table_favaDetections"></td></tr>			
  								<tr><td>Associated Detections<canvas id="AssociatedCanvas" width="40" height="20"></td><td td id="table_associatedDetections"></td></tr>			
  								<tr><td>Unassociated Detections<canvas id="unassociatedCanvas" width="30" height="20"></canvas></td><td td id="table_unassociatedDetections"></td></tr>			



			              </tbody>
			            </table>  

				    </center>

		      	<!-- </div> -->
		    </div>
			<!-- FAVA Analysis Overview ends here -->	


			<!-- FAVA Resources start here -->		
			<div class="panel panel-default" style="height: 225px;">
				<div class="panel-heading">
			        <h3 class="panel-title">FAVA Resources</h3>
			     </div>

					<center>

			            <table class="table table-striped">
			            <!-- <table class="table"> -->
			              <tbody>					
  								<tr><td><a href="index.php">FAVA Weekly Flare List</a></td><td td id="table_flarelist"></td></tr>			
  								<tr><td><a href="LightCurve.php">FAVA Light Curve Generator</a></td><td td id="table_lightcurve"></td></tr>
								<tr><td><a href="http://adsabs.harvard.edu/abs/2013ApJ...771...57A">1st FAVA Catalog</a></td><td id="table1_1FAV"></td></tr>
								<tr><td><a href="#">2nd FAVA Catalog</a></td><td td id="table1_2FAV"></td></tr>
								<!-- <tr><td><a href="https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&uact=8&ved=0ahUKEwjSw8e3oKzKAhWMPz4KHU0bAMwQFggtMAE&url=http%3A%2F%2Ffermi.gsfc.nasa.gov%2Fscience%2Fmtgs%2Fsymposia%2F2015%2Fprogram%2Fwednesday%2Fsession11B%2FDKocevski.pdf&usg=AFQjCNGB3SKJx9skOJCGjHqWykqSw0R7eg&sig2=jKSTnYazQwXoMa2VHCbg5w">About FAVA</a></td><td td id="table_flarelist"></td></tr>			 -->
								<!-- <tr><td><a href="#" data-target="#AnalysisModal" data-toggle="modal">About FAVA</a></td></tr>			 -->
								<tr><td><a href="About.html">About FAVA</a></td><td></td></tr>		

					<!-- <button data-toggle="modal" href="#ConfigureTableModal" type="submit" class="btn btn-primary" style="color:white;font-size: 12px;margin:5px">Configure Table</button> -->



			              </tbody>
			            </table>  

				    </center>

		    </div>
			<!-- FAVA Resources ends here -->		


		    <!-- Caveat statement start here -->	
			<div>	
				<center>
					<div class="alert alert-info" role="alert">All analysis results presented here are preliminary unless otherwise stated.  Please consult the <a href="About.html">about page</a> for important details and caveats associated with this analysis.</div>
			    </center>
		    </div>
			<!-- Caveat statement ends here -->	

			<!-- Citation request start here -->	
			<div>	
				<center>
					<div class="alert alert-success" role="alert">Please reference <a href="http://adsabs.harvard.edu/abs/2013ApJ...771...57A">Ackermann et al. 2013</a> for any use of the presented results</div>
			    </center>
		    </div>
			<!--  Citation request ends here -->	

		</div>
		<!-- sidebar ends here -->

		<!-- Content starts here -->
		<div id="content">

			<!-- FAVA Flare map panel start here -->	
		    <div style="width:1300px; margin-left: 340px;">
		 		<div class="panel panel-default" style="height: 600px;">
					<div class="panel-heading"><h3 class="panel-title">FAVA Flare Map</h3></div>

				     <div class="panel-body">

						<center>

							<div id="tip" style="visibility: hidden"></div>
							<div id="Selection" style="visibility: hidden" width=10 height=10></div>

			                <div id="map-parent" style="margin-top:0px">
			                    <center>
			                    </center>
			                </div>

			                <div id="map-tip" style="display: none; color: #808080;">
			                	Double click a flare for additional information
			                </div>

						</center>

					</div>	

	      		</div>
		    </div>
			<!-- FAVA flare panel ends here -->	



            <!-- Map canvas div starts here -->
		    <div style="width:1300px; margin-left: 340px;">

	            <div style="width:75%; float: left">

	                <!-- Map canvas starts here
	                <div id="map-parent">
	                    <center>
	                    </center>
	                </div>
	                <!-- Map canvas ends here -->

	            </div>
		    </div>

            <!-- Map canvas div ends here -->



			<!-- Weekly analysis panel start here -->	
		    <div style="width:1300px; margin-left: 340px;">
			 	<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Weekly FAVA Runs</h3></div>
				    <div class="panel-body">
				    	<center>
			            <table class="table table-striped table-condensed table-bordered" id="dataTable" style="width:1000px;"></table>  
			            </center>
					</div>	
		      	</div>
		    </div>
			<!-- Weekly analysis panel ends here -->




		</div>
		<!-- Content ends here -->

	<!-- Main ends here -->
	</div>

	<!-- footer starts here -->	
	<div id="footer">
		<div id="footer-content">
		
			<p>
				<hr>
				FAVA Data Portal v2.0	- Support Contact:<a href="mailto:daniel.kocevski@nasa.gov"> Daniel Kocevski</a>
			</p>

		</div>
	</div>
	<!-- footer ends here -->




</body>


