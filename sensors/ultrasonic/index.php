<?php
header("Content-Type:application/json");

// default units
$units = "cm"; //centimeters

$duration = 1;
$interval = 1;
$success = true;

$error_message = 'Invalid request. See /sensors/readme for query string rules.';


$unitsArray = array(
  "cm" => 0,
  "in" => 1,
);
  

if(!empty($_GET['units'])){
  $unitstr = strtolower($_GET['units']);
  $units = $unitsArray[$unitstr];
  if(empty($units)){
    deliver_response(400, $error_message, NULL);
  }
}

if(!empty($_GET['duration'])){
  $duration = (int)$_GET['duration'];
}

if(!empty($_GET['interval'])){
  $interval = (int)$_GET['interval'];
}

$command = './ultrasonic '.$units;

$data = [];
for($i=0; $i<$duration; $i++)
{
  $tstart = microtime(true);
  
  exec($command, $output);
  
  if(!empty ($output))
  {
      $data_row['time'] = microtime(true);
      $data_row['distance'] = $output[0];

      array_push($data,$data_row);
  
  }
  else
  {
    deliver_response(400, "Error: the server did not return any data", null);
    $success = false;
    break;
  }

  $output = "";

  $tend = microtime(true);
  
  $sleep = ($interval - ($tend - $tstart)) * 1000000 ;
  usleep($sleep);
}

if($success)
{
  deliver_response(200, "success", $data);
}

function deliver_response($status, $status_message, $data)
{
  header("HTTP/1.1 $status $status_message");

  $response['status'] = $status;
  $response['status_message'] = $status_message;
  $response['data'] = $data;

  $json_response = json_encode($response,  JSON_UNESCAPED_SLASHES);
          
  echo $json_response;
}
?>
