<?php
require_once('../../php/startsession.php');
require_once('../../../config.php');
require_once('../../php/constants_config.php');
require_once('../../php/log.php');

$start = microtime(true);

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>\n<title>Gender Table</title>
<link rel='stylesheet' type='text/css' href='https://cdn.datatables.net/1.10.22/css/jquery.dataTables.css'>
<link rel='stylesheet' href='https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'>
<link rel='stylesheet' type='text/css' href='css/gender_table.css'>
<script src='https://kit.fontawesome.com/8b75e01689.js' crossorigin='anonymous'></script>
<script src='https://code.jquery.com/jquery-3.5.1.js'></script>
<script src='https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js'></script>
<script src='https://code.jquery.com/ui/1.12.1/jquery-ui.js'></script>
<script src='js/gender_table.js'></script>
<script src='js/gender_form.js'></script>
</head>\n<body>\n";

$table_header = "<div id='gender_table'>";

$table = "<table id='genders' class='display' style='width:100%'>\n<thead>\n<tr>\n<th>ID</th>\n\n<th>Name</th>
<th>Description</th>\n<th>Operations</th></tr>\n</thead>\n";

$table_footer = "</table>\n";

$add_gender_button = "<button class='op_button add-btn' id='add' onclick='set_button_id(this.id)'><i class='far fa-address-card' title='Add Gender'></i></button>\n</div>\n";

$html_footer = "</body>\n</html>";

$form = "<div id='dialog-form' title='Perform Gender Action'>\n<p class='validateTips'>Gender Action</p>\n<form id='gender-form'>\n<fieldset>";
$form.= create_form_field('gendername', "Gender Name: ", 'gname_icon', "Must be 1-25 characters");
$form.= create_form_multiline_field('description', "Description: ", 'description_icon', "Optional");
$form.= "<input type='submit' tabindex='-1' style='position:absolute; top:-500px'></fieldset></form>\n</div>\n";

$delete_dialog = "<div id='delete-dialog' title='Delete gender?'>\n<p>\n<span class='ui-icon ui-icon-alert' id='gender-msg' style='float:left; margin:12px 12px 20px 0;'></span>\nThe selected gender will be deleted from the database. Are you sure?\n</p>\n</div>\n";
$prevent_delete_dialog = "<div id='prevent-gender-delete' title='Gender in use'>\n<p>\n<span class='ui-icon ui-icon-alert' id='prevent-gender-msg' style='float:left; margin:12px 12px 20px 0;'></span>\n</p>\n</div>\n";

$html = $html_header.$form.$delete_dialog.$prevent_delete_dialog.$table_header.$table.$table_footer.$add_gender_button.$html_footer;
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

function create_form_multiline_field($name, $title, $icon_id, $icon_title) {
    $form_field = "<br><label for='$name'>$title";
    $form_field.= "<textarea id='$name' name='$name' cols='16' rows='5' onkeyup='validate_on_input(this.name)'></textarea><i class='far fa-question-circle' style='color: blue' id='$icon_id' title='$icon_title'></i></label>\n";

    return $form_field;
}