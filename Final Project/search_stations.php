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
                    if (preg_match("/[fF][mM]/", $inputString)){
                        array_push($listOfTokens, "FM");
                    }
                    else if (preg_match("/[Ff][Uu][Ss][Ii][Oo][Nn]/", $inputString)){
                        array_push($listOfTokens, "FUSION");
                    }
		    else if (preg_match("/[aA][mM]/", $inputString)){
                        array_push($listOfTokens, "AM");
                    }
                    else if (preg_match("/[Dd]-?[Ss][Tt][Aa][Rr]/", $inputString)){
                        array_push($listOfTokens, "DSTAR");
                    }
                    else if (preg_match("^[Dd][Mm][Rr]/[Mm][Aa][Rr][Cc]^", $inputString)){
                        array_push($listOfTokens, "DMR/MARC");
                    }
                    else if (preg_match("^[Dd][Mm][Rr]/[Bb][Mm]^", $inputString)){
                        array_push($listOfTokens, "DMR/BM");
                    }
                    
                    //match bands
                    if (preg_match("/14[4-8]./", $inputString)){
                        array_push($listOfTokens, "2M");
                    }
                    else if (preg_match("/22[0-5]./", $inputString)){
                        array_push($listOfTokens, "1.25M");
                    }
                    else if (preg_match("/4[2-5][0-9]./", $inputString)){
                        array_push($listOfTokens, "70CM");
                    }
                    else if (preg_match("/9[0-2][0-8]./", $inputString)){
                        array_push($listOfTokens, "33CM");
                    }
                    else if (preg_match("/1[2-3][0-9][0-9]./", $inputString)){
                        array_push($listOfTokens, "23CM");
                    }
                    else if (preg_match("/5[0-4].[0-9]*/", $inputString)){
                        array_push($listOfTokens, "6M");
                    }
                    else if (preg_match("/6[mM]/", $inputString)){
                        array_push($listOfTokens, "6M");
                    }
                    else if (preg_match("/2[mM]/", $inputString)){
                        array_push($listOfTokens, "2M");
                    }
                    else if (preg_match("/1.25[mM]/", $inputString)){
                        array_push($listOfTokens, "1.25M");
                    }
                    else if (preg_match("/70[c, C][mM]/", $inputString)){
                        array_push($listOfTokens, "70CM");
                    }
                    else if (preg_match("/33[c, C][mM]/", $inputString)){
                        array_push($listOfTokens, "33CM");
                    }
                    else if (preg_match("/23[c, C][mM]/", $inputString)){
                        array_push($listOfTokens, "23CM");
                    }

                    //match squelch
                    if(preg_match("/[0-9]*.[0-9] [Hh][Zz]/", $inputString)){
                        array_push($listOfTokens, "TONE_SQUELCH");
                    }
                    else if (preg_match("/[cC][cC][0-9]+/", $inputString)){
                        array_push($listOfTokens, "DIGITAL_SQUELCH");
                    }
                    else if (preg_match("/[tT][oO][nN][eE]/", $inputString)){
                        array_push($listOfTokens, "TONE_SQUELCH");
                    }
                }
                function printValue($doc){
                    $value = 'value';
                    $valueArray = explode(',', $doc->$value);
                    echo "<tr> ";
                    echo "<td> " . $valueArray[0] . " </td>";
                    echo "<td> " . $valueArray[1] . " </td>";
                    echo "<td> " . $valueArray[2] . " </td>";
                    echo "<td> " . $valueArray[3] . " </td>";
                    echo "<td> " . $valueArray[4] . " </td>";
                    echo "<td> " . $valueArray[5] . " </td>";
                    echo "</tr>";
                }

                class Doc{
                    public $value = "";
                    public $score = 0.0;
                }

                function docSort($a, $b){
                    $score = 'score';
                    if ($a->$score == $b->$score) return 0;
                    if ($a->$score < $b->$score) return 1;
                    return -1;
                }

                function generateCandidateList(&$tokenArray){
                    $newArray = array();
                    for ($i = 0; $i < count($tokenArray); ++$i){
                        $i_string = explode(' ',  $tokenArray[$i]);
                        for ($j = $i+1; $j < count ($tokenArray); ++$j){
                            $j_string = explode(' ', $tokenArray[$j]);
                            if ($j_string == " ") continue;
                            $intersect = array_intersect($i_string, $j_string);
                            if(count($i_string) - count($intersect) == 1){
                                print_r(array_combine($i_string, $j_string));
                                array_push($newArray, implode(" ", array_merge($i_string, $j_string)));
                            }
                        }
                    }
                    $tokenArray = $newArray;
                }


		$formdata = array(
				'location'=> ($_GET['location']),
				'station_type' => $_GET['station_type']
			);

		echo "<p>You are searching: " . $formdata['location'] . " </p>";
		$locations = explode(',', $formdata['location']);
                foreach ($locations as &$l){
                    $l = strtoupper(trim($l, " "));
                }
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
                $value = 'value';
                $score = 'score';

                $queryTokens = array();
                tokenize($queryTokens, $formdata['station_type']);
                
                print_r($formdata['station_type']);
                echo "<br />";
                print_r($queryTokens);
                echo "<br />";

                $docsArray = array();
                $itemTokensArray = array();

                foreach($pool as $item){
                    $itemTokens = array();
                    $newDoc = new Doc;
                    tokenize ($itemTokens, $item);


                    $newDoc->$value = $item;
                    $union = array_unique(array_merge($itemTokens, $queryTokens));
                    $intersect = array_intersect($itemTokens, $queryTokens);
                    $newDoc->$score = count($intersect)/count($union);

                    array_push($docsArray, $newDoc);

                    array_push($itemTokensArray, $itemTokens);
                }

                uasort($docsArray, "docSort");
                if (count($docsArray) < 10) $k = count($docsArray);
                else $k = 10;

                echo "<h3>Printing the top " . $k . " results: </h3>";

                echo "<table cellpadding=\"5\" cellspacing=\"5\" align=\"left\" width=\"100%\" border=\"1\">";
                    echo "<tr>";
                    echo "<td>City</td>";
                    echo "<td>Mode</td>";
                    echo "<td>Call Sign</td>";
                    echo "<td>Frequency</td>";
                    echo "<td>Offset</td>";
                    echo "<td>Squelch</td>";
                    echo "</tr>";
                    foreach ($docsArray as $d) {
                        $k--;
                        if ($k < 0) break;
                        printValue($d);
                    }
                echo "</table>";

                print_r($itemTokensArray);
                foreach ($itemTokensArray as &$a){
                    generateCandidateList($a);
                }
                echo "<br />";
                print_r($itemTokensArray);

                fclose( $inFile );

	?> 

</body>
</html>
