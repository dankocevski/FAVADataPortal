<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
<head>
    <title>Fermi All-Sky Variability Analysis (FAVA)</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- jQuery -->
    <script type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>

    <!-- Bootstrap core js -->
    <script src="./js/bootstrap.min.js"></script>

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
    <script type="text/javascript" src="./js/kartograph.js"></script>

</head>

<!-- custom css -->
<style type="text/css">

	.TimebinData td { 
		height: 100px 
	};

	/* Removing the hover coloring from bootstrap tables */
    .table tbody tr:hover td,
    .table tbody tr:hover th {
      background-color: transparent;
    }

.modal.modal-wide .modal-dialog {
  width: 1050px;
}
.modal-wide .modal-body {
  overflow-y: auto;
}

#proj { 
	background:url(./img/FAVAFlareSummary_NoFlares.png);
	background-repeat: no-repeat;
	background-position: 50% 13%; 
	background-size: 98% 96%;
}

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

#footer { 
	float:left;
	padding:25px 0 10px 10px;
	width:99%}
}


</style>


<body id="body-plain">

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
		        ctx.clearRect(0,0,w,h+20);
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
		    ctx = createCanvas("proj", w,h+20),
		    len = ctx.measureText(p.toUpperCase()).width;

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
		    // ctx.lineWidth = 1;
		    // var i, point, p0;
		    // points_CanvasProjection = new Array();
		    // for (i=0; i<points.length; i++) {
		    //     point = points[i];
		    //     point_CanvasProjection = view.project(P.project(360.-parseFloat(point.GLON), parseFloat(point.GLAT)));
		    //     points_CanvasProjection.push(point_CanvasProjection);
		    //     if (P._visible(360.-parseFloat(point.GLON), parseFloat(point.GLAT))) {
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
		            // ctx.arc(point_CanvasProjection[0], point_CanvasProjection[1], parseFloat(point.Size)/3, 0, 2 * Math.PI, false);
		            ctx.arc(point_CanvasProjection[0], point_CanvasProjection[1], 3 + Math.pow(parseFloat(point.Size)/15, 2), 0, 2 * Math.PI, false);
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


		// Call the database
		function queryDB_2FAV() {

			console.log('Querying the database...')

			// Get the week number
			var weekNumber = document.getElementById('table_weeknumber').innerHTML;
			var weekNumber_urlEncoded = encodeURIComponent(weekNumber);

			// Set the request type
			var typeOfRequest = 'SourceList';
			var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

			// Encode optional url parameters
			var thresholdRequest = document.getElementById('thresholdSelection').value;
			var thresholdRequest_urlEncoded = encodeURIComponent(thresholdRequest);

			// Setup the URL
			var URL = "queryDB_2FAV.php?typeOfRequest=" + typeOfRequest_urlEncoded + "&week=" + weekNumber_urlEncoded + "&threshold=" + thresholdRequest_urlEncoded;

			console.log(URL)

			// Perform an ajax request
			$.ajax({url: URL, success: function(responseText){

				data = JSON.parse(responseText);

				fillTable(data);
				populateMap(data);


			}});			
		}

       function toggleColumn(checkbox) {

            // Determine which checkbox was selected
            var value = checkbox.value;

            if (checkbox.checked == false) {

                // Hide the column
                console.log('Hiding Column ' + value)
                $('#dataTable td:nth-child(' + value + '),#dataTable th:nth-child(' + value + ')').hide();

                // // Hide the erorr column for columns 8, 10, and 12
                // if (value == 8 || value == 10 || value == 13) {

                //     var nextValue = (parseFloat(value) + 1).toString();
                //     console.log('Hiding Column ' + nextValue)
                //     $('#dataTable td:nth-child(' + nextValue + '), #dataTableth:nth-child(' + nextValue + ')').hide();
                // }

            } else {

                // Show the column
                console.log('Showing Column ' + value)
                $('#dataTable td:nth-child(' + value + '),#dataTable th:nth-child(' + value + ')').show();

                // // Show the erorr column for columns 8, 10, and 12
                // if (value == 8 || value == 10 || value == 13) {

                //     var nextValue = (parseFloat(value) + 1).toString();
                //     console.log('Showing Column ' + nextValue)
                //     $('#dataTable td:nth-child(' + nextValue + '),#dataTable th:nth-child(' + nextValue + ')').show();
                // }
            }
        }


        function toggleColumns() {

            // check the states of the table configuration checkboxes
            var checkboxes = $('#TableConfiguration').find(':checkbox')

            for (var i=0, size=checkboxes.length; i<size; i++) {

                checkbox = checkboxes[i]
                var value = checkbox.value;

                if (checkbox.checked == false) {

                    // Hide the column
                    console.log('Hiding Column ' + value)
                    $('#dataTable td:nth-child(' + value + '),#dataTable th:nth-child(' + value + ')').hide();

                    // Hide the erorr column for columns 8, 10, and 12
                    // if (value == 8 || value == 10 || value == 13) {

                    //     var nextValue = (parseFloat(value) + 1).toString();
                    //     console.log('Hiding Column ' + nextValue)
                    //     $('#dataTable td:nth-child(' + nextValue + '),#dataTable th:nth-child(' + nextValue + ')').hide();
                    // }

                } else {

                    // Show the column
                    $('#dataTable td:nth-child(' + value + '),#dataTable th:nth-child(' + value + ')').show();

                    // // Show the erorr column for columns 8, 10, and 12
                    // if (value == 8 || value == 10 || value == 13) {

                    //     var nextValue = (parseFloat(value) + 1).toString();
                    //     $('#dataTable td:nth-child(' + nextValue + '),#dataTable th:nth-child(' + nextValue + ')').show();
                    // }
                }
            }
        }


		function fillTable(data) {

			// Setup the row array
			var row = new Array(), j = -1;

			// Create the header string
			var header = '<tr> \
				<th style="text-align: center;">Flare ID</th> \
				<th style="text-align: center;">Flare Number</th> \
				<th style="text-align: center;">RA (deg)</th> \
				<th style="text-align: center;">Dec (deg)</th> \
				<th style="text-align: center;">r95 (deg)</th> \
				<th style="text-align: center;">Galactic l (deg)</th> \
				<th style="text-align: center;">Galactic b (deg)</th> \
				<th style="text-align: center;">Position Source</th> \
				<th style="text-align: center;">FAVA RA (deg)</th> \
				<th style="text-align: center;">FAVA Dec (deg)</th> \
				<th style="text-align: center;">FAVA Galactic l bin</th> \
				<th style="text-align: center;">FAVA Galactic b bin</th> \
				<th style="text-align: center;">FAVA Galactic l (deg)</th> \
				<th style="text-align: center;">FAVA Galactic b (deg)</th> \
				<th style="text-align: center;">Time Start</th> \
				<th style="text-align: center;">Time Stop</th> \
				<th style="text-align: center;">Sigma (LE)</th> \
				<th style="text-align: center;">Expected Events (LE)</th> \
				<th style="text-align: center;">Observed Events (LE)</th> \
				<th style="text-align: center;">Observed Events (HE)</th> \
				<th style="text-align: center;">Expected Events (HE)</th> \
				<th style="text-align: center;">Sigma (HE)</th> \
				<th style="text-align: center;">Sun Distance (deg)</th> \
				<th style="text-align: center;">Variability Index</th> \
				<th style="text-align: center;">FAVA Association</th> \
				<th style="text-align: center;">3FGL Association</th> \
				<th style="text-align: center;">Object Association</th> \
				<th style="text-align: center;">Likelihood TS (LE)</th> \
				<th style="text-align: center;">Likelihood Sigma (LE)</th> \
				<th style="text-align: center;">Likelihood RA (LE)</th> \
				<th style="text-align: center;">Likelihood DEC (LE)</th> \
				<th style="text-align: center;">Likelihood gall (LE)</th> \
				<th style="text-align: center;">Likelihood galb (LE)</th> \
				<th style="text-align: center;">Likelihood r95 (LE)</th> \
				<th style="text-align: center;">le_contflag</th> \
				<th style="text-align: center;">Sun Distance (LE)</th> \
				<th style="text-align: center;">le_dist2bb</th> \
				<th style="text-align: center;">le_ffsigma</th> \
				<th style="text-align: center;">le_hightsfrac</th> \
				<th style="text-align: center;">le_gtlts</th> \
				<th style="text-align: center;">Flux (LE)</th> \
				<th style="text-align: center;">Flux Error (LE)</th> \
				<th style="text-align: center;">Index (LE)</th> \
				<th style="text-align: center;">Index Error (LE)</th> \
				<th style="text-align: center;">Likelihood TS (HE)</th> \
				<th style="text-align: center;">Likelihood Sigma (HE)</th> \
				<th style="text-align: center;">Likelihood RA (HE)</th> \
				<th style="text-align: center;">Likelihood Dec (HE)</th> \
				<th style="text-align: center;">Likelihood gall (HE)</th> \
				<th style="text-align: center;">Likelihood galb (HE)</th> \
				<th style="text-align: center;">Likelihood r95 (HE)</th> \
				<th style="text-align: center;">he_contflag</th> \
				<th style="text-align: center;">he_sundist</th> \
				<th style="text-align: center;">he_dist2bb</th> \
				<th style="text-align: center;">he_ffsigma</th> \
				<th style="text-align: center;">he_hightsfrac</th> \
				<th style="text-align: center;">he_gtlts</th> \
				<th style="text-align: center;">he_le_dist</th> \
				<th style="text-align: center;">Flux (HE)</th> \
				<th style="text-align: center;">Flux Error (HE)</th> \
				<th style="text-align: center;">Index (HE)</th> \
				<th style="text-align: center;">Index Error (HE)</th> \
				<th style="text-align: center;">week_number</th> \
				<th style="text-align: center;">date_start</th> \
				<th style="text-align: center;">date_stop</th> \
			</tr>'




			// Loop through each data entry and add columns to the corresponding row entry
			for (var i=0, size=data.length; i<size; i++) {

			    sourceRecord = data[i];
			    row[++j] = '<tr>';

			    for (var key in sourceRecord) {

					row[++j] ='<td style="text-align: center;">';

					if (key === 'cast(num as int)') {

						row[++j] = '<a href="SourceReport.php?week=' + sourceRecord['week'] + '&flare=' + sourceRecord['cast(num as int)'] + '"">' + sourceRecord['cast(num as int)'] + '</a>';

					} else if (key === 'bestPositionSource') {

						// Add the best gall and galb
						row[++j] = calculateGalacticCoordinates(sourceRecord['best_ra'], sourceRecord['best_dec'])[0].toFixed(2)
						row[++j] = '</td>';
						row[++j] ='<td style="text-align: center;">';
						row[++j] = calculateGalacticCoordinates(sourceRecord['best_ra'], sourceRecord['best_dec'])[1].toFixed(2)
						row[++j] = '</td>';
						row[++j] ='<td style="text-align: center;">';

						if (sourceRecord[key] === 'low') {
							row[++j] = 'Like LE'
						} else if (sourceRecord[key] === 'high') {
							row[++j] = 'Like HE'
						} else {
							row[++j] = sourceRecord[key];
						}

						console.log(calculateGalacticCoordinates(sourceRecord['best_ra'], sourceRecord['best_dec']))

					} else if (key === 'he_avnev') {

						row[++j] = parseFloat(sourceRecord[key]).toFixed(2)

					} else if (key === 'he_sigma') {

						row[++j] = parseFloat(sourceRecord[key]).toFixed(2)

					} else if (key === 'tmin') {

						document.getElementById('table_metstart').innerHTML = sourceRecord[key]
						row[++j] = sourceRecord[key];

					} else if (key === 'tmax') {

						document.getElementById('table_metstop').innerHTML = sourceRecord[key]
						row[++j] = sourceRecord[key];

					} else if (key === 'dateStart') {

						document.getElementById('table_datestart').innerHTML = sourceRecord[key]
						row[++j] = sourceRecord[key];

					} else if (key === 'dateStop') {

						document.getElementById('table_datestop').innerHTML = sourceRecord[key]
						row[++j] = sourceRecord[key];

					} else if (key === 'cast(num as int)') {

						document.getElementById('table_sources').innerHTML = sourceRecord[key]
						row[++j] = sourceRecord[key];

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

			toggleColumns()

		}

		function populateMap(data) {

			console.log('Populating Map');

			var points = [];
			var point = {};

			var numberOfAssocaitedSources = 0
			var numberOfUnassocaitedSources = 0

			console.log(data.length);

			// Loop through each data entry and extract the values
			for (var i=0, size=data.length; i<size; i++) {

				// Get the current source
			    sourceRecord = data[i];

			    // Save the neccessary source properties
  			    var radius;
			    var sigma = parseFloat(sourceRecord['sigma'])
			    if (sigma < 0) {
			    	radius = 6
			    } else {
			    	radius = sigma * 1.5;
			    }

			    point = {Source_Name: sourceRecord['cast(num as int)'], ASSOC1: sourceRecord['fglassoc'], RAJ2000: sourceRecord['fava_ra'], DEJ2000: sourceRecord['fava_dec'], GLON: sourceRecord['gall'], GLAT: sourceRecord['galb'], Size: radius }

			    // Add the point to the points array
			    points.push(point);

				// Determine the number of associated and unassociated sources
				if (sourceRecord['fglassoc'] === 'None') {
					numberOfUnassocaitedSources = numberOfUnassocaitedSources + 1
				} else {
					numberOfAssocaitedSources = numberOfAssocaitedSources + 1
				}

			}

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


			console.log(points);

            // Load the map data
            window.points = points;

			renderFrame();
		}

        function Resize() {


            width=800;
            height=400;

            htmlCanvas = document.getElementById('proj');
            context = htmlCanvas.getContext('2d');
            htmlCanvas.width = width;
            htmlCanvas.height = height;

            renderFrame();
        }

		// Convert RA and Dec to galactic coordinates
		function calculateGalacticCoordinates(ra, dec) {

			// Adopted from http://www.robertmartinayers.org/tools/coordinates.html
			// Copyright Robert Martin Ayers, 2009, 2011, 2014.  All rights reserved.

			// Make sure the input are floats
			ra = parseFloat(ra);
			dec = parseFloat(dec);

			// Define some constants			
			pi = 3.1415926536
			toDegrees = 180.0/pi;
			degrees2arcseconds = 3600.;
			hours2degrees = 360/24.
			radians2degrees = 360./(2*pi)

			// From J2000 to "galactic coordinates"
			// Spherical Astronomy by Green, equation 14.55, page 355
			var JtoG = new Array (
			-0.054876, -0.873437, -0.483835,
			 0.494109, -0.444830,  0.746982,
			-0.867666, -0.198076,  0.455984 );

			var radec = new Array (99.0, 99.0);

			var ra;
			var dec;

			// Converting the user supplied ra and dec from degrees to arcseconds
			globalJRA = parseFloat(ra) * degrees2arcseconds;
			globalJDec = parseFloat(dec) * degrees2arcseconds;


			// Make sure that the coordinates make sense
			// if ( (globalJRA >= 1296000) || (globalJRA < 0) ) 
			// {  
			//     return 0; // Acting like exit 
			// }
			// if ( (globalJDec > 324000) || (globalJDec < -324000) ) 
			// { 
			//     return 0; // Acting like exit 
			// }

			var radec1 = new Array ( (globalJRA/3600.0) / toDegrees, 
			(globalJDec/3600.0) / toDegrees );

			radec = radec1;
			matrix = JtoG;

			var r0 = new Array ( 
			Math.cos(radec[0]) * Math.cos(radec[1]),
			Math.sin(radec[0]) * Math.cos(radec[1]),
			Math.sin(radec[1]) );

			var s0 = new Array (
			r0[0]*matrix[0] + r0[1]*matrix[1] + r0[2]*matrix[2], 
			r0[0]*matrix[3] + r0[1]*matrix[4] + r0[2]*matrix[5], 
			r0[0]*matrix[6] + r0[1]*matrix[7] + r0[2]*matrix[8] ); 

			var r = Math.sqrt ( s0[0]*s0[0] + s0[1]*s0[1] + s0[2]*s0[2] ); 

			var result = new Array ( 0.0, 0.0 );
			result[1] = Math.asin ( s0[2]/r ); // New dec in range -90.0 -- +90.0 
			// or use sin^2 + cos^2 = 1.0  
			var cosaa = ( (s0[0]/r) / Math.cos(result[1] ) );
			var sinaa = ( (s0[1]/r) / Math.cos(result[1] ) );
			result[0] = Math.atan2 (sinaa,cosaa);

			if ( result[0] < 0.0 ) {
				result[0] = result[0] + pi + pi;
			}

			result[0] = result[0] * radians2degrees
			result[1] = result[1] * radians2degrees

			return result
		}


		$(function() {

			// Use php to read the url parameter and set it in the analysis run info box
            <?php
				if ( (isset($_GET['week'])) ) {				
					$week = $_GET['week'];
				    $weekSetString = "document.getElementById('table_weeknumber').innerHTML = $week;";
	                echo $weekSetString;
	            } 
            ?>  

            // Get the map data
            // console.log('Calling queryDB(MapData)')
            // points = queryDB('MapData');

			queryDB_2FAV();

			
            $("#thresholdSelection").change(function(){

                var userSelectedThreshold = $(this).val();
                queryDB_2FAV();
            });


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

                        var week = document.getElementById('table_weeknumber').innerHTML; 

                        // var lightcurveLink = "<a href=\"http://localhost/~kocevski/FAVA/LightCurve.php?ra=" + point.RAJ2000 + "&dec=" + point.DEJ2000 + "\">FAVA Lightcurve</a>";
                        // var spectrum = "<a href=\"./data/3FGL_spec_v5/"  + point.Source_Name.replace(' ', '_').replace('.','d').replace('+','p') + "_spec.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">Spectrum</a>";
                        var sourceReportLink = "<a href=\"SourceReport.php?week=" + week + "&flare=" + point.Source_Name + "\">FAVA Source Report</a>";


                        document.getElementById("tip").innerHTML =   '2FAV_' + week + '_' + point.Source_Name + '<BR>RA: ' + point.RAJ2000 + ', Dec: ' + point.DEJ2000 + '<BR>Association: ' + point.ASSOC1 + '<BR>' + sourceReportLink

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
	<div id="main">	

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
				<H2>Fermi All-sky Variability Analysis (FAVA) - Weekly Analysis</H2>
			</div>
		</div>
		<!-- Header ends here -->


		<!-- sidebar start here -->
	    <div style="width:300px; margin-left:25px; float:left;" id="coordinateInput">

			<!-- Analysis information start here -->		
			<div class="panel panel-default" style="height: 225px;">
				<div class="panel-heading">
			        <h3 class="panel-title">Analysis Run</h3>
			    </div>

<!-- 			    <div class="panel-body">
					<div class="table-responsive">
			            <table class="table table-striped table-condensed">
			              <tbody>
								<tr><td>Weeks Analyzed: </td><td id="table_weekNumber1" align="right" style="padding-right:18px"></td></tr>
								<tr><td>MET Start: </td><td id="table_metstart" align="right" style="padding-right:18px"></td></tr>
								<tr><td>MET Stop: </td><td id="table_metstop" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Date Start: </td><td id="table_datestart" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Date Stop: </td><td id="table_datestop" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  
 				    </div>
				</div> -->

					<center>

			            <table class="table table-striped">
			              <tbody>					
								<tr><td>Weeks Number: </td><td id="table_weeknumber" align="right" style="padding-right:18px"></td></tr>
								<tr><td>MET Start: </td><td id="table_metstart" align="right" style="padding-right:18px"></td></tr>
								<tr><td>MET Stop: </td><td id="table_metstop" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Date Start: </td><td id="table_datestart" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Date Stop: </td><td id="table_datestop" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  

				    </center>

			</div>
			<!-- Position information ends here -->		


			<!-- FAVA Analysis Overview start here -->		
			<div class="panel panel-default"  style="height: 162px;">
				<div class="panel-heading">
			        <h3 class="panel-title">Analysis Overview</h3>
			     </div>
			     <!-- <div class="panel-body"> -->

					<center>

			            <table class="table table-striped">
			              <tbody>					
  								<!-- <tr><td>Weeks Analyzed</td><td td id="table_weekNumber2" align="right" style="padding-right:25px"></td></tr>			 -->
  								<tr><td>FAVA Detections (>6&sigma;)</td><td td id="table_favaDetections" align="right" style="padding-right:25px"></td></tr>			
  								<tr><td>Associated Detections<canvas id="AssociatedCanvas" width="40" height="20"></td><td td id="table_associatedDetections" align="right" style="padding-right:25px"></td></tr>			
  								<tr><td>Unassociated Detections<canvas id="unassociatedCanvas" width="30" height="20"></canvas></td><td td id="table_unassociatedDetections" align="right" style="padding-right:25px"></td></tr>			
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
								<!-- <tr><td><a href="CatalogView_2FAV.php">2nd FAVA Catalog</a></td><td td id="table1_2FAV"></td></tr> -->
								<tr><td>2nd FAVA Catalog</td><td td id="table1_2FAV"></td></tr>
								<tr><td><a href="About.html">About FAVA</a></td><td></td></tr>		
			              </tbody>
			            </table>  

				    </center>

		    </div>
			<!-- FAVA Resources ends here -->		
	

			<!-- Download panel start here -->		
			<div class="panel panel-default">
				<div class="panel-heading">
			        <h3 class="panel-title">Table Options</h3>
			     </div>
			     <div class="panel-body">

			     <center>
					<button data-toggle="modal" href="#ConfigureTableModal" type="submit" class="btn btn-primary" style="color:white;margin:5px">Configure Table</button>
              	</center>

		      	</div>
		    </div>
			<!-- Download panel ends here -->	
	

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
					<div class="alert alert-success" role="alert">Please reference <a href="http://adsabs.harvard.edu/abs/2017ApJ...846...34A">Abdollahi et al. 2017</a> for any use of the presented results</div>
			    </center>
		    </div>
			<!--  Citation request ends here -->	


		</div>
		<!-- sidebar ends here -->


		<!-- Content starts here -->
		<div id="content">




			<!-- FAVA Flare map panel start here -->	
			<!-- <div style="width:1500px; height:1000px; margin-left: 340px;"> -->
<!-- 
			<div class="panel panel-default" style="width:1500px; height:500px; margin-left: 340px;">
				<div class="panel-heading"><h3 class="panel-title">Low Energy Flare Map (100 - 800 MeV)</h3></div>

			     <div class="panel-body"> -->

		            <!-- Map canvas div starts here -->
		            <!-- <div style="width:100%; float: left"> -->



		            <!-- </div> -->
		            <!-- Map canvas div ends here -->

				<!-- </div>	 -->

				<!-- </div> -->
			<!-- </div> -->
			<!-- FAVA flare panel ends here -->	


			<!-- FAVA Flare map panel start here -->	
<!-- 		    <div style="width:600px; margin-left: 650px;">
		 		<div class="panel panel-default" style="height: 600px;">
					<div class="panel-heading"><h3 class="panel-title">FAVA Flare Map</h3></div>

				     <div class="panel-body">

						<center>

						<div id="contentPlaceholderHigh" style="margin-top:50px; font-weight: normal; color:#ddd; vertical-align: top;"> 							
						</div>


						</center>

					</div>	

	      		</div>
		    </div> -->
			<!-- FAVA flare panel ends here -->	

			<!-- FAVA Flare map panel start here -->	
		    <div style="width:1550px; margin-left: 340px;">
		 		<div class="panel panel-default" style="height: 600px;">
					<div class="panel-heading"><h3 class="panel-title">Weekly Flare Map</h3></div>

				     <div class="panel-body">

						<center>

							<div id="tip" style="visibility: hidden"></div>
							<div id="Selection" style="visibility: hidden" width=10 height=10></div>

			                <div id="map-parent" style="margin-top:0px">
			                    <center>
			                    </center>
			                </div>

			                <div id="map-tip" style="display: none; color: #808080; margin-top:-15px">
			                	Double click a flare for additional information
			                </div>

						</center>

					</div>	

	      		</div>
		    </div>
			<!-- FAVA flare panel ends here -->	


			<!-- Weekly analysis panel start here -->	
		    <div style="width:1550px; margin-left: 340px;">
			 	<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Weekly FAVA Flares</h3></div>
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

        <div class="footer">
        <!-- &copy; Copyright 2012-2014. Created by <a href="http://driven-by-data.net">Gregor Aisch</a>. -->
        </div>

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


    <!-- Modal view starts here -->
    <div id="ConfigureTableModal" class="modal modal-wide fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title" style="font-size: 18px; font-weight: normal; color: #333; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">Table Configuration</h4>
                </div>

                <div class="modal-body"  style="font-size: 14px; font-weight: normal; color: #333; line-height: 1.42857143; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                  <!-- <iframe frameborder="0" scrolling="no" width="700" height="700" src="http://google.about.com/b/2013/04/01/google-nose.htm"></iframe>-->

                    <style>
                        table.notifications th, table.notifications td {font-size: 14px !important;font: normal 1em 'Helvetica Neue', Helvetica, Arial, sans-serif; padding-bottom: 4px}
                    </style>

                    <form id="TableConfiguration" name='TableConfiguration'>
                        <div style="width: 100%; overflow: hidden;">
                            <div id="ColumnSelect" name="ColumnSelect" style="width: 325px; float: left; margin-left:10px"> 

                                <b>FAVA Analysis:</b><br>
                                <div style="display: none"> <input type="checkbox" name="select" value="1" onclick="toggleColumn(this)">  Unique Flare Number<br></div>
                                <input type="checkbox" name="select" checked value="2" onclick="toggleColumn(this)">  Source Number<br>
                                <input type="checkbox" name="select" checked value="3" onclick="toggleColumn(this)">  Best RA (J2000.0)<br>
                                <input type="checkbox" name="select" checked value="4" onclick="toggleColumn(this)">  Best Dec (J2000.0)<br>
                                <input type="checkbox" name="select" checked value="5" onclick="toggleColumn(this)">  Best r95 (J2000.0)<br>
                                <input type="checkbox" name="select" checked value="6" onclick="toggleColumn(this)">  Best Galactic l<br>
                                <input type="checkbox" name="select" checked value="7" onclick="toggleColumn(this)">  Best Galactic b<br>
                                <input type="checkbox" name="select" checked value="8" onclick="toggleColumn(this)">  Position Source<br>
                                <input type="checkbox" name="select" value="9" onclick="toggleColumn(this)">  FAVA RA (J2000.0)<br>
                                <input type="checkbox" name="select" value="10" onclick="toggleColumn(this)">  FAVA Dec (J2000.0)<br>
                                <input type="checkbox" name="select" value="11" onclick="toggleColumn(this)">  FAVA lbin<br>
                                <input type="checkbox" name="select" value="12" onclick="toggleColumn(this)">  FAVA bbin<br>
                                <input type="checkbox" name="select" value="13" onclick="toggleColumn(this)">  FAVA Galactic l<br>
                                <input type="checkbox" name="select" value="14" onclick="toggleColumn(this)">  FAVA Galactic b<br>
                                <input type="checkbox" name="select" value="15" onclick="toggleColumn(this)">  MET Start<br>
                                <input type="checkbox" name="select" value="16" onclick="toggleColumn(this)">  MET Stop<br>
                                <input type="checkbox" name="select" checked value="17" onclick="toggleColumn(this)">  Expected Number of Events (Low Energy)<br>
                                <input type="checkbox" name="select" checked value="18" onclick="toggleColumn(this)">  Observed Number of Events (Low Energy)<br>
                                <input type="checkbox" name="select" checked value="19" onclick="toggleColumn(this)">  FAVA Significance (Low Energy)<br>
                                <input type="checkbox" name="select" checked value="20" onclick="toggleColumn(this)">  Expected Number of Events (High Energy)<br>
                                <input type="checkbox" name="select" checked value="21" onclick="toggleColumn(this)">  Observed Number of Events (High Energy)<br>
                                <input type="checkbox" name="select" checked value="22" onclick="toggleColumn(this)">  FAVA Significance (High Energy)<br>
                                <input type="checkbox" name="select" checked value="23" onclick="toggleColumn(this)">  Sun Distance<br>
                                <input type="checkbox" name="select" value="24" onclick="toggleColumn(this)">  Variability Index<br>
                                <input type="checkbox" name="select" value="25" onclick="toggleColumn(this)">  FAVA Association<br>
                                <input type="checkbox" name="select" checked value="26" onclick="toggleColumn(this)">  3FGL Association<br>
                                <input type="checkbox" name="select" checked value="27" onclick="toggleColumn(this)">  Catalog Association<br>


                            </div>

                            <div style="margin-left: 25px; float:left;">
								<b>Likelihood Analysis (100 - 800 MeV):</b><br>
                                <input type="checkbox" name="select" checked value="28" onclick="toggleColumn(this)">  Likelihood TS (Low Energy)<br>
                                <input type="checkbox" name="select" value="29" onclick="toggleColumn(this)">  Likelihood Significance (Low Energy)<br>
                                <input type="checkbox" name="select" value="30" onclick="toggleColumn(this)">  Likelihood RA (Low Energy)<br>
                                <input type="checkbox" name="select" value="31" onclick="toggleColumn(this)">  Likelihood Dec (Low Energy)<br>
                                <input type="checkbox" name="select" value="32" onclick="toggleColumn(this)">  Likelihood Galactic l (Low Energy)<br>
                                <input type="checkbox" name="select" value="33" onclick="toggleColumn(this)">  Likelihood Galactic b (Low Energy)<br>
                                <input type="checkbox" name="select" value="34" onclick="toggleColumn(this)">  Likelihood 95% Position Error (Low Energy)<br>
                                <input type="checkbox" name="select" value="35" onclick="toggleColumn(this)">  TS Map Contour Flag (Low Energy)<br>
                                <input type="checkbox" name="select" value="36" onclick="toggleColumn(this)">  Likelihood Sun Distance (Low Energy)<br>
                                <input type="checkbox" name="select" value="37" onclick="toggleColumn(this)">  TS Map Border Distance (Low Energy)<br>
                                <div style="display: none"><input type="checkbox" name="select"  value="38" onclick="toggleColumn(this)">  le_ffsigma<br></div>
                                <div style="display: none"><input type="checkbox" name="select"  value="39" onclick="toggleColumn(this)">  le_hightsfrac<br></div>
                                <div style="display: none"><input type="checkbox" name="select"  value="40" onclick="toggleColumn(this)">  le_gtlts<br></div>
                                <input type="checkbox" checked name="select" value="41" onclick="toggleColumn(this)">  Likelihood Flux (Low Energy)<br>
                                <input type="checkbox" name="select" value="42" onclick="toggleColumn(this)">  Likelihood Flux Error (Low Energy)<br>
                                <input type="checkbox" checked name="select" value="43" onclick="toggleColumn(this)">  Likelihood Index (Low Energy)<br>
                                <input type="checkbox" name="select" value="44" onclick="toggleColumn(this)">  Likelihood Index Error (Low Energy)<br>

	                            <div id="ThresholdSelectionDiv" style="margin-top: 50px;">
	                                <b>Flare Threshold Selection:</b><br>
	                                <div class="input-group input-group-sm">
	                                    <select id="thresholdSelection" name="thresholdSelection">
	                                        <option value="3Sigma">3 sigma (FAVA Sigma > 3)</option>
	                                        <option value="6Sigma"selected>6 sigma (FAVA Sigma > 6 or TS > 39)</option>
	                                    </select>
	                                </div>
	                            </div>

                            </div>


                            <div style="margin-left: 25px; float:left;">

								<b>Likelihood Analysis (800 MeV - 300 GeV):</b><br>
                                <input type="checkbox" name="select" checked value="45" onclick="toggleColumn(this)">  Likelihood TS (High Energy)<br>
                                <input type="checkbox" name="select" value="46" onclick="toggleColumn(this)">  Likelihood Significance (High Energy)<br>
                                <input type="checkbox" name="select" value="47" onclick="toggleColumn(this)">  Likelihood RA (High Energy)<br>
                                <input type="checkbox" name="select" value="48" onclick="toggleColumn(this)">  Likelihood Dec (High Energy)<br>
                                <input type="checkbox" name="select" value="49" onclick="toggleColumn(this)">  Likelihood Galactic l (High Energy)<br>
                                <input type="checkbox" name="select" value="50" onclick="toggleColumn(this)">  Likelihood Galactic b (High Energy)<br>
                                <input type="checkbox" name="select" value="51" onclick="toggleColumn(this)">  Likelihood 95% Position Error (High Energy)<br>
                                <input type="checkbox" name="select" value="52" onclick="toggleColumn(this)">  TS Map Contour Flag (High Energy)<br>
                                <input type="checkbox" name="select" value="53" onclick="toggleColumn(this)">  Likelihood Sun Distance (High Energy)<br>
                                <input type="checkbox" name="select" value="54" onclick="toggleColumn(this)">  TS Map Border Distance (High Energy)<br>
                                <div style="display: none"><input type="checkbox" name="select"  value="55" onclick="toggleColumn(this)">  he_ffsigma<br></div>
                                <div style="display: none"><input type="checkbox" name="select"  value="56" onclick="toggleColumn(this)">  he_hightsfrac<br></div>
                                <div style="display: none"><input type="checkbox" name="select"  value="57" onclick="toggleColumn(this)">  he_gtlts<br></div>
                                <div style="display: none"><input type="checkbox" name="select"  value="58" onclick="toggleColumn(this)">  he_le_dist<br></div>
                                <input type="checkbox" checked name="select" value="59" onclick="toggleColumn(this)">  Likelihood Flux (High Energy)<br>
                                <input type="checkbox" name="select" value="60" onclick="toggleColumn(this)">  Likelihood Flux Error (High Energy)<br>
                                <input type="checkbox" checked name="select" value="61" onclick="toggleColumn(this)">  Likelihood Index (High Energy)<br>
                                <input type="checkbox" name="select" value="62" onclick="toggleColumn(this)">  Likelihood Index Error (High Energy)<br>

                                <BR>
                                <div style="display: none"><input type="checkbox" name="select" value="63" onclick="toggleColumn(this)"> Week<br></div>
                                <div style="display: none"><input type="checkbox" name="select" value="64" onclick="toggleColumn(this)">  Date Start<br></div>
                                <div style="display: none"><input type="checkbox" name="select" value="65" onclick="toggleColumn(this)">  Date Stop<br></div>
                                <div style="display: none"><input type="checkbox" name="select" value="66" onclick="toggleColumn(this)">  Date Stop<br></div>

                            </div>

                        </div>
                    </form>

                </div>

                <div class="modal-footer">
                    <div style="float: right; margin-right: 4px;"> 
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="color:black;font-size: 12px;margin-top:10px">Close</button>
                    </div>           
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Modal view ends here -->



</body>


