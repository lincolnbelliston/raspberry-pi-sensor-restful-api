<?php
header("Content-type":"application/json");
if(!empty($_GET['units'])){
  $units = $_GET['units'];
}

if(!empty($_GET['duration'])){
  $duration = $_GET['duration'];
}

$command = './temp $units $duration'
exec($command, $output);

foreach($output as $value){
  $data = explode(".",$value);
  $time = $value[0];
  $temp = $value[1];
}
 ?>
