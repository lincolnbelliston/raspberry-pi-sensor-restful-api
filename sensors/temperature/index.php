<?php


header("Content-Type:application/json");
$units = "C";
$duration = '';


if(!empty($_GET['units'])){
  $units = $_GET['units'];
}

if(!empty($_GET['duration'])){
  $duration = $_GET['duration'];
}



$command = './temp '.$units.' '. $duration;

exec($command, $output);


if(!empty ($output))
{
  $data = [];
  foreach($output as $value){
    $raw = explode("_",$value);
    $data_row['time'] = (int)$raw[0];
    $data_row['temp'] = $raw[1];

    array_push($data,$data_row);

  }
  deliver_response(200, "success", $data);

}
else
{
  deliver_response(200, "fail", null);
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
