<?php
require_once('log.php');

function length_in_bounds($string, $min, $max) {

	$length = strlen($string);

	if(($length > $min) && ($length < $max)) {
	    return true;
    } else {
	    return false;
    }
}

function value_is_string($string) {

    if(is_string($string)) {
        return true;
    } else {
        return false;
    }
}

function check_regex($string, $regex){

    preg_match($regex, $string, $matches, PREG_OFFSET_CAPTURE);

    //logger("Matches: ".$matches);

    if(empty($matches)) {
        return false;
    } else {
        return true;
    }
}
