<?php
session_start();

//require_once('../modules/startsession.php');
require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/constants_config.php');
require_once('insert_student.php');
require_once('php/data_checks.php');
require_once('php/student_form_json.php');
require_once('php/gender_map.php');

global $servername, $dbname, $username, $password;

//Record start time to measure page render time later
$start = microtime(true);

//Connect to the database
$conn = dbconnect($servername, $dbname, $username, $password);

logger("EDIT STUDENT: Beginning of script");

//For each of the fields, create an array to be later encoded as JSON response onSubmit
//$sid_array = create_field_array('edit_sid');
//$firstname_array = create_field_array('edit_firstname');
//$lastname_array = create_field_array('edit_lastname');
//$address_array = create_field_array('edit_address');
//$major_array = create_field_array('edit_major');
//$gender_array = create_field_array('edit_gender');
//$comments_array = create_field_array('edit_comments');

//Get all the field values
$id = filter_var($_POST['row_id'], FILTER_SANITIZE_SPECIAL_CHARS);
$sid = filter_var($_POST['edit_sid'], FILTER_SANITIZE_SPECIAL_CHARS);
$firstname = filter_var($_POST['edit_firstname'], FILTER_SANITIZE_SPECIAL_CHARS);
$lastname = filter_var($_POST['edit_lastname'], FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_var($_POST['edit_address'], FILTER_SANITIZE_SPECIAL_CHARS);
$major = filter_var($_POST['edit_major'], FILTER_SANITIZE_SPECIAL_CHARS);
$gender = filter_var($_POST['edit_gender'], FILTER_SANITIZE_SPECIAL_CHARS);
$comments = filter_var($_POST['edit_comments'], FILTER_SANITIZE_SPECIAL_CHARS);

$gender_id = $gender_map[$gender];

logger("EDIT STUDENT: ".var_dump($_POST));

//Call update_records with field values fetched from $_POST
$status = update_record($conn, $id, $sid, $firstname, $lastname, $address, $major, $gender_id, $comments);

if($status == true) {
    logger("EDIT STUDENT: Inside success case");
    //$response_array = create_array_to_encode('SUCCESS', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $gender_array, $comments_array);
} else {
    logger("EDIT STUDENT: Inside fail case");
    //$response_array = create_array_to_encode('FAIL', $sid_array, $firstname_array, $lastname_array, $address_array, $major_array, $gender_array, $comments_array);
}

//$json_response = json_encode($response_array);
//echo $json_response;

//Set current page URL, JSON object in $_SESSION superglobal
$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
//$_SESSION['json'] = $json_response;

logger("SVARS: Referrer URI: ".$_SESSION['referer']." json: ".$_SESSION['json']);

//Redirect back to form page
$referer = $_SERVER['HTTP_REFERER'];
//header("Location: $referer");
exit();

/****************______Start of function definitions______*******************/
function update_record($conn, $id, $sid, $firstname, $lastname, $address, $major, $gender, $comments) {

    global $EDIT_SUCCESS_LOG, $EDIT_FAIL_LOG;

    try {
        $sql = $conn->prepare("UPDATE students SET sid='$sid', firstname='$firstname', lastname='$lastname', address='$address', major='$major', gender='$gender', comments='$comments' WHERE id='$id'");
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
