<?php
session_start();

//require_once('../modules/startsession.php');
require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('php/colors.php');
require_once('php/get_fields.php');

global $SUCCESS_COLOR, $ERROR_COLOR;

$start = microtime(true);

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<title>Insert Student Form</title>
<script src='https://kit.fontawesome.com/8b75e01689.js' crossorigin='anonymous'></script>
<link rel='stylesheet' href='//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
<script src='https://code.jquery.com/jquery-1.12.4.js'></script>
<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
<script src='js/student_form.js'></script>
</head>\n<body>\n";

$form_header = "<form id = 'nameform' name='nameform' method='post' action='insert_student.php'>\n";
$sid_field = create_HTML_form_text_field("SID", 'sid',"SID", 'sid', 'sid_icon', "Must begin with S0 and be exactly 10 characters");
$fname_field = create_HTML_form_text_field("First Name", 'firstname', "First Name", 'firstname', 'fname_icon', "Must be 2-20 characters");
$lname_field = create_HTML_form_text_field("Last Name", 'lastname', "Last Name", 'lastname', 'lname_icon', "Must be 2-20 characters");
$addr_field = create_HTML_form_text_field("Address", 'address', "Address", 'address', 'addr_icon', "Must be 2-50 characters");
$major_field = create_HTML_form_text_field("Major", 'major', "Major", 'major', 'major_icon', "Must be 2-20 characters");
$gender_field = create_HTML_form_select_field('Gender', "gender", 'gender', 'gender_icon', "Please select one that applies");
$comments_field = create_HTML_form_text_field("Comments", 'comments', "Comments", 'comments', 'comments_icon', "Must be 2-250 characters");
$sumbit_button = "<br><input type='submit'>\n</form>\n";

$form = $form_header.$sid_field.$fname_field.$lname_field.$addr_field.$major_field.$gender_field.$comments_field.$sumbit_button;
$html_footer = "</body>\n</html>";

$sdump = implode("|", $_SESSION);
logger("Student Form: Session vardump: $sdump");

if(isset($_SESSION['referer'])) {

    //echo "Referer value set in update_student: ".$_SESSION['referer'];
    //echo "Http referer value to compare to: ".$_SERVER['HTTP_REFERER'];

    if($_SESSION['referer'] === $_SERVER['HTTP_REFERER']) {
        $json = $_SESSION['json'];

        //echo "JSON retrieved from SESSION: ".$json;

        $php_object = json_decode($json);

        //$pdump = (string)$php_object;
        //logger("Student Form: PHP Object vardump: $pdump");

        $submit_status = $php_object->submit_status;

        //If status is "SUCCESS", display one success message in black
        if($submit_status->status === "SUCCESS") {
            //logger("Student Form: Inside success case");
            $main_msg = set_main_msg($submit_status, $SUCCESS_COLOR);
        } else {
            //logger("Student Form: Inside error case");

            //Add messages for each individual field
            $sid_field = set_field_HTML($php_object, 'sid', $sid_field);
            $fname_field = set_field_HTML($php_object, 'firstname', $fname_field);
            $lname_field = set_field_HTML($php_object, 'lastname', $lname_field);
            $addr_field = set_field_HTML($php_object, 'address', $addr_field);
            $major_field = set_field_HTML($php_object, 'major', $major_field);
            $gender_field = set_field_HTML($php_object, 'gender', $gender_field);
            $comments_field = set_field_HTML($php_object, 'comments', $comments_field);

            $form = $form_header.$sid_field.$fname_field.$lname_field.$addr_field.$major_field.$gender_field.$comments_field.$sumbit_button;
            $main_msg = set_main_msg($submit_status, $ERROR_COLOR);
        }

        //logger("Student Form: Inside reference check");
        $html = $html_header.$form.$main_msg.$html_footer;

    } else {
        //logger("Student Form: Referer is not update_student");
        $html = $html_header.$form.$html_footer;
    }
} else {
    //logger("Student Form: Referer not set in session");
    $html = $html_header.$form.$html_footer;
}

echo $html;

/***********Function Definitions***********/
function set_main_msg($submit_status, $color){
    $main_msg = "<span style='color: $color'>".$submit_status->msg."</span>";
    return $main_msg;
}

function set_field($field_status){

    global $SUCCESS_COLOR, $ERROR_COLOR;

    if($field_status->status === "VALID") {
        $msg = "<span style='color: $SUCCESS_COLOR'>".$field_status->msg."</span>";
    } else {
        $msg = "<span style='color: $ERROR_COLOR'>".$field_status->msg."</span>";
    }

    return $msg;
}

function set_field_HTML($php_object, $field, $field_tag) {

    $status = $php_object->$field;
    $msg = set_field($status);
    $field_tag.= $msg;

    return $field_tag;
}

function create_HTML_form_text_field($tag, $id, $placeholder, $name, $icon_id, $icon_title) {

    $form_field = "<br><label for='$id'>$tag: </label>";

    $form_field.= "<input type='text' id='$id' placeholder='$placeholder' name='$name' onkeyup='validate_on_input(this.id)'>
    <i class='far fa-question-circle' style='color: blue' id='$icon_id' title='$icon_title'></i>\n";

    return $form_field;
}

function create_HTML_form_select_field($label, $id, $name, $icon_id, $icon_title) {

    $sql = get_all_genders();

    $select_field = "<br><label for='$id'>$label: </label>\n<select name='$name' id='$id'>";

    while($gender = $sql->fetch(PDO::FETCH_ASSOC)) {
        $select_field.= "<option value='".$gender['id']."'>".$gender['name']."</option>\n";
    }

    $select_field.= "</select><i class='far fa-question-circle' style='color: blue' id='".$icon_id."' title='".$icon_title."'></i>\n";

    return $select_field;
}

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);