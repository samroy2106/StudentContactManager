<?php
require_once('../../php/startsession.php');
require_once('../../../config.php');
require_once('../../php/dbconnect.php');
require_once('../../php/log.php');
require_once('../../php/constants_config.php');
require_once('../../php/data_checks.php');
require_once('../student_action_log/log_actions.php');
require_once('../../php/csrf_check.php');

global $servername, $dbname, $username, $password;

$gendername = '';
$description = '';

$gendername_array = [];
$description_array = [];

//For add operation, only add if gendername is populated ..... Used to track required fields
$validity_counter = 0;

//Record start time to measure page render time later
$start = microtime(true);

//Connect to the database
$conn = dbconnect($servername, $dbname, $username, $password);

//Button press on one of the table operations
if(check_csrf($_POST['token']) && isset($_POST['action'])) {

    logger("GENDER: Inside post action handler");

    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $valid = validateFields();
            addGender($valid, $conn, $gendername, $description, $gendername_array, $description_array);
            break;
        case 'edit':
            $valid = validateFields();
            $operation_id = $_POST['row_id'];
            editGender($valid, $conn, $operation_id, $gendername, $description, $gendername_array, $description_array);
            break;
        case 'delete':
            $operation_id = $_POST['row_id'];
            deleteGender($conn, $operation_id);
            break;
    }
} else {
    //Log CSRF warning
    logger("CSRF check failed.");
    //add_log_entry("csrf_gender_action", "Warning");
}

function validateFields() {

    global $gendername, $description;
    global $gendername_array, $description_array;
    global $validity_counter;

    if(isset($_POST['gendername']) && !(empty($_POST['gendername']))) {
        $gendername = filter_var($_POST['gendername'], FILTER_SANITIZE_SPECIAL_CHARS);
        $gendername_array = create_field_array('gendername', $gendername);
    } else {
        $gendername = "";
    }

    if(isset($_POST['description']) && !(empty($_POST['description']))) {
        $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $description_array = create_field_array('description', $description);
    } else {
        $description = "";
    }

    if($validity_counter >= 1) {
        return true;
    } else {
        return false;
    }
}

/***************Build final response*****************/

function addGender($valid, $conn, $gendername, $description, $gendername_array, $description_array) {

    logger("FORM: Inside add gender case.");

    if($valid) {

        $status = insert_record($conn, $gendername, $description);

        if($status == true) {
            $response_array = create_insert_response('SUCCESS', $gendername_array, $description_array);
            add_log_entry("add_gender", $gendername);
        } else {
            $response_array = create_insert_response('DB_FAIL', $gendername_array, $description_array);
        }
    } else {
        $response_array = create_insert_response('FAIL', $gendername_array, $description_array);
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

function editGender($valid, $conn, $operation_id, $gendername, $description, $gendername_array, $description_array) {

    logger("FORM: Inside edit gender case.");

    if($valid){
        $status = update_record($conn, $operation_id, $gendername, $description);

        if($status == true) {
            $response_array = create_update_response('SUCCESS', $gendername_array, $description_array);
            add_log_entry("edit_gender", $gendername);
        } else {
            $response_array = create_update_response('FAIL', $gendername_array, $description_array);
        }
    } else {
        $response_array = create_update_response('FAIL', $gendername_array, $description_array);
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

function deleteGender($conn, $operation_id) {

    logger("FORM: Inside delete gender case.");

    $status = delete_record($conn, $operation_id);

    if($status == true) {
        $response_array = create_delete_response('SUCCESS');
        add_log_entry("delete_gender", $operation_id);
    } else {
        $response_array = create_delete_response('FAIL');
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

/*****************Execute SQL statements**************/

function insert_record($conn, $gendername, $description) {

    global $GENDER_INSERT_SUCCESS_LOG, $GENDER_INSERT_FAIL_LOG;

    try {
        $sql = $conn->prepare("INSERT INTO gender(name, description) VALUES ('$gendername', '$description')");
        $sql->execute();

        logger($GENDER_INSERT_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($GENDER_INSERT_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

function update_record($conn, $operation_id, $gendername, $description) {

    global $GENDER_EDIT_SUCCESS_LOG, $GENDER_EDIT_FAIL_LOG;

    try {
        $sql = $conn->prepare("UPDATE gender SET name='$gendername', description='$description' WHERE id=$operation_id");
        $sql->execute();

        logger($GENDER_EDIT_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($GENDER_EDIT_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

function delete_record($conn, $operation_id) {

    global $GENDER_DELETE_SUCCESS_LOG, $GENDER_DELETE_FAIL_LOG;

    try {
        $sql = $conn->prepare("DELETE FROM gender where id=$operation_id");
        $sql->execute();

        logger($GENDER_DELETE_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($GENDER_DELETE_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

/****************************Create response arrays******************************/

function create_insert_response($submit_status, $gendername_array, $description_array) {

    global $GENDER_ADD_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $GENDER_ADD_STATUS[$submit_status]),
        "gendername" => $gendername_array,
        "description" => $description_array
    );

    return $array_to_encode;
}

function create_update_response($submit_status, $gendername_array, $description_array) {

    global $GENDER_EDIT_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $GENDER_EDIT_STATUS[$submit_status]),
        "gendername" => $gendername_array,
        "description" => $description_array
    );

    return $array_to_encode;
}

function create_delete_response($submit_status) {

    global $GENDER_DELETE_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $GENDER_DELETE_STATUS[$submit_status])
    );

    return $array_to_encode;
}

/******************Validation for all fields***********************/

function create_field_array($field, $field_value) {

    global $MIN, $GENDER_MAX, $GENDER_DESCRIPTION_MAX;

    switch ($field) {
        case 'gendername':
            $status_array = create_status_array($field, $field_value, $MIN, $GENDER_MAX);
            break;
        case 'description':
            $status_array = create_status_array($field, $field_value, $MIN, $GENDER_DESCRIPTION_MAX);
            break;
    }

    return $status_array;
}

function create_status_array($field, $field_value, $min, $max) {

    global $validity_counter;
    global $LENGTH_LOG, $STRING_LOG;

    if(length_in_bounds($field_value, $min, $max) == false) {
        logger($LENGTH_LOG);
        $status_array = create_field_status_array("ERR_LENGTH", $field);
    } elseif (value_is_string($field_value) == false) {
        logger($STRING_LOG);
        $status_array = create_field_status_array("ERR_NOT_STRING", $field);
    } else{
        $status_array = create_field_status_array("VALID", $field);
        $validity_counter+=1;
    }

    return $status_array;
}

function create_field_status_array($response_type, $field) {

    global $GENDER_RESPONSE_TYPE;

    $status_array = array("status" => $response_type, "msg" => $GENDER_RESPONSE_TYPE[$field][$response_type]);

    return $status_array;
}