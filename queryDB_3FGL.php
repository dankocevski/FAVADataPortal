<?php

    define('MYSQL_ASSOC',MYSQLI_ASSOC);

    error_reporting(E_ALL);
    ini_set('display_errors', '1');
        
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
        echo 'queryDB_3FGL.php?typeOfRequest=MapData&ra=0&dec=0&radius=12<BR>';

    } else {

        // Determine the type of data requested
        $typeOfRequest = $_GET['typeOfRequest'];

    }

    // Return basic information on all sources to be displayed in the map
    if ($typeOfRequest === 'MapData') { 

       if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }

        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius']; 
        }
        
        $queryStatement = 'SELECT Source_Name, ASSOC1, Type, RAJ2000, DEJ2000, GLON, GLAT, Size FROM Catalog ' . $CLASSTYPE . $NAME ;

        $db = new SQLite3('./db/gll_psc_v14.db');
        $results = $db->query($queryStatement);

        $data = array();

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                // Get the ra and dec of each source
                $raSource = $row['RAJ2000'];
                $decSource = $row['DEJ2000'];

                // Find the distance to the user specified coordinates
                $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                if ($distance < $radius) {
                    $data[] = $row;
                }

            } else {

                $data[] = $row;

            }

        }  

        echo json_encode($data);

    } 

    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'TableData' || $typeOfRequest === 'ReloadTableData') { 

        if (isset($_GET['lines'])) { 
            $lines = $_GET['lines'];
            $LIMIT = ' LIMIT ' . $lines;
        } else { 
            $lines = 100; 
            $LIMIT = ' LIMIT ' . $lines;
        }

        if (isset($_GET['offset'])) { 
            $offset = $_GET['offset']; 
            $OFFSET = ' OFFSET ' . $offset;
        } else { 
            $offset = 0; 
            $OFFSET = ' OFFSET ' . $offset;
        }

        if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }


        if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 
            $raROI = $_GET['ra']; 
            $decROI = $_GET['dec']; 
            $radius = $_GET['radius']; 
        }

        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ' . $CLASSTYPE . $NAME . $LIMIT . $OFFSET ;
        // echo $queryStatement;
        // echo "<BR><BR>";

        $db = new SQLite3('./db/gll_psc_v14.db');
        $results = $db->query($queryStatement);

        $data = array();

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            if (isset($_GET['ra']) && isset($_GET['dec']) && isset($_GET['radius'])) { 

                // Get the ra and dec of each source
                $raSource = $row['RAJ2000'];
                $decSource = $row['DEJ2000'];

                // Find the distance to the user specified coordinates
                $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

                if ($distance < $radius) {
                    $data[] = $row;
                }

            } else {

                $data[] = $row;

            }
        }  

        echo json_encode($data);

    } 


    if ($typeOfRequest === 'ROISearchCelestial') { 

        if (isset($_GET['ra'])) { 
            $raROI = $_GET['ra']; 
        } 
        if (isset($_GET['dec'])) { 
            $decROI = $_GET['dec']; 
        } 
        if (isset($_GET['radius'])) { 
            $radius = $_GET['radius']; 
        } 

        if (isset($_GET['Class'])) { 
            $ClassValues = $_GET['Class']; 
            $CLASSTYPE = 'where Type == ' . str_replace("' '", "' OR Type == '", $ClassValues) . " COLLATE NOCASE";
        } else { 
            $CLASSTYPE = '';
        }

        if (isset($_GET['Name'])) { 

            $SearchQuery = $_GET['Name'];
            $SearchQuery = "'%" . $SearchQuery . "%'";

            if (isset($_GET['Class'])) { 
                $NAME = " AND (Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE)";
            } else {
                $NAME = "WHERE Source_Name like " . $SearchQuery . " OR ASSOC1 like " . $SearchQuery . " OR ASSOC_TEV like " . $SearchQuery . " COLLATE NOCASE";
            }

        } else { 
            $NAME = '';
        }

        $db = new SQLite3('./db/gll_psc_v14.db');
        $queryStatement = 'SELECT Source_Name, ASSOC1, RAJ2000, DEJ2000, GLON, GLAT, Signif_Avg, Flux_Density, Unc_Flux_Density, Flux1000, Unc_Flux1000, SpectrumType, Spectral_Index, Unc_Spectral_Index, Variability_Index, CLASS1, TEVCAT_FLAG, ASSOC_TEV, Flags, Size, Type FROM Catalog ' . $CLASSTYPE . $NAME;
        $results = $db->query($queryStatement);

        $data = array();

        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {

            // Get the ra and dec of each source
            $raSource = $row['RAJ2000'];
            $decSource = $row['DEJ2000'];

            // Find the distance to the user specified coordinates
            $distance = AngularDistance($raSource, $decSource, $raROI, $decROI);

            if ($distance < $radius) {
                $data[] = $row;
            }

            // $data[] = $row;


        }  

        echo json_encode($data);



    } 


    // Return detailed information on a limited number of sources to be displayed in the data table
    if ($typeOfRequest === 'Test') { 

        $keys = array_keys($_GET);

        foreach ($keys as &$key) {
            echo "<BR>";
            echo $key;
        }

    } 

?>  






