var table, operation_id, row_data;
var add_student_dialog, add_student_form;
var edit_student_dialog, edit_student_form, editor;
var delete_student_dialog, clone_student_dialog;
var button_id;

var sid, firstname, lastname, address, major, gender, comments, allFields, tips;
var edit_sid, edit_firstname, edit_lastname, edit_address, edit_major, edit_gender, edit_comments, edit_allFields;

var sid_length, sid_regex, min, name_max, address_max, major_max, gender_max, comments_max;

$.getJSON("https://vweb32.engr.uvic.ca/samroy2106/master/php/validation_constants.php", function (constants) {

    sid_length = constants.sid_length;
    sid_regex = constants.sid_regex;
    min = constants.min;
    name_max = constants.name_max;
    address_max = constants.address_max;
    major_max = constants.major_max;
    gender_max = constants.gender_max;
    comments_max = constants.comments_max;
});

$(document).ready(function()
{
    sid = $("#sid");
    firstname = $("#firstname");
    lastname = $("#lastname");
    address = $("#address");
    major = $("#major");
    gender = $("#gender");
    comments = $("#comments");
    allFields = $([]).add(sid).add(firstname).add(lastname).add(address).add(major).add(gender).add(comments);

    tips = $(".validateTips");

    function updateTips(t) {
        tips
            .text(t)
            .addClass("ui-state-highlight");
        setTimeout(function () {
            tips.removeClass("ui-state-highlight", 1500);
        }, 500);
    }

    function checkLength(field, name, min, max) {
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

        if(!(regex.test( field.val() ) ) ){
            field.addClass("ui-state-error");
            updateTips(name);
            return false;
        } else {
            return true;
        }
    }

    //Init table
    table = $('#students').DataTable( {
        "serverSide": true,
        "processing": true,
        "ajax": "https://vweb32.engr.uvic.ca/samroy2106/master/student_json.php",
        "rowId": "operation_id",
        "lengthMenu": [15, 30, 50, 70],
        "columns": [
            {"data": "sid"},
            {"data": "firstname"},
            {"data": "lastname"},
            {"data": "address"},
            {"data": "major"},
            {"data": "gender"},
            {"data": "comments"},
            {
                "class": "operations",
                "orderable": false,
                "data": null,
                "defaultContent": "<button id='edit' onclick='set_button_id(this.id)'><i class='far fa-edit' title='Edit Student'></i></button><button id='clone' onclick='set_button_id(this.id)'><i class='far fa-clone' title='Clone Student'></i></button><button id='delete' onclick='set_button_id(this.id)'><i id='delete' class='far fa-trash-alt' title='Delete Student'></i></button>"
            }
        ]
    } );

    //Init add_student_dialog
    add_student_dialog = $('#add-dialog-form').dialog({
        autoOpen: false,
        height: 400,
        width: 650,
        modal: true,
        buttons: {
            "OK": addStudent,
            Cancel: function(){
                add_student_dialog.dialog("close");
            }
        },
        close: function() {
            add_student_form[0].reset();
            allFields.removeClass("ui-state-error");
        }
    });

    //Init edit_student_dialog
    edit_student_dialog = $('#edit-dialog-form').dialog({
        autoOpen: false,
        height: 400,
        width: 650,
        modal: true,
        buttons: {
            "Confirm": editStudent,
            Cancel: function(){
                edit_student_dialog.dialog("close");
            }
        },
        close: function() {
            edit_student_form[0].reset();
            allFields.removeClass("ui-state-error");
        }
    });

    //Init clone_student_dialog
    clone_student_dialog = $('#clone-dialog').dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Clone": cloneStudent,
            Cancel: function () {
                $(this).dialog("close");
            }
        }
    });

    //Init delete_student_dialog
    delete_student_dialog = $('#delete-dialog').dialog({
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

    /*
    editor = new $.fn.dataTable.Editor( {
        ajax: "https://vweb32.engr.uvic.ca/samroy2106/master/student_json.php",
        table: "#students",
        fields: [ {
                label: "First Name: ",
                name: "firstname"
            }, {
                label: "Last Name: ",
                name: "lastname"
            }, {
                label: "Address: ",
                name: "address"
            }, {
                label: "Major: ",
                name: "major"
            }, {
                label: "Gender: ",
                name: "gender"
            }, {
                label: "Comments: ",
                name: "comments"
            }
        ]
    });
    */

    //Submit add_student_form
    add_student_form = add_student_dialog.find("form").on("submit", function (event) {
        event.preventDefault();
        addStudent();
    });

    //Submit edit_student_form
    edit_student_form = edit_student_dialog.find("form").on("submit", function (event) {
        event.preventDefault();
        editStudent();
    });

    //Student operations (Edit, Clone, Delete)
    $('#students tbody').on('click', 'tr td.operations', function() {

        console.log("Button id: " + button_id);

        operation_id = $(this).closest('tr').attr('id');
        //console.log("SID right after is it assigned: " + sid_for_operation);

        row_data = table.row(this).data();
        //console.log("Row data: " + row_data);

        display_prompt(button_id);
    } );

    //Add student
    $('#add-student').button().on("click", function () {
        console.log("Clicking...");
        add_student_dialog.dialog('open');
    });

    function addStudent() {
        var serializedData;
        var valid = true;

        allFields.removeClass("ui-state-error");

        valid = valid && checkLength(sid, "SID", sid_length, sid_length);
        valid = valid && checkLength(firstname, "firstnane", min, name_max);
        valid = valid && checkLength(lastname, "lastname", min, name_max);
        valid = valid && checkLength(address, "address", min, address_max);
        valid = valid && checkLength(major, "major", min, major_max);
        valid = valid && checkLength(gender, "gender", min, gender_max);
        //valid = valid && checkLength(comments, "comments", min, comments_max);

        valid = valid && checkRegex(sid, "Must begin with 'S0'");

        if(valid) {

            serializedData = jQuery.param(allFields);

            //Trigger backend php to addname
            $.ajax({
                type: 'POST',
                url: 'https://vweb32.engr.uvic.ca/samroy2106/master/insert_student.php',
                data: serializedData,
                success: function(response){
                    //No response here, redirected back to page
                    //console.log("Response from insert_student.php: " + response);
                    add_student_dialog.dialog("close");
                    table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            //Extract JSON from $_SESSION?

            console.log("End of valid add case");
        }
    }

    function editStudent() {
        var serializedData;
        var valid = true;

        allFields.removeClass("ui-state-error");

        //valid = valid && checkLength(edit_sid, "SID", sid_length, sid_length);
        //valid = valid && checkLength(edit_firstname, "firstnane", min, name_max);
        //valid = valid && checkLength(edit_lastname, "lastname", min, name_max);
        //valid = valid && checkLength(edit_address, "address", min, address_max);
        //valid = valid && checkLength(edit_major, "major", min, major_max);
        //valid = valid && checkLength(edit_gender, "gender", min, gender_max);
        //valid = valid && checkLength(comments, "comments", min, comments_max);

        //valid = valid && checkRegex(edit_sid, "Must begin with 'S0'");

        if(valid) {

            var form_data = $('#edit_form').serializeArray();
            form_data.push({name: 'row_id', value: operation_id});

            console.log("Form data: "+JSON.stringify(form_data));

            //Trigger backend php to addname
            $.ajax({
                type: 'POST',
                url: 'https://vweb32.engr.uvic.ca/samroy2106/master/update_student.php',
                data: form_data,
                success: function(response){
                    //No response here, redirected back to page
                    //console.log("Response from insert_student.php: " + response);
                    add_student_dialog.dialog("close");
                    table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid add case");
        }
    }

    function cloneStudent() {

        serializedData = jQuery.param(row_data);

        $.ajax({
            type: 'POST',
            url: 'https://vweb32.engr.uvic.ca/samroy2106/master/clone_student.php',
            data: serializedData,
            success: function(response) {
                clone_student_dialog.dialog("close");
                table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(textStatus, errorThrown);
            }
        });
        console.log("End of valid clone case");
    }

    function deleteStudent() {

        $.ajax({
            type: 'POST',
            url: 'https://vweb32.engr.uvic.ca/samroy2106/master/delete_student.php',
            data: {'operation_id': operation_id},
            success: function(response) {
                delete_student_dialog.dialog("close");
                table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(textStatus, errorThrown);
            }
        });
        console.log("End of valid delete case");
    }

} );

function display_prompt(button_id) {

    switch (button_id) {
        case 'edit':
            edit_student_dialog.dialog('open');
            break;
        case 'clone':
            clone_student_dialog.dialog('open');
            break;
        case 'delete':
            delete_student_dialog.dialog('open');
            break;
    }
}

function set_button_id(clicked) {
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