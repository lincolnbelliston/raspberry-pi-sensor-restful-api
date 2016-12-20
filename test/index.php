<?php


$command = './a.out';

exec($command, $output);

echo $output[0];

?>
