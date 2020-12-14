<?php
require_once('php/startsession.php');
require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/constants_config.php');
require_once('php/data_checks.php');
require_once('admin_panel/student_action_log/log_actions.php');
require_once('php/csrf_check.php');

global $servername, $dbname, $username, $password;

$sid = '';
$firstname = '';
$lastname = '';
$address = '';
$major = '';
$gender_id = null;
$comments = '';

$sid_array = [];
$firstname_array = [];
$lastname_array = [];
$address_array = [];
$major_array = [];
$comments_array = [];

//For add operation, only add if all fields (except comments) are populated
$validity_counter = 0;

//Record start time to measure page render time later
$start = microtime(true);

//Connect to the database
$conn = dbconnect($servername, $dbname, $username, $password);

//Button press on one of the table operations
if(check_csrf($_POST['token']) && isset($_POST['action'])) {

    logger("Inside post action handler");

    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $valid = validateFields();
            addStudent($valid, $conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
            break;
        case 'edit':
            $valid = validateFields();
            $operation_id = $_POST['row_id'];
            editStudent($valid, $conn, $operation_id, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
            break;
        case 'clone':
            $valid = validateFields();
            cloneStudent($valid, $conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array);
            break;
        case 'delete':
            $operation_id = $_POST['row_id'];
            deleteStudent($conn, $operation_id);
            break;
    }
} else {
    // Log CSRF warning
    logger("CSRF check failed.");
    //add_log_entry("csrf_student_action", "Warning");
}

function validateFields() {

    global $sid, $firstname, $lastname, $address, $major, $gender_id, $comments;
    global $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array;
    global $validity_counter;

    if(isset($_POST['sid']) && !(empty($_POST['sid']))) {
        $sid = filter_var($_POST['sid'], FILTER_SANITIZE_SPECIAL_CHARS);
        $sid_array = create_field_array('sid', $sid);
    } else {
        $sid = "";
    }

    if(isset($_POST['firstname']) && !(empty($_POST['firstname']))) {
        $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_SPECIAL_CHARS);
        $firstname_array = create_field_array('firstname', $firstname);
    } else {
        $firstname = "";
    }

    if(isset($_POST['lastname']) && !(empty($_POST['lastname']))) {
        $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_SPECIAL_CHARS);
        $lastname_array = create_field_array('lastname', $lastname);
    } else {
        $lastname = "";
    }

    if(isset($_POST['address']) && !(empty($_POST['address']))) {
        $address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        $address_array = create_field_array('address', $address);
    } else {
        $address = "";
    }

    if(isset($_POST['major']) && !(empty($_POST['major']))) {
        $major = filter_var($_POST['major'], FILTER_SANITIZE_SPECIAL_CHARS);
        $major_array = create_field_array('major', $major);
    } else {
        $major = "";
    }

    if(isset($_POST['gender']) && !(empty($_POST['gender']))) {

        //logger("GENDER: ".$_POST['gender']);

        $gender_id = filter_var($_POST['gender'], FILTER_SANITIZE_NUMBER_INT);
    }

    if(isset($_POST['comments']) && !(empty($_POST['comments']))) {
        $comments = filter_var($_POST['comments'], FILTER_SANITIZE_SPECIAL_CHARS);
        $comments_array = create_field_array('comments', $comments);
    } else {
        $comments = "";
    }

    if($validity_counter == 6) {
        return true;
    } else {
        return false;
    }
}

/***************Build final response*****************/

