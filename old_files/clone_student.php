<?php
session_start();

require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/constants_config.php');
require_once('php/data_checks.php');
require_once('php/student_form_json.php');
require_once('php/gender_map.php');

global $servername, $dbname, $username, $password;
global $SID_REGEX;

$start = microtime(true);

$conn = dbconnect($servername, $dbname, $username, $password);

logger("CLONE STUDENT: ".var_dump($_POST));

//Get data to clone from POST handler. Set on trash button click.
$sid = filter_var($_POST['sid'], FILTER_SANITIZE_SPECIAL_CHARS);
$firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_SPECIAL_CHARS);
$lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
$major = filter_var($_POST['major'], FILTER_SANITIZE_SPECIAL_CHARS);
$gender = filter_var($_POST['gender'], FILTER_SANITIZE_SPECIAL_CHARS);
$comments = filter_var($_POST['comments'], FILTER_SANITIZE_SPECIAL_CHARS);

$gender_id = $gender_map[$gender];
//logger("GENDER: ".$gender.", ".$gender_id);

//Get status of cloning
$status = clone_student_record($conn, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

if($status == true) {
    $student_record_clone_response = create_clone_student_status_array("SUCCESS");
} else {
    $student_record_clone_response = create_clone_student_status_array("FAIL");
}

$json_response = json_encode($student_record_clone_response);
echo $json_response;

//Set current page URL, JSON object in $_SESSION superglobal
$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
$_SESSION['json'] = $json_response;

logger("S_TABLE: Referrer URI: ".$_SESSION['referer']." json: ".$_SESSION['json']);

//Redirect back to form page
$referer = $_SERVER['HTTP_REFERER'];
//header("Location: $referer");
exit();

//echo $json_response;

/*************___Function Definitions___***************/
function clone_student_record($conn, $sid, $firstname, $lastname, $address, $major, $gender, $comments) {

    global $CLONE_SUCCESS_LOG, $CLONE_FAIL_LOG;

    try {
        $sql = $conn->prepare("INSERT INTO students(sid, firstname, lastname, address, major, gender_id, comments) VALUES ('$sid', '$firstname', '$lastname', '$address', '$major', '$gender', '$comments')");
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

function create_clone_student_status_array($clone_status) {

    global $CLONE_STATUS;

    $array_to_encode = array(
        "clone_status" => array("status" => $clone_status, "msg" => $CLONE_STATUS[$clone_status])
    );

    return $array_to_encode;
}