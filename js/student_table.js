var student_table, operation_id, student_row_data;
var student_dialog;
var delete_student_dialog;
var button_id;

var gender_id, gender_value, gender_map;
var csrf_token;

var sid, firstname, lastname, address, major, gender, comments, allFields, tips;

var sid_length, sid_regex, min, name_max, address_max, major_max, gender_max, comments_max;

$.getJSON("php/validation_constants.php", function (constants) {

    sid_length = constants.sid_length;
    sid_regex = constants.sid_regex;
    min = constants.min;
    name_max = constants.name_max;
    address_max = constants.address_max;
    major_max = constants.major_max;
    gender_max = constants.gender_max;
    comments_max = constants.comments_max;
});

$.getJSON("php/gender_map.php", function (map) {
    gender_map = map;
});

$(document).ready(function()
{

    //Assign fields to variables for onSubmit validation purposes
    sid = $('#sid');
    firstname = $('#firstname');
    lastname = $('#lastname');
    address = $('#address');
    major = $('#major');
    gender = $('#gender');
    comments = $('#comments');
    allFields = $([]).add(sid).add(firstname).add(lastname).add(address).add(major).add(gender).add(comments);

    tips = $(".validateTips");

    $.getJSON("php/get_token.php", function (token) {
        csrf_token = token;
        //console.log("CSRF Token value: " + csrf_token);
    });

    //Init table
    student_table = $('#students').DataTable( {
        "serverSide": true,
        "processing": true,
        "ajax": {
            "url": "student_json.php",
            "data": {
                "token": csrf_token
            }
        },
        "rowId": "operation_id",
        "lengthMenu": [15, 30, 50, 70],
        "dom" : 'Bfrtip',
        "columns": [
            {"data": "sid"},
            {"data": "firstname"},
            {"data": "lastname"},
            {"data": "address"},
            {"data": "major"},
            {"data": "gender"},
            {"data": "comments"},
            {
                "class": "student_operations",
                "orderable": false,
                "data": null,
                "defaultContent": "<i class='far fa-edit' id='edit_student' onclick='set_student_button_id(this.id)' title='Edit Student'></i><i class='far fa-clone' id='clone_student' onclick='set_student_button_id(this.id)' title='Clone Student'></i><i class='far fa-trash-alt' id='delete_student' onclick='set_student_button_id(this.id)' title='Delete Student'></i>"
            }
        ],
        buttons: ['excel']
    });

    //Init student_dialog
    student_dialog = $('#student-dialog-form').dialog({
        autoOpen: false,
        height: 500,
        width: 575,
        modal: true,
        buttons: {
            "OK": function (){

                //Based on action, trigger relevant method
                switch (button_id) {
                    case 'add_student':
                        addStudent();
                        break;
                    case 'edit_student':
                        editStudent();
                        break;
                    case 'clone_student':
                        cloneStudent();
                        break;
                    case 'delete_student':
                        deleteStudent();
                        break;
                }
            },
            Cancel: function(){
                student_dialog.dialog("close");
            }
        },
        close: function() {
            //student_form[0].reset();
            allFields.removeClass("ui-state-error");
        }
    });

    //Init delete_student_dialog
    delete_student_dialog = $('#student-delete-dialog').dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Delete": deleteStudent,
            Cancel: function () {
                $(this).dialog("close");
            }
        }
    });

    //Student operations (Edit, Clone, Delete)
    $('#students tbody').on('click', 'tr td.student_operations', function() {

        console.log("Button id: " + button_id);

        operation_id = $(this).closest('tr').attr('id');
        console.log("Student operation id: " + operation_id);
        //console.log("SID right after is it assigned: " + sid_for_operation);

        student_row_data = student_table.row(this).data();
        //console.log("Row data: " + JSON.stringify(row_data));

        display_student_prompt(button_id);
    } );

    //Add student button action
    $('#add_student').button().on("click", function () {
        console.log("Clicking...");
        display_student_prompt(button_id);
    });

    function addStudent() {

        allFields.removeClass("ui-state-error");

        valid = check_student_data_validity()

        if(valid) {

            var form_data = $('#student-form').serializeArray();
            form_data.push({name: 'action', value: 'add'});
            form_data.push({name: 'token', value: csrf_token});

            //console.log("Add form data: " + JSON.stringify(form_data));

            //Trigger backend php to addname
            $.ajax({
                type: 'POST',
                url: 'student_actions.php',
                data: form_data,
                success: function(response){
                    //console.log("Response from insert_student.php: " + response);
                    student_dialog.dialog("close");
                    student_table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid add case");
        }
    }

    function editStudent() {

        allFields.removeClass("ui-state-error");

        valid = check_student_data_validity();

        if(valid) {

            var form_data = $('#student-form').serializeArray();
            form_data.push({name: 'row_id', value: operation_id});
            form_data.push({name: 'action', value: 'edit'});
            form_data.push({name: 'token', value: csrf_token});

            //console.log("Edit form data: " + JSON.stringify(form_data));

            //Trigger backend php to addname
            $.ajax({
                type: 'POST',
                url: 'student_actions.php',
                data: form_data,
                success: function(response){
                    //console.log("Response from insert_student.php: " + response);
                    student_dialog.dialog("close");
                    student_table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid add case");
        }
    }

    function cloneStudent() {

        allFields.removeClass("ui-state-error");

        var valid = true;

        valid = valid && checkLength($('#sid'), "SID", sid_length, sid_length);
        valid = valid && checkRegex($('#sid'), "Must begin with 'S0'");

        if(valid) {

            var form_data = $('#student-form').serializeArray();
            form_data.push({name: 'action', value: 'clone'});
            form_data.push({name: 'token', value: csrf_token});

            $.ajax({
                type: 'POST',
                url: 'student_actions.php',
                data: form_data,
                success: function(response) {
                    student_dialog.dialog("close");
                    student_table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid clone case");
        }
    }

    function deleteStudent() {

        $.ajax({
            type: 'POST',
            url: 'student_actions.php',
            data: {'row_id': operation_id, 'action': 'delete', 'token': csrf_token},
            success: function(response) {
                delete_student_dialog.dialog("close");
                student_table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(textStatus, errorThrown);
            }
        });

        console.log("End of valid delete case");
    }
});

function display_student_prompt(button_id) {

    if(button_id === 'add_student') {

        //Empty all fields
        $('#student-form').trigger('reset');

        enable_fields();
        $('#student-form').trigger('reset');

        student_dialog.dialog('open');

    } else if(button_id === 'edit_student') {

        enable_fields();
        $('#student-form').trigger('reset');

        prefill_student_form(student_row_data);

        student_dialog.dialog('open');

    } else if(button_id === 'clone_student') {

        //Diable all fields except SID
        disable_clone_fields();
        $('#student-form').trigger('reset');

        prefill_student_form(student_row_data);

        student_dialog.dialog('open');

    } else if(button_id === 'delete_student') {

        delete_student_dialog.dialog('open');

    }
}

function prefill_student_form(student_row_data) {

    $('#sid').val(student_row_data.sid);
    $('#firstname').val(student_row_data.firstname);
    $('#lastname').val(student_row_data.lastname);
    $('#address').val(student_row_data.address);
    $('#major').val(student_row_data.major);

    gender_value = student_row_data.gender;
    gender_id = gender_map[gender_value];

    //console.log("gender_value: " + gender_value);
    //console.log("gender map: " + JSON.stringify(gender_map));
    //console.log("gender_id: " + gender_id);

    //document.getElementById('gender').value = gender_id;
    $('#gender').val(gender_id);
    $('#gender').selectmenu('refresh');

    $('#comments').val(student_row_data.comments);
}

function check_student_data_validity() {

    var valid = true;

    valid = valid && checkLength($('#sid'), "SID", sid_length, sid_length);
    valid = valid && checkLength($('#firstname'), "firstnane", min, name_max);
    valid = valid && checkLength($('#lastname'), "lastname", min, name_max);
    valid = valid && checkLength($('#address'), "address", min, address_max);
    valid = valid && checkLength($('#major'), "major", min, major_max);
    //valid = valid && checkLength($('#comments'), "comments", min, comments_max);

    valid = valid && checkRegex($('#sid'), "Must begin with 'S0'");

    return valid;
}

function updateTips(t) {
    tips
        .text(t)
        .addClass("ui-state-highlight");
    setTimeout(function () {
        tips.removeClass("ui-state-highlight", 1500);
    }, 500);
}

function checkLength(field, name, min, max) {

    //console.log("What is inside field: " + field);

    if (field.val().length > max || field.val().length < min) {
        field.addClass("ui-state-error");
        updateTips("Length of " + name + " must be between " + min + " and " + max + ".");
        return false;
    } else{
        return true;
    }
}

function checkRegex(field, name) {

    var regex = new RegExp(sid_regex);

    //console.log("Field value being tested against regex: " + field.value);

    if(!(regex.test( field.val() ) ) ){
        field.addClass("ui-state-error");
        updateTips(name);
        return false;
    } else {
        return true;
    }
}

function enable_fields() {
    $('#firstname').attr('readonly', false);
    $('#lastname').attr('readonly', false);
    $('#address').attr('readonly', false);
    $('#major').attr('readonly', false);
    //$('#gender').selectmenu('enable');
    $('#comments').attr('readonly', false);
}

function disable_clone_fields() {

    $('#firstname').attr('readonly', true);
    $('#lastname').attr('readonly', true);
    $('#address').attr('readonly', true);
    $('#major').attr('readonly', true);
    //$('#gender').selectmenu('disable');
    $('#comments').attr('readonly', true);
}

function set_student_button_id(clicked) {
    button_id = clicked;
}

/*
function display_previous_value(field_id) {

    var previous_value;
    row_id = sid_for_operation;

    //Add placeholder to each edit form field

    switch(field_id) {
        case 'edit_firstname':
            previous_value = table.row(row_id).data()[1];
            break;
        case 'edit_lastname':
            previous_value = table.row(row_id).data()[2];
            break;
        case 'edit_address':
            previous_value = table.row(row_id).data()[3];
            break;
        case 'edit_major':
            previous_value = table.row(row_id).data()[4];
            break;
        case 'edit_gender':
            previous_value = table.row(row_id).data()[5];
            break;
        case 'edit_comments':
            previous_value = table.row(row_id).data()[6];
            break;
    }

    console.log("Previous value: " + previous_value);

    return previous_value;
}
 */