<?php

require_once('php/startsession.php');
require_once('../config.php');
require_once('php/constants_config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');

$start = microtime(true);

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>\n<title>Student Contact Manager</title>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css'>
<link rel='stylesheet' type='text/css' href='css/main_page.css'>
<link rel='stylesheet' type='text/css' href='css/student_table.css'>
<link rel='stylesheet' type='text/css' href='admin_panel/gender/css/gender_table.css'>
<link rel='stylesheet' type='text/css' href='admin_panel/student_action_log/css/log_table.css'>
<script src='https://kit.fontawesome.com/8b75e01689.js' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/jquery-3.5.1.js'></script>
<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
<script src='https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js'></script>
<script src='https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js'></script>
<script src='https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js'></script>
<script src='js/tabs.js'></script>
<script src='js/student_table.js'></script>
<script src='js/form_checks.js'></script>
<script src='admin_panel/gender/js/gender_table.js'></script>
<script src='admin_panel/student_action_log/js/log_table.js'></script>
</head>\n<body>\n";

$div_footer = "</div>";
$html_footer = "</body>\n</html>";

/**********Student Page************/
$student_div_header = "<div id='student_page'>";

$student_table_header = "<div id='student_table'>";

$student_table = "<table id='students' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n<th>Firstname</th>\n<th>Lastname</th>
<th>Address</th>\n<th>Major</th>\n<th>Gender</th>\n<th>Comments</th>\n<th>Operations</th></tr>\n</thead>\n";

$student_table_footer = "</table>\n";

$add_student_button = "<button class='op_button add-btn' id='add_student' onclick='set_student_button_id(this.id)'><i class='far fa-address-card' title='Add Student'></i></button>\n</div>\n";

$student_form = "<div id='student-dialog-form' title='Perform Student Action'>\n<p class='validateTips'>Please enter valid data only.</p>\n<form id='student-form'>\n<fieldset>";
$student_form.= create_form_field('sid', "SID: ", 'sid_icon', "Must begin with S0 and be exactly 10 characters");
$student_form.= create_form_field('firstname', "First Name: ", 'fname_icon', "Must be 2-20 characters");
$student_form.= create_form_field('lastname', "Last Name: ", 'lname_icon', "Must be 2-20 characters");
$student_form.= create_form_field('address', "Address: ", 'addr_icon', "Must be 2-50 characters");
$student_form.= create_form_field('major', "Major: ", 'major_icon', "Must be 2-20 characters");
$student_form.= create_form_select_field('gender', "Gender: ", 'gender_icon', "Please select one that applies");
$student_form.= create_form_multiline_field('comments', "Comments: ", 'comments_icon', "Must be 2-250 characters", 40, 5);
$student_form.= "<input type='submit' tabindex='-1' style='position:absolute; top:-500px'></fieldset></form>\n</div>\n";

$delete_student_dialog = "<div id='student-delete-dialog' title='Delete student record?'>\n<p>\n<span class='ui-icon ui-icon-alert' style='float:left; margin:12px 12px 20px 0;'></span>\nStudent record will be deleted from the database. Are you sure?\n</p>\n</div>\n";

/**********Gender Page************/
$gender_div_header = "<div id='gender_page'>";

$gender_table_header = "<div id='gender_table'>";

$gender_table = "<table id='genders' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n\n<th>Name</th>
<th>Description</th>\n<th>Operations</th></tr>\n</thead>\n";

$gender_table_footer = "</table>\n";

$add_gender_button = "<button class='op_button add-btn' id='add_gender' onclick='set_gender_button_id(this.id)'><i class='far fa-address-card' title='Add Gender'></i></button>\n</div>\n";

$gender_form = "<div id='gender-dialog-form' title='Perform Gender Action'>\n<p class='validateTips'>Gender Action</p>\n<form id='gender-form'>\n<fieldset>";
$gender_form.= create_form_field('gendername', "Gender Name: ", 'gname_icon', "Must be 1-25 characters");
$gender_form.= create_form_multiline_field('description', "Description: ", 'description_icon', "Optional", 16, 5);
$gender_form.= "<input type='submit' tabindex='-1' style='position:absolute; top:-500px'></fieldset></form>\n</div>\n";

$delete_gender_dialog = "<div id='gender-delete-dialog' title='Delete gender?'>\n<p>\n<span class='ui-icon ui-icon-alert' id='gender-msg' style='float:left; margin:12px 12px 20px 0;'></span>\nThe selected gender will be deleted from the database. Are you sure?\n</p>\n</div>\n";
$prevent_gender_delete_dialog = "<div id='prevent-gender-delete' title='Gender in use'>\n<p>\n<span class='ui-icon ui-icon-alert' id='prevent-gender-msg' style='float:left; margin:12px 12px 20px 0;'></span>\n</p>\n</div>\n";

/***********Log Page**************/
$log_div_header = "<div id='log_page'>";

$log_table_header = "<div id='log_table'>";

$log_table = "<table id='logs' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n<th>Action Time</th>
<th>Requester</th>\n<th>IP Address</th>\n<th>Action</th><th>Details</th></tr>\n</thead>\n";

$log_table_footer = "</table>\n";

/**************End of individual page HTML*********************/
//Add logic here to echo relevant page html
$student_html = $student_div_header.$student_form.$delete_student_dialog.$student_table_header.$student_table.$student_table_footer.$add_student_button.$div_footer;
$gender_html = $gender_div_header.$gender_form.$delete_gender_dialog.$prevent_gender_delete_dialog.$gender_table_header.$gender_table.$gender_table_footer.$add_gender_button.$div_footer;
$log_html = $log_div_header.$log_table_header.$log_table.$log_table_footer.$div_footer;

/****************Main Page******************/
$main_div_header = "<div id='tabs'>\n<ul>\n<li><a href='#student_page'>Student Page</a></li>\n<li><a href='#gender_page'>Gender Page</a></li>\n<li><a href='#log_page'>Log Page</a>\n</li>\n</ul>\n";

$html = $html_header.$main_div_header.$student_html.$gender_html.$log_html.$div_footer.$html_footer;
echo $html;

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);

/***********______Function definitions______************/
function create_form_field($name, $title, $icon_id, $icon_title) {
    $form_field = "<br><label for='$name'>$title";
    $form_field.= "<input type='text' id='$name' name='$name' class='text ui-widget-content ui-corner-all' onkeyup='validate_on_input(this.name)'><i class='far fa-question-circle' style='color: blue' id='$icon_id' title='$icon_title'></i></label>\n";

    return $form_field;
}

function create_form_select_field($name, $title, $icon_id, $icon_title) {
    $sql = get_all_genders();

    $select_field = "<br><label for='$name'>$title\n<select name='$name' id='$name'>";

    while($gender = $sql->fetch(PDO::FETCH_ASSOC)) {
        $select_field.= "<option value='".$gender['id']."'>".$gender['name']."</option>\n";
    }

    $select_field.= "</select><i class='far fa-question-circle' style='color: blue' id='".$icon_id."' title='".$icon_title."'></i></label>\n";

    return $select_field;
}

function create_form_multiline_field($name, $title, $icon_id, $icon_title, $columns, $rows) {
    $form_field = "<br><label for='$name'>$title";
    $form_field.= "<textarea id='$name' name='$name' cols='$columns' rows='$rows' onkeyup='validate_on_input(this.name)'></textarea><i class='far fa-question-circle' style='color: blue' id='$icon_id' title='$icon_title'></i></label>\n";

    return $form_field;
}

function get_all_genders() {

    global $servername, $dbname, $username, $password;

    $conn = dbconnect($servername, $dbname, $username, $password);

    $sql = $conn->prepare("SELECT id, name FROM gender");
    $sql->execute();

    $conn = null;

    return $sql;
}