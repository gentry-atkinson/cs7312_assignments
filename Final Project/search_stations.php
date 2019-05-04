<!DOCTYPE php>
<html lang="en-US">
<head>
<meta charset="utf-8"/>
<title>Repeater Results</title>
</head>
<body style="background-color: #E9E9EF">

	<?php
                function tokenize(&$listOfTokens, $inputString){
                    //match modes
                    if (preg_match("/[f, F][m, M]/", $inputString)){
                        array_push($listOfTokens, "FM");
                    }
                    else if (preg_match("/[fusion, FUSION, Fusion]/", $inputString)){
                        array_push($listOfTokens, "FUSION");
                    }
                    else if (preg_match("/[DSTAR, dstar, Dstar]/", $inputString)){
                        array_push($listOfTokens, "DSTAR");
                    }
                    else if (preg_match("^[dmr, DMR, Dmr]/[Marc, marc, MARC]^", $inputString)){
                        array_push($listOfTokens, "DMR/MARC");
                    }
                    else if (preg_match("^[dmr, DMR, Dmr]/[bm, Bm, BM]^", $inputString)){
                        array_push($listOfTokens, "DMR/BM");
                    }
                    
                    //match bands
                    if (preg_match("/5[0-4]./", $inputString)){
                        array_push($listOfTokens, "6M");
                    }
                    else if (preg_match("/14[4-8]./", $inputString)){
                        array_push($listOfTokens, "2M");
                    }
                    else if (preg_match("/22[0-5]./", $inputString)){
                        array_push($listOfTokens, "1.25M");
                    }
                    else if (preg_match("/4[2-5]0./", $inputString)){
                        array_push($listOfTokens, "70CM");
                    }
                    else if (preg_match("/9[0-2][0-8]./", $inputString)){
                        array_push($listOfTokens, "33CM");
                    }
                    else if (preg_match("/1[2-3][0-9][0-9]./", $inputString)){
                        array_push($listOfTokens, "23CM");
                    }
                    else if (preg_match("/6[m, M]/", $inputString)){
                        array_push($listOfTokens, "6M");
                    }
                    else if (preg_match("/2[m, M]/", $inputString)){
                        array_push($listOfTokens, "2M");
                    }
                    else if (preg_match("/1.25[m, M]/", $inputString)){
                        array_push($listOfTokens, "1.25M");
                    }
                    else if (preg_match("/70[c, C][m, M]/", $inputString)){
                        array_push($listOfTokens, "70CM");
                    }
                    else if (preg_match("/33[c, C][m, M]/", $inputString)){
                        array_push($listOfTokens, "33CM");
                    }
                    else if (preg_match("/23[c, C][m, M]/", $inputString)){
                        array_push($listOfTokens, "23CM");
                    }

                    //match squelch
                    if(preg_match("/[0-9]*.[0-9] [H, h][Z, z]/", $inputString)){
                        array_push($listOfTokens, "TONE_SQUELCH");
                    }
                    else if (preg_match("/[c, C][c, C][0-9]+/", $inputString)){
                        array_push($listOfTokens, "DIGITAL_SQUELCH");
                    }
                }


		$formdata = array(
				'location'=> ($_GET['location']),
				'station_type' => $_GET['station_type']
			);

		echo "<p>You are searching: " . $formdata['location'] . " </p>";
		$locations = explode(',', $formdata['location']);
                print_r($locations);
		
		$filename = "TEXAS_REPEATERS.csv";
		$inFile = fopen( $filename, "r");
		$pool = array();
		
                //generate pool of repeaters
		while(! feof($inFile)) {
			$candidate = fgets($inFile);
			$candidateLocation = explode(',', $candidate)[0];
			if (in_array($candidateLocation, $locations)){
                            array_push($pool, $candidate);
                        }
		}
                print_r($pool);
                echo "<br />";

                $queryTokens = array();
                tokenize($queryTokens, $formdata['station_type']);
                
                print_r($formdata['station_type']);
                echo "<br />";
                print_r($queryTokens);
                echo "<br />";
                $itemTokens = array();

                foreach($pool as $item){
                    $toPrint = true;
                    tokenize ($itemTokens, $item);
                    foreach ($queryTokens as $thisQueryToken){
                        if (! in_array($thisQueryToken, $itemTokens)){
                            $toPrint = false;
                        }
                    }
                    if($toPrint){
                        print_r($item);
                        echo "<br />";
                    }
                    $itemTokens = array();
                }

		fclose( $inFile );

	?> 

</body>
</html>