function addStudent($valid, $conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array) {

    logger("FORM: Inside add student case.");

    if($valid) {

        $status = insert_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

        if($status == true) {
            $response_array = create_insert_response('SUCCESS', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
            add_log_entry("add_student", $sid);
        } else {
            $response_array = create_insert_response('DB_FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
        }
    } else {
        $response_array = create_insert_response('FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

function editStudent($valid, $conn, $operation_id, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array) {

    logger("FORM: Inside edit student case.");

    if($valid){
        $status = update_record($conn, $operation_id, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

        if($status == true) {
            $response_array = create_update_response('SUCCESS', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
            add_log_entry("edit_student", $sid);
        } else {
            $response_array = create_update_response('FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
        }
    } else {
        $response_array = create_update_response('FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array);
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

function cloneStudent($valid, $conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments, $sid_array) {

    logger("FORM: Inside clone student case.");

    if($valid) {
        $status = clone_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

        if($status == true) {
            $response_array = create_clone_response('SUCCESS', $sid_array);
            add_log_entry("clone_student", $sid);
        } else {
            $response_array = create_clone_response('FAIL', $sid_array);
        }
    } else {
        $response_array = create_clone_response('FAIL', $sid_array);
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

function deleteStudent($conn, $operation_id) {

    logger("FORM: Inside delete student case.");

    $status = delete_record($conn, $operation_id);

    if($status == true) {
        $response_array = create_delete_response('SUCCESS');
        add_log_entry("delete_student", $operation_id);
    } else {
        $response_array = create_delete_response('FAIL');
    }

    $json_response = json_encode($response_array);
    echo $json_response;
}

/*****************Execute SQL statements**************/

function insert_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments) {

    //logger("COMMENTS INSERT: ".$comments);

    global $INSERT_SUCCESS_LOG, $INSERT_FAIL_LOG;

    try {
        $sql = $conn->prepare("INSERT INTO students(sid, firstname, lastname, address, major, gender_id, comments) VALUES ('$sid', '$firstname', '$lastname', '$address', '$major', $gender_id, '$comments')");
        $sql->execute();

        logger($INSERT_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($INSERT_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

function update_record($conn, $operation_id, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments) {

    //logger("COMMENTS UPDATE: ".$comments);

    global $EDIT_SUCCESS_LOG, $EDIT_FAIL_LOG;

    try {
        $sql = $conn->prepare("UPDATE students SET sid='$sid', firstname='$firstname', lastname='$lastname', address='$address', major='$major', gender_id=$gender_id, comments='$comments' WHERE id=$operation_id");
        $sql->execute();

        logger($EDIT_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($EDIT_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

function clone_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments) {

    //logger("COMMENTS CLONE: ".$comments);

    global $CLONE_SUCCESS_LOG, $CLONE_FAIL_LOG;

    try {
        $sql = $conn->prepare("INSERT INTO students(sid, firstname, lastname, address, major, gender_id, comments) VALUES ('$sid', '$firstname', '$lastname', '$address', '$major', $gender_id, '$comments')");
        $sql->execute();

        logger($CLONE_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($CLONE_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

function delete_record($conn, $operation_id) {

    global $DELETE_SUCCESS_LOG, $DELETE_FAIL_LOG;

    try {
        $sql = $conn->prepare("DELETE FROM students where id=$operation_id");
        $sql->execute();

        logger($DELETE_SUCCESS_LOG);
        $status = true;
    } catch (PDOException $e) {
        $status = false;
        logger($DELETE_FAIL_LOG." Exception: ".$e);
    }

    $conn = null;
    return $status;
}

/****************************Create response arrays******************************/

function create_insert_response($submit_status, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array) {

    global $ADD_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $ADD_STATUS[$submit_status]),
        "sid" => $sid_array,
        "firstname" => $firstname_array,
        "lastname" => $lastname_array,
        "address" => $address_array,
        "major" => $major_array,
        "comments" => $comments_array
    );

    return $array_to_encode;
}

function create_update_response($submit_status, $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $comments_array) {

    global $EDIT_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $EDIT_STATUS[$submit_status]),
        "sid" => $sid_array,
        "firstname" => $firstname_array,
        "lastname" => $lastname_array,
        "address" => $address_array,
        "major" => $major_array,
        "comments" => $comments_array
    );

    return $array_to_encode;
}

function create_clone_response($submit_status, $sid_array) {

    global $CLONE_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $CLONE_STATUS[$submit_status]),
        "sid" => $sid_array
    );

    return $array_to_encode;
}

function create_delete_response($submit_status) {

     global $DELETE_STATUS;

    $array_to_encode = array(
        "submit_status" => array("status" => $submit_status, "msg" => $DELETE_STATUS[$submit_status])
    );

    return $array_to_encode;
}

/******************Validation for all fields***********************/

function create_field_array($field, $field_value) {

    global $SID_LENGTH, $MIN, $NAME_MAX, $ADDRESS_MAX, $MAJOR_MAX, $COMMENTS_MAX;

    switch ($field) {
        case 'sid':
            $status_array = create_status_array($field, $field_value, $MIN, $SID_LENGTH);
            break;
        case 'firstname':
            $status_array = create_status_array($field, $field_value, $MIN, $NAME_MAX);
            break;
        case 'lastname':
            $status_array = create_status_array($field, $field_value, $MIN, $NAME_MAX);
            break;
        case 'address':
            $status_array = create_status_array($field, $field_value, $MIN, $ADDRESS_MAX);
            break;
        case 'major':
            $status_array = create_status_array($field, $field_value, $MIN, $MAJOR_MAX);
            break;
        case 'comments':
            $status_array = create_status_array($field, $field_value, $MIN, $COMMENTS_MAX);
            break;
    }

    return $status_array;
}

function create_status_array($field, $field_value, $min, $max) {

    global $validity_counter;
    global $SID_REGEX;
    global $LENGTH_LOG, $STRING_LOG, $REGEX_LOG;

    if($field === 'sid') {

        if(strlen($field_value) != $max) {
            logger($LENGTH_LOG);
            $status_array = create_field_status_array("ERR_LENGTH", $field);
        } elseif (value_is_string($field_value) == false) {
            logger($STRING_LOG);
            $status_array = create_field_status_array("ERR_NOT_STRING", $field);
        } elseif (check_regex($field_value, $SID_REGEX) == false) {
            logger($REGEX_LOG);
            $status_array = create_field_status_array("ERR_SID_FORMAT", $field);
        }else{
            $status_array = create_field_status_array("VALID", $field);
            $validity_counter+=1;
        }
    } else {

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
    }

    return $status_array;
}

function create_field_status_array($response_type, $field) {

    global $RESPONSE_TYPE;

    $status_array = array("status" => $response_type, "msg" => $RESPONSE_TYPE[$field][$response_type]);

    return $status_array;
}