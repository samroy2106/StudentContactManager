<?php
session_start();

//require_once('../modules/startsession.php');
require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/constants_config.php');
require_once('php/data_checks.php');
require_once('php/student_form_json.php');

global $servername, $dbname, $username, $password;
$validity_counter = 0;

//Record start time to measure page render time later
$start = microtime(true);

//Connect to the database
$conn = dbconnect($servername, $dbname, $username, $password);

//For each of the fields, create an array to be later encoded as JSON response onSubmit
$sid_array = create_field_array('sid');
$firstname_array = create_field_array('firstname');
$lastname_array = create_field_array('lastname');
$address_array = create_field_array('address');
$major_array = create_field_array('major');
$gender_array = create_field_array('gender');
$comments_array = create_field_array('comments');

//Check if all fields are valid, if yes call update_records to do as the name suggests
//If the update fails due to db unavailability, set submit_status to FAIL, else set it to SUCCESS
if($validity_counter >= 6){
    //Get all the field values
    $sid = filter_var($_POST['sid'], FILTER_SANITIZE_SPECIAL_CHARS);
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
    $major = filter_var($_POST['major'], FILTER_SANITIZE_SPECIAL_CHARS);
    $gender_id = filter_var($_POST['gender'], FILTER_SANITIZE_SPECIAL_CHARS);
    $comments = filter_var($_POST['comments'], FILTER_SANITIZE_SPECIAL_CHARS);

    //Call update_records with field values fetched from $_POST
    $status = insert_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

    if($status == true) {
        $response_array = create_array_to_encode('SUCCESS', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $gender_array, $comments_array);
    } else {
        $response_array = create_array_to_encode('DB_FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $gender_array, $comments_array);
    }
} else {
    $response_array = create_array_to_encode('FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array,$gender_array, $comments_array);
}

$json_response = json_encode($response_array);
echo $json_response;

//Set current page URL, JSON object in $_SESSION superglobal
$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
$_SESSION['json'] = $json_response;

logger("SVARS: Referrer URI: ".$_SESSION['referer']." json: ".$_SESSION['json']);

//Redirect back to form page
$referer = $_SERVER['HTTP_REFERER'];
//header("Location: $referer");
exit();

/****************______Start of function definitions______*******************/
function insert_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments) {

    global $INSERT_SUCCESS_LOG, $INSERT_FAIL_LOG;

    try {
        $sql = $conn->prepare("INSERT INTO students(sid, firstname, lastname, address, major, gender_id, comments) VALUES ('$sid', '$firstname', '$lastname', '$address', '$major', '$gender_id', '$comments')");
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

function create_field_array($field) {

    global $SID_LENGTH, $MIN, $NAME_MAX, $ADDRESS_MAX, $MAJOR_MAX, $GENDER_MAX, $COMMENTS_MAX;

    /*
    switch ($field) {
        case "sid":
            $status_array = create_status_array("sid", $MIN, $SID_LENGTH);
            break;
        case "firstname":
            $status_array = create_status_array("firstname", $MIN, $NAME_MAX);
            break;
        case "lastname":
            $status_array = create_status_array("lastname", $MIN, $NAME_MAX);
            break;
        case "address":
            $status_array = create_status_array("address", $MIN, $ADDRESS_MAX);
            break;
        case "major":
            $status_array = create_status_array("major", $MIN, $MAJOR_MAX);
            break;
        case "gender":
            $status_array = create_status_array("gender", $MIN, $GENDER_MAX);
            break;
        case "comments":
            $status_array = create_status_array("comments", $MIN, $COMMENTS_MAX);
            break;
    }*/

    if($field === "sid" || $field === "edit_sid") {
        $status_array = create_status_array($field, $MIN, $SID_LENGTH);

    } else if ($field === "firstname" || $field === "edit_firstname") {
        $status_array = create_status_array($field, $MIN, $NAME_MAX);

    } else if ($field === "lastname" || $field === "edit_lastname") {
        $status_array = create_status_array($field, $MIN, $NAME_MAX);

    } else if ($field === "address" || $field === "edit_address") {
        $status_array = create_status_array($field, $MIN, $ADDRESS_MAX);

    } else if ($field === "major" || $field === "edit_major") {
        $status_array = create_status_array($field, $MIN, $MAJOR_MAX);

    } else if ($field === "gender" || $field === "edit_gender") {
        $status_array = create_status_array($field, $MIN, $GENDER_MAX);

    } else if ($field === "comments" || $field === "edit_comments") {
        $status_array = create_status_array($field, $MIN, $COMMENTS_MAX);

    }

    return $status_array;
}

function create_status_array($field, $min, $max) {

    logger("Entering status array creation");

    global $validity_counter;
    global $SID_REGEX;
    global $LENGTH_LOG, $STRING_LOG, $NOT_SET_LOG, $REGEX_LOG;

    if(isset($_POST[$field]) && !(empty($_POST[$field]))) {
        $field_value = filter_var($_POST[$field], FILTER_SANITIZE_SPECIAL_CHARS);

        if($field === "sid" || $field === "edit_sid") {
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
        } elseif($field === "gender" || $field === "edit_gender") {
            $status_array = create_field_status_array("VALID", $field);
            $validity_counter+= 1;
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
    } else {
        logger($NOT_SET_LOG);
        $status_array = create_field_status_array("ERR_NOT_SET", filter_var($_POST[$field], FILTER_SANITIZE_SPECIAL_CHARS));
    }

    return $status_array;
}

//Calculate render time
$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);