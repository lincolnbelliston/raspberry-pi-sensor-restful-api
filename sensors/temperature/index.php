<?php
header("Content-Type:application/json");

$units = "C";
$duration = 1;
$interval = 1;
$success = true;

if(!empty($_GET['units'])){
  $units = $_GET['units'];
}

if(!empty($_GET['duration'])){
  $duration = (int)$_GET['duration'];
}

if(!empty($_GET['interval'])){
  $interval = (int)$_GET['interval'];
}

$command = './temp '.$units;

$data = [];
for($i=0; $i<$duration; $i++)
{
  $tstart = microtime(true);
  
  exec($command, $output);
  
  if(!empty ($output))
  {
      $data_row['time'] = microtime(true);
      $data_row['temp'] = $output[0];

      array_push($data,$data_row);
  
  }
  else
  {
    deliver_response(400, "fail", null);
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

  $json_response = json_encode($response);

  echo $json_response;
}
 
?>
