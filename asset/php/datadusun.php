<?php 

  $dataSpreadsheetUrl1 = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRpS-Z15J6JqjqyepoKENVStfvUKXcIP7FsClL2j4_BxHu1mENQRNPeZ2VYcnVvGjOh8PYuCDANcukb/pub?gid=540460534&single=true&output=csv";
  
  $rowCount = 0;
  $features = array();
  $error = FALSE;
  $titikrumah = array();
  // attempt to set the socket timeout, if it fails then echo an error
  if ( ! ini_set('default_socket_timeout', 15))
  {
    $titikrumah = array('error' => 'Unable to Change PHP Socket Timeout');
    $error = TRUE;
  } // end if, set socket timeout

  // if the opening the CSV file handler does not fail
  if ( !$error && (($dataHandle = fopen($dataSpreadsheetUrl1, "r")) !== FALSE) )
  {
    // while CSV has data, read up to 10000 rows
    while (($csvRow = fgetcsv($dataHandle, 10000, ",")) !== FALSE)
    {
      $rowCount++;
      if ($rowCount == 1) { continue; } // skip the first/header row of the CSV

      $titikrumah[] = array(
        'type' => 'Feature',
        'properties' => array(
          'rumah' => $csvRow[2],
          'RT' => $csvRow[3],
          'kode' => $csvRow[4],
        ),
        'geometry' => array(
          'type' => 'Point',
          'coordinates' => array(
            $csvRow[0],$csvRow[1], '0.0'
          ),
        )
      );
    } // end while, loop through CSV data

    fclose($dataHandle); // close the CSV file handler
    
  }  // end if , read file handler opened

  // else, file didn't open for reading
  else
  {
    $titikrumah = array('error' => 'Problem Reading Google CSV');
  };  // end else, file open fail

  $new_titikdusun = array(
    'type' => 'FeatureCollection',
    'name'=> 'Data_Dusun',
    'crs' => [ 
      'type'=>'name', 
      'properties'=> [
        'name'=>   'urn:ogc:def:crs:OGC:1.3:CRS84' ]
      ],
    'features'=> $titikrumah
  );

  $json_datatitik = json_encode($new_titikdusun, JSON_NUMERIC_CHECK); 
  


?>