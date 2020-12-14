<?php

$gender_map = array(
    "Male" => 1,
    "Female" => 3,
    "Trans Male" => 5,
    "Trans Female" => 7,
    "Non-binary" => 9,
    "Pangender" => 11,
    "Two-spirit" => 13,
    "Gender Fluid" => 15,
    "Genderqueer" => 17,
    "Other" => 19
);

echo(json_encode($gender_map));