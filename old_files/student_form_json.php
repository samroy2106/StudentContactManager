<?php
require_once('constants_config.php');

function create_field_status_array($response_type, $field) {

    global $RESPONSE_TYPE;

    $status_array = array("status" => $response_type, "msg" => $RESPONSE_TYPE[$field][$response_type]);
    return $status_array;
}

function create_array_to_encode($submit_status, $sid, $firstname, $lastname, $address, $major, $gender, $comments) {

    global $SUBMIT_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $SUBMIT_STATUS[$submit_status]),
        "sid" => $sid,
        "firstname" => $firstname,
        "lastname" => $lastname,
        "address" => $address,
        "major" => $major,
        "gender" => $gender,
        "comments" => $comments
    );

    return $array_to_encode;
}
