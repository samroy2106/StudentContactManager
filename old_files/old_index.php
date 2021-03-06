<?php
session_start();

require_once '../config.php';
require_once 'php/log.php';
require_once 'php/get_fields.php';

$start = microtime(true);

$error_msg = null;

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>\n<title>Student Table</title>
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css'>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
<link rel='stylesheet' type='text/css' href='css/student_table.css'>
<script src='https://kit.fontawesome.com/8b75e01689.js' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/jquery-3.5.1.js'></script>
<script src='https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js'></script>
<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
<script src='js/student_table.js'></script>
<script src='js/student_form.js'></script>
</head>\n<body>\n";
$error_tag = "<div style='color:#F00; text-align:center;'><?= (!is_null($error_msg) ? $error_msg : ''); ?></div>";
$table_header = "<div id='student_table'>";
$table = "<table id='students' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n<th>Firstname</th>\n<th>Lastname</th>
<th>Address</th>\n<th>Major</th>\n<th>Gender</th>\n<th>Comments</th>\n<th>Operations</th></tr>\n</thead>\n";
$table_footer = "</table>\n";
$add_student_button = "<button id='add-student'><i class='far fa-address-card' title='Add Student'></i></button>\n</div>\n";
$html_footer = "</body>\n</html>";

$add_form = "<div id='add-dialog-form' title='Add Student'>\n<p class='validateTips'>All fields required except comments.</p>\n<form>\n<fieldset>";
$add_form.= create_form_field('sid', "SID: ", 'sid_icon', "Must begin with S0 and be exactly 10 characters");
$add_form.= create_form_field('firstname', "First Name: ", 'fname_icon', "Must be 2-20 characters");
$add_form.= create_form_field('lastname', "Last Name: ", 'lname_icon', "Must be 2-20 characters");
$add_form.= create_form_field('address', "Address: ", 'addr_icon', "Must be 2-50 characters");
$add_form.= create_form_field('major', "Major: ", 'major_icon', "Must be 2-20 characters");
$add_form.= create_form_select_field('gender', "Gender: ", 'gender_icon', "Please select one that applies");
$add_form.= create_form_field('comments', "Comments: ", 'comments_icon', "Must be 2-250 characters");
$add_form.= "<input type='submit' tabindex='-1' style='position:absolute; top:-500px'></fieldset></form>\n</div>\n";

$edit_form = "<div id='edit-dialog-form' title='Edit Student'>\n<p class='validateTips'>Change fields to update student record.</p>\n<form id='edit_form'>\n<fieldset>";
$edit_form.= create_form_field('edit_sid', "SID: ", 'sid_icon', "Must begin with S0 and be exactly 10 characters");
$edit_form.= create_form_field('edit_firstname', "First Name: ", 'fname_icon', "Must be 2-20 characters");
$edit_form.= create_form_field('edit_lastname', "Last Name: ", 'lname_icon', "Must be 2-20 characters");
$edit_form.= create_form_field('edit_address', "Address: ", 'addr_icon', "Must be 2-50 characters");
$edit_form.= create_form_field('edit_major', "Major: ", 'major_icon', "Must be 2-20 characters");
$edit_form.= create_form_select_field('edit_gender', "Gender: ", 'gender_icon', "Please select one that applies");
$edit_form.= create_form_field('edit_comments', "Comments: ", 'comments_icon', "Must be 2-250 characters");
$edit_form.= "<input type='submit' tabindex='-1' style='position:absolute; top:-500px'></fieldset>\n</form>\n</div>\n";

/*
$clone_form = "<div id='clone-dialog-form' title='Clone Student'>\n<p class='validateTips'>Enter SID to clone student details for.</p>\n<form>\n<fieldset>";
$clone_form.= create_form_field('clone_sid', "SID: ", 'sid_icon', "Must begin with S0 and be exactly 10 characters");
$clone_form.= "<input type='submit' tabindex='-1' style='position:relative; top:-500px'></fieldset>\n</form>\n</div>\n";
*/

$clone_dialog = "<div id='clone-dialog' title='Clone student record?'>\n<p>\n<span class='ui-icon ui-icon-alert' style='float:left; margin:12px 12px 20px 0;'></span>\nStudent record will be cloned and assigned to new SID. Proceed?\n</p>\n</div>\n";

$delete_dialog = "<div id='delete-dialog' title='Delete student record?'>\n<p>\n<span class='ui-icon ui-icon-alert' style='float:left; margin:12px 12px 20px 0;'></span>\nStudent record will be deleted from database. Are you sure?\n</p>\n</div>\n";

if(isset($_SESSION['referer'])) {

    if($_SESSION['referer'] === $_SERVER['HTTP_REFERER']) {
        $json = $_SESSION['json'];

        $php_object = json_decode($json);

        $submit_status = $php_object->submit_status;

        //If status is "FAIL", display error message
        if($submit_status->status === "FAIL") {
            //logger("Student Table: Inside error case");
            $error_msg = "Unable to delete student record.";
        }
    }
}

$html = $html_header.$error_tag.$add_form.$edit_form.$clone_dialog.$delete_dialog.$table_header.$table.$table_footer.$add_student_button.$html_footer;
echo $html;

//logger("Client: ". implode(" ", $_GET));

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);

/***********______Function definitions______************/
function create_form_field($name, $title, $icon_id, $icon_title) {
    $form_field = "<br><label for='$name'>$title</label>";
    $form_field.= "<input type='text' id='$name' name='$name' class='text ui-widget-content ui-corner-all' onkeyup='validate_on_input(this.name)'><i class='far fa-question-circle' style='color: blue' id='$icon_id' title='$icon_title'></i>\n";

    return $form_field;
}

function create_form_select_field($name, $title, $icon_id, $icon_title) {
    $sql = get_all_genders();

    $select_field = "<br><label for='$name'>$title</label>\n<select name='$name' id='$name'>";

    while($gender = $sql->fetch(PDO::FETCH_ASSOC)) {
        $select_field.= "<option value='".$gender['id']."'>".$gender['name']."</option>\n";
    }

    $select_field.= "</select><i class='far fa-question-circle' style='color: blue' id='".$icon_id."' title='".$icon_title."'></i>\n";

    return $select_field;
}