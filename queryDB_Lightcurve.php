<?php

    // Increase the timeout and memory limits
    ini_set('max_execution_time', 60);
    ini_set('memory_limit','256M');

    function distance($latA, $lonA, $latB, $lonB) {
            // convert from degrees to radians
            $latA = deg2rad($latA); $lonA = deg2rad($lonA);
            $latB = deg2rad($latB); $lonB = deg2rad($lonB);

            // calculate absolute difference for latitude and longitude
            $dLat = ($latA - $latB);
            $dLon = ($lonA - $lonB);

            // do trigonometry magic
            $d = sin($dLat/2) * sin($dLat/2) + cos($latA) * cos($latB) * sin($dLon/2) *sin($dLon/2);
            $d = 2 * asin(sqrt($d));
            return $d * 6371;
    }

    // Get the url parameters
    $raUser = $_GET['ra'];
    $decUser = $_GET['dec'];

    // Setup the databa info
    $servername = "asddb.gsfc.nasa.gov";
    $username = "favaread";
    $password = "IhopeFAVAdataworks";

    // Initiate the database connection
    // $conn = new mysqli($servername, $username, $password);
    $conn = mysql_connect($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    // echo "Connected successfully";

    // Create the query statement
    $queryStatement = 'SELECT RAs, Decs FROM geohash';

    // echo "<BR><BR>Query Statement:<BR>";
    // echo $queryStatement;
    // echo "<BR>";


    // Select the database
    mysql_select_db('FAVA');

    // Query the database
    $retval = mysql_query($queryStatement, $conn);

    if(! $retval ) {
        die('Could not get data: ' . mysql_error());
    }

    $distance = array();

    // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {

        // $data[] = $row;
        
        $ra = $row['RAs'];
        $dec = $row['Decs'];
        $radec = $row['RAs'] . 'x' . $row['Decs'];

        $distance[$radec] = distance($dec, $ra, floatval($decUser), floatval($raUser));

        // echo "$ra x $dec - $distance <BR>";
    }  

    $radec_closest = min(array_keys($distance, min($distance)));

    // echo "Closest geohash bin to user supplied coordinates: ra = $raUser, dec = $decUser";
    // echo "<BR>";
    // echo $radec_closest; 

    // Construct the SQL command        
    $queryStatement = "SELECT (cast(tmin as SIGNED) + cast(tmax as SIGNED))/2.0 AS time, nev, avnev, sigma, he_nev, he_avnev, he_sigma FROM data WHERE radec = '" . $radec_closest . "' ORDER BY tmin";

    // echo "Query Statement:<BR>";
    // echo $queryStatement;
    // echo "<BR>";

    // Query the database
    $retval = mysql_query($queryStatement, $conn);

    if(! $retval ) {
        die('Could not get data: ' . mysql_error());
    }

    // Create an array to store the results
    $data = array();

    // // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
    while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {

        $data[] = $row;

    }  

    // Encode the PHP associative array into a JSON associative array
    echo json_encode($data);

    // echo "Fetched data successfully\n";
    mysql_close($conn);

?> 