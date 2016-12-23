<?php
header("Content-Type:application/json");

// default units
$tunits = "C"; // Celsius
$punits = 0; // Pascals
$aunits = 0; // meters

$duration = 1;
$interval = 1;
$success = true;

$error_message = 'Invalid request. See /sensors/readme for query string rules.';

$punitsArray = array(
  "pa" => 0,
  "atm" => 1,
  "psi" => 2,
);

$aunitsArray = array(
  "m" => 0,
  "ft" => 1,
);
  

if(!empty($_GET['tunits'])){
  $tunits = strtolower($_GET['tunits']);
}

if(!empty($_GET['punits']))
{
  $punitstr = strtolower($_GET['punits']);
  $punits = $punitsArray[$punitstr];
  if(empty($punits))
  {
    deliver_response(400, $error_message, NULL);
  }
}

if(!empty($_GET['aunits'])){
  $aunitstr = strtolower($_GET['aunits']);
  $aunits = $aunitsArray[$aunitstr];
  if(empty($aunits)){
    deliver_response(400, $error_message, NULL);
  }
}

if(!empty($_GET['duration'])){
  $duration = (int)$_GET['duration'];
}

if(!empty($_GET['interval'])){
  $interval = (int)$_GET['interval'];
}

$command = './barometer '.$tunits.' '.$punits.' '.$aunits;

$data = [];
for($i=0; $i<$duration; $i++)
{
  $tstart = microtime(true);
  
  exec($command, $output);
  
  if(!empty ($output))
  {
      $raw = explode("_",$output[0]);
      $data_row['time'] = microtime(true);
      $data_row['temperature'] = $raw[0];
      $data_row['pressure'] = $raw[1];
      $data_row['elevation'] = $raw[2];

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
