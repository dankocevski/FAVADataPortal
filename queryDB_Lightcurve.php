<?php

    // phpinfo();
    function distance($latA, $lonA, $latB, $lonB) {
            // convert from degrees to radians
            $latA = deg2rad($latA); $lonA = deg2rad($lonA);
            $latB = deg2rad($latB); $lonB = deg2rad($lonB);

            // calculate absolute difference for latitude and longitude
            $dLat = ($latA - $latB);
            $dLon = ($lonA - $lonB);

            // do trigonometry magic
            $d =
                    sin($dLat/2) * sin($dLat/2) +
                    cos($latA) * cos($latB) * sin($dLon/2) *sin($dLon/2);
            $d = 2 * asin(sqrt($d));
            return $d * 6371;
    }


    // Get the url parameters
    $raUser = $_GET['ra'];
    $decUser = $_GET['dec'];

    // Initiate the database connection
    // $db = new SQLite3 ('./db/geohash.db');
    // $db = new SQLite3 ('./db/fava.db');
    $db = new SQLite3 ('./db/fava_lightcurve.db');

    $queryStatement = 'SELECT ra, dec FROM geohash' ;
    // $queryStatement = 'SELECT radec FROM geohash' ;

    echo "Query Statement:<BR>";
    echo $queryStatement;
    echo "<BR>";

    // // // Query the database
    // $results = $db->query($queryStatement);

    // // // Create an array to store the results
    // // // $ra = array();
    // // // $dec = array();

    // $distance = array();

    // // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
    // while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

    //     $data[] = $row;
        
    //     $ra = $row['ra'];
    //     $dec = $row['dec'];
    //     $radec = $row['ra'] . 'x' . $row['dec'];

    //     // $radec = $row;
    //     // $substrings = explode('x',$radec);
    //     // $ra = $substrings[0];
    //     // $dec = $substrings[1];

    //     $distance[$radec] = distance($dec, $ra, floatval($decUser), floatval($raUser));

    //     // echo "$ra x $dec - $distance <BR>";
    // }  

    // $radec_closest = min(array_keys($distance, min($distance)));

    // // echo "Closest geohash bin to user supplied coordinates: ra = $raUser, dec = $decUser";
    // // echo "<BR>";
    // // echo $radec_closest; 

    // // Get the url parameters
    // // $radec = $_GET['radec'];

    // // Construct the SQL command        
    // $queryStatement = 'SELECT (tmin + tmax)/2.0 AS time, nev, avnev, sigma, he_nev, he_avnev, he_sigma FROM data WHERE radec == "' . $radec_closest . '" ORDER BY tmin';

    // // echo "Query Statement:<BR>";
    // // echo $queryStatement;
    // // echo "<BR>";

    // // Query the database
    // $results = $db->query($queryStatement);

    // // Create an array to store the results
    // $data = array();

    // // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
    // while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

    //     $data[] = $row;

    // }  

    // Encode the PHP associative array into a JSON associative array
    // echo json_encode($data);


?>  






