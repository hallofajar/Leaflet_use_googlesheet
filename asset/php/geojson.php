<?php

  include('datadusun.php');

  $dataSpreadsheetUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vRpS-Z15J6JqjqyepoKENVStfvUKXcIP7FsClL2j4_BxHu1mENQRNPeZ2VYcnVvGjOh8PYuCDANcukb/pub?gid=1227738134&single=true&output=csv";

  $rowCount = 0;
  $features = array();
  $error = FALSE;
  $output = array();

  // attempt to set the socket timeout, if it fails then echo an error
  if ( ! ini_set('default_socket_timeout', 15))
  {
    $output = array('error' => 'Unable to Change PHP Socket Timeout');
    $error = TRUE;
  } // end if, set socket timeout

  // if the opening the CSV file handler does not fail
  if ( !$error && (($dataHandle = fopen($dataSpreadsheetUrl, "r")) !== FALSE) )
  {
    // while CSV has data, read up to 10000 rows
    while (($csvRow = fgetcsv($dataHandle, 10000, ",")) !== FALSE)
    {
      $rowCount++;
      if ($rowCount == 1) { continue; } // skip the first/header row of the CSV

      $output[] = array(
        'features' => array(
          'kode' => $csvRow[0],
          'kk' => $csvRow[1],
          'Keluarga' => $csvRow[2],
          'kelamin' => $csvRow[3],
          'tgl_lahir' => $csvRow[4],
          'status' => $csvRow[5],
        )
      );
    } // end while, loop through CSV data

    fclose($dataHandle); // close the CSV file handler
    
  }  // end if , read file handler opened

  // else, file didn't open for reading
  else
  {
    $output = array('error' => 'Problem Reading Google CSV');
  }  // end else, file open fail

  //print_r($new_titikdusun);

//   //Read geojson file
//  $geojsonAdmin = file_get_contents("../geojson/Data_Dusun.geojson");
  $polygonAdmin = json_decode($json_datatitik, TRUE);


  foreach ($polygonAdmin['features'] as $key => $first_value) {
    foreach ($output as $second_value) {
      if($first_value['properties']['kode']==$second_value['features']['kode']){
        $polygonAdmin['features'][$key]['properties']['anggota'][] = [
          "nama" => $second_value['features']['Keluarga'],
          "status" => $second_value['features']['status'],
          "tgl_lhr" => $second_value['features']['tgl_lahir'],
          "kelamin" => $second_value['features']['kelamin']
        ];

      } else {}
    } 
}


  $combined_output = json_encode($polygonAdmin, JSON_NUMERIC_CHECK);  
  



	header("Access-Control-Allow-Origin: *");
  // header('Cache-Control: no-cache, must-revalidate');
	header('Content-Type: application/json');
  echo $combined_output;
?>