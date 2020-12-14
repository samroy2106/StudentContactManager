<?php
session_start();

require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/constants_config.php');
require_once('php/data_checks.php');
require_once('php/student_form_json.php');

global $servername, $dbname, $username, $password;
global $SID_REGEX;

$start = microtime(true);

$conn = dbconnect($servername, $dbname, $username, $password);

//Get SID to delete from POST handler. Set on trash button click.
$delete_id = filter_var($_POST['operation_id'], FILTER_SANITIZE_SPECIAL_CHARS);

//Get status of deletion
$status = delete_student_record($conn, $delete_id);

if($status == true) {
    $student_record_delete_response = create_delete_student_status_array("SUCCESS");
} else {
    $student_record_delete_response = create_delete_student_status_array("FAIL");
}

$json_response = json_encode($student_record_delete_response);
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
function delete_student_record($conn, $operation_id) {

    global $DELETE_SUCCESS_LOG, $DELETE_FAIL_LOG;

    try {
        $sql = $conn->prepare("DELETE FROM students where id = '$operation_id'");
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

function create_delete_student_status_array($delete_status) {

    global $DELETE_STATUS;

    $array_to_encode = array(
        "delete_status" => array("status" => $delete_status, "msg" => $DELETE_STATUS[$delete_status])
    );

    return $array_to_encode;
}