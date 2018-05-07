<?php

    function AngularDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        $angle_degrees = $angle * (180/pi());

        return $angle_degrees;

    }

    if (isset($_GET['typeOfRequest']) == false) {
        echo '<BR><B>Usage Examples:</B><BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=2FAV<BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=TimebinData<BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=SourceList&week=100<BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=SourceList&week=100&threshold=6Sigma<BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=SourceReport&week=100&flare=1<BR>';
        echo 'queryFlaresDB_2FAV.php?typeOfRequest=MapData&ra=0&dec=0&radius=12&threshold=6Sigma<BR>';

    } else {

        // Determine the type of data requested
        $typeOfRequest = $_GET['typeOfRequest']; 

    }

    // Setup the databa info
    $servername = "asddb.gsfc.nasa.gov";
    $username = "favaread";
    $password = "IhopeFAVAdataworks";

    // Initiate the database connection
    $conn = mysql_connect($servername, $username, $password);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    // echo "Connected successfully";


    // echo "<BR><BR>type of request<BR>";
    // echo $typeOfRequest;
    // echo "<BR><BR>";


    // Select the database
    mysql_select_db('FAVA');

    // Return the 2FAV catalog (2933 flares)
    if ($typeOfRequest === '2FAV') { 

        $and = ' and ';
        $or = ' or ';

        $cut1 = '((week < 340) and (he_sigma>6) and (sundist>10))';
        $cut2 = '((week < 340) and (sigma>6) and (sundist>10))';
        $cut3 = '((week < 340) and ((sigma>4) and (he_sigma>4) and (sundist>10)) and ((sigma<=6) or (he_sigma<=6)))';

        $cut4 = '((week < 340) and ((he_ts>39) and (he_sundist>10)) and ((he_contflag=0) or (he_contflag=1)))';
        $cut5 = '((week < 340) and ((le_ts>39) and (le_sundist>10)) and ((le_contflag=0) or (le_contflag=1)))';
        $cut6 = '((week < 340) and ((le_ts>18) and (he_ts>18)) and ((le_sundist>10) and (he_sundist>10)) and ((le_contflag=0) or (le_contflag=1)) and ((he_contflag=0) or (he_contflag=1)) and (he_le_dist<1.5) and ((he_ts<=39) or (le_ts<=39)))';

        $queryStatement = 'SELECT flareID, num, best_ra, best_dec, best_r95, bestPositionSource, fava_ra, fava_dec, lbin, bbin, gall, galb, tmin, tmax, sigma, avnev, nev, he_nev, he_avnev, he_sigma, sundist, varindex, favasrc, fglassoc, assoc, le_ts, le_tssigma, le_ra, le_dec, le_gall, le_galb, le_r95, le_contflag, le_sundist, le_dist2bb, le_ffsigma, le_hightsfrac, le_gtlts, le_flux, le_fuxerr, le_index, le_indexerr, he_ts, he_tssigma, he_ra, he_dec, he_gall, he_galb, he_r95, he_contflag, he_sundist, he_dist2bb, he_ffsigma, he_hightsfrac, he_le_dist, he_gtlts, he_flux, he_fuxerr, he_index, he_indexerr, week, dateStart, dateStop from flares WHERE ' . $cut1 . $or . $cut2 . $or . $cut3 . $or . $cut4 . $or . $cut5 . $or . $cut6 . ' ORDER BY best_ra ASC';


        // echo "Query Statement:<BR>";
        // echo $queryStatement;
        // echo "<BR>";

        // Query the database
        // $results = $db->query($queryStatement);
        $retval = mysql_query($queryStatement, $conn);

        if(! $retval ) {
            die('Could not get data: ' . mysql_error());
        }

        // Create an array to store the results
        $data = array();

        // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
        while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            $data[] = $row;
        }  

        // Encode the PHP associative array into a JSON associative array
        echo json_encode($data);
    }

    // Return timebin data
    if ($typeOfRequest === 'TimebinData') { 

        // Construct the query statement 
        $queryStatement = 'select distinct week, tmin, tmax, dateStart, dateStop from flares order by cast(week as int) DESC;' ;

        // echo "Query Statement:<BR>";
        // echo $queryStatement;
        // echo "<BR>";

        // Query the database
        // $results = $db->query($queryStatement);
        $retval = mysql_query($queryStatement, $conn);

        // Create an array to store the results
        $data = array();

        // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
        while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
            $data[] = $row;
        }  

        // Encode the PHP associative array into a JSON associative array
        echo json_encode($data);
    }

    // Return timebin data
    if ($typeOfRequest === 'SourceList') { 

        if (isset($_GET['week'])) { 

            // Get the URL parameters
            $week = $_GET['week'];
            $threshold = $_GET['threshold'];

            $and = ' and ';
            $or = ' or ';

            // Construct the query statement 
            if ($threshold === '6Sigma') {

                $cut1 = '(week = ' . $week . ' and (he_sigma>6) and (sundist>10))';
                $cut2 = '(week = ' . $week . '  and (sigma>6) and (sundist>10))';
                $cut3 = '(week = ' . $week . '  and ((sigma>4) and (he_sigma>4) and (sundist>10)) and ((sigma<=6) or (he_sigma<=6)))';

                $cut4 = '(week = ' . $week . '  and ((he_ts>39) and (he_sundist>10)) and ((he_contflag=0) or (he_contflag=1)))';
                $cut5 = '(week = ' . $week . '  and ((le_ts>39) and (le_sundist>10)) and ((le_contflag=0) or (le_contflag=1)))';
                $cut6 = '(week = ' . $week . '  and ((le_ts>18) and (he_ts>18)) and ((le_sundist>10) and (he_sundist>10)) and ((le_contflag=0) or (le_contflag=1)) and ((he_contflag=0) or (he_contflag=1)) and (he_le_dist<1.5) and ((he_ts<=39) or (le_ts<=39)))';

                $queryStatement = 'SELECT flareID, num, best_ra, best_dec, best_r95, bestPositionSource, fava_ra, fava_dec, lbin, bbin, gall, galb, tmin, tmax, sigma, avnev, nev, he_nev, he_avnev, he_sigma, sundist, varindex, favasrc, fglassoc, assoc, le_ts, le_tssigma, le_ra, le_dec, le_gall, le_galb, le_r95, le_contflag, le_sundist, le_dist2bb, le_ffsigma, le_hightsfrac, le_gtlts, le_flux, le_fuxerr, le_index, le_indexerr, he_ts, he_tssigma, he_ra, he_dec, he_gall, he_galb, he_r95, he_contflag, he_sundist, he_dist2bb, he_ffsigma, he_hightsfrac, he_le_dist, he_gtlts, he_flux, he_fuxerr, he_index, he_indexerr, week, dateStart, dateStop from flares WHERE ' . $cut1 . $or . $cut2 . $or . $cut3 . $or . $cut4 . $or . $cut5 . $or . $cut6 . ' ORDER BY num ASC';

                // echo "Query Statement:<BR>";
                // echo $queryStatement;
                // echo "<BR>";

            } else {

                $queryStatement = 'SELECT flareID, num, best_ra, best_dec, best_r95, bestPositionSource, fava_ra, fava_dec, lbin, bbin, gall, galb, tmin, tmax, sigma, avnev, nev, he_nev, he_avnev, he_sigma, sundist, varindex, favasrc, fglassoc, assoc, le_ts, le_tssigma, le_ra, le_dec, le_gall, le_galb, le_r95, le_contflag, le_sundist, le_dist2bb, le_ffsigma, le_hightsfrac, le_gtlts, le_flux, le_fuxerr, le_index, le_indexerr, he_ts, he_tssigma, he_ra, he_dec, he_gall, he_galb, he_r95, he_contflag, he_sundist, he_dist2bb, he_ffsigma, he_hightsfrac, he_le_dist, he_gtlts, he_flux, he_fuxerr, he_index, he_indexerr, week, dateStart, dateStop from flares WHERE week == ' . $week . ' ORDER BY num ASC';

                // echo "Query Statement:<BR>";
                // echo $queryStatement;
                // echo "<BR>";

            }
            
            // Query the database
            // $results = $db->query($queryStatement);
            $retval = mysql_query($queryStatement, $conn);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $data[] = $row;
            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);

        }
    }

    // Return timebin data
    if ($typeOfRequest === 'SourceReport') { 

        if (isset($_GET['week']) && isset($_GET['flare'])) { 

            // Get the URL parameters
            $week = $_GET['week'];
            $flare = $_GET['flare'];
            $flareID = $week . '_' . $flare;

            $and = ' and ';
            $or = ' or ';

            // Construct the query statement 
            $queryStatement = "SELECT * from flares WHERE flareID = '" . $flareID . "'";

            // echo "Query Statement:<BR>";
            // echo $queryStatement;
            // echo "<BR>";

            // Query the database
            $retval = mysql_query($queryStatement, $conn);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $data[] = $row;
            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);

        }
    }

    // Return basic information on all sources to be displayed in the map
    if ($typeOfRequest === 'MapData') { 

        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius']) && isset($_GET['threshold'])) { 

            // Get the URL parameters
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius'];
            $threshold = $_GET['threshold'];
            
            // Construct the logical operators
            $and = ' and ';
            $or = ' or ';

            // Construct the query statement 
            if ($threshold === '6Sigma') {

                $cut1 = '((he_sigma>6) and (sundist>10) )';
                $cut2 = '((sigma>6) and (sundist>10) )';
                $cut3 = '(((sigma>4) and (he_sigma>4) and (sundist>10)) and ((sigma<=6) or (he_sigma<=6)))';

                $cut4 = '(((he_ts>39) and (he_sundist>10)) and ((he_contflag=0) or (he_contflag=1)))';
                $cut5 = '(((le_ts>39) and (le_sundist>10)) and ((le_contflag=0) or (le_contflag=1)))';
                $cut6 = '(((le_ts>18) and (he_ts>18)) and ((le_sundist>10) and (he_sundist>10)) and ((le_contflag=0) or (le_contflag=1)) and ((he_contflag=0) or (he_contflag=1)) and (he_le_dist<1.5) and ((he_ts<=39) or (le_ts<=39)))';

                $queryStatement = 'SELECT * from flares WHERE ' . $cut1 . $or . $cut2 . $or . $cut3 . $or . $cut4 . $or . $cut5 . $or . $cut6;

                // echo "Query Statement:<BR>";
                // echo $queryStatement;
                // echo "<BR>";

            } else {

                // -- $queryStatement = 'SELECT * from flares';
                $queryStatement = 'SELECT * from flares';

                // echo "Query Statement:<BR>";
                // echo $queryStatement;
                // echo "<BR>";

            }

            // Query the database
            // $results = $db->query($queryStatement);
            $retval = mysql_query($queryStatement, $conn);

            // Create an array to store the results
            $data = array();
            $count = 0;

            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {

                // Get the ra and dec of each source
                $raSource = $row['fava_ra'];
                $decSource = $row['fava_dec'];

                // Find the distance to the user specified coordinates
                $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                if ($distance < $radius) {
                    $data[] = $row;

                    $count = $count + 1;

                }

            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);

        } 
    }

    if ($typeOfRequest === 'FlareList') { 

            // Construct the logical operators
            $and = ' and ';
            $or = ' or ';

            // Construct the cut parameters
            $cut1 = '(he_sigma>6) and (sundist>10)';
            $cut2 = '(sigma>6) and (sundist>10)';
            $cut3 = '((sigma>4) and (he_sigma>4) and (sundist>10)) and ((sigma<=6) or (he_sigma<=6))';

            $cut4 = '((he_ts>39) and (he_sundist>10)) and ((he_contflag=0) or (he_contflag=1))';
            $cut5 = '((le_ts>39) and (le_sundist>10)) and ((le_contflag=0) or (le_contflag=1))';
            $cut6 = '((le_ts>18) and (he_ts>18)) and ((le_sundist>10) and (he_sundist>10)) and ((le_contflag=0) or (le_contflag=1)) and ((he_contflag=0) or (he_contflag=1)) and (he_le_dist<1.5) and ((he_ts<=39) or (le_ts<=39))';

           // Construct the query statement 
            $queryStatement = 'SELECT flareID, best_ra, best_dec, gall, galb, sigma, fglassoc from flares WHERE ' . $cut1 . $or . $cut2 . $or . $cut3 . $or . $cut4 . $or . $cut5 . $or . $cut6;

            // echo "Query Statement:<BR>";
            // echo $queryStatement;
            // echo "<BR>";

            // Query the database
            $retval = mysql_query($queryStatement, $conn);

            // Create an array to store the results
            $data = array();

            // Loop through each row and create an associative array (i.e. dictionary) where the column name is the key
            while($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
                $data[] = $row;
            }  

            // Encode the PHP associative array into a JSON associative array
            echo json_encode($data);
    }

?>  






