<?php

$prefix = "S0";
$sid_file = fopen("sid.txt", "w") or die("Unable to open sid.txt.");

for($i = 0; $i < 20000; $i++) {
    $id = mt_rand(10000000, 99999999);
    $id_string = (string)$id;
    $sid = $prefix.$id_string.PHP_EOL;

    //Write to file sid.txt
    fwrite($sid_file, $sid);
}

fclose($sid_file);
