var gender_table, gender_operation_id, gender_row_data;
var button_id;

var gendername, description, allFields, tips;
var csrf_token;

var min, gender_name_max, description_max;

$.getJSON("php/validation_constants.php", function (constants) {

    min = constants.min;
    gender_name_max = constants.gender_max;
    description_max = constants.gender_description_max;
});

$(document).ready(function()
{

    //Assign fields to variables for onSubmit validation purposes
    gendername = $('#gendername');
    description = $('#description');
    allFields = $([]).add(gendername).add(description);

    tips = $(".validateTips");

    $.getJSON("php/get_token.php", function (token) {
        csrf_token = token;
    });

    //Init table
    gender_table = $('#genders').DataTable( {
        "serverSide": true,
        "processing": true,
        "ajax": {
            "url": "admin_panel/gender/gender_json.php",
            "data": {
                "token": csrf_token
            }
        },
        "rowId": "id",
        "lengthMenu": [10, 20, 40, 60],
        "dom": 'Bfrtip',
        "columns": [
            {"data": "id"},
            {"data": "gendername"},
            {"data": "description"},
            {
                "class": "gender_operations",
                "orderable": false,
                "data": null,
                "defaultContent": "<i class='far fa-edit' id='edit_gender' onclick='set_gender_button_id(this.id)' title='Edit Gender'></i></i><i class='far fa-trash-alt' id='delete_gender' onclick='set_gender_button_id(this.id)' title='Delete Gender'></i>"
            }
        ],
        buttons: ['excel']
    });

    //Init gender_dialog
    gender_dialog = $('#gender-dialog-form').dialog({
        autoOpen: false,
        height: 500,
        width: 575,
        modal: true,
        buttons: {
            "OK": function (){

                //Based on action, trigger relevant method
                switch (button_id) {
                    case 'add_gender':
                        addGender();
                        break;
                    case 'edit_gender':
                        editGender();
                        break;
                }
            },
            Cancel: function(){
                gender_dialog.dialog("close");
            }
        },
        close: function() {
            //student_form[0].reset();
            allFields.removeClass("ui-state-error");
        }
    });

    //Init delete_gender_dialog
    delete_gender_dialog = $('#gender-delete-dialog').dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "Delete": deleteGender,
            Cancel: function () {
                $(this).dialog("close");
            }
        }
    });

    prevent_delete_dialog = $('#prevent-gender-delete').dialog({
        autoOpen: false,
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            OK: function() {
                $(this).dialog("close");
            }
        }
    });

    //Gender operations (Edit, Delete)
    $('#genders tbody').on('click', 'tr td.gender_operations', function() {

        console.log("Button id: " + button_id);

        gender_operation_id = $(this).closest('tr').attr('id');
        //console.log("SID right after is it assigned: " + sid_for_operation);

        gender_row_data = gender_table.row(this).data();
        //console.log("Row data: " + JSON.stringify(row_data));

        display_gender_prompt(button_id);
    });

    //Add student button action
    $('#add_gender').button().on("click", function () {
        console.log("Clicking...");
        display_gender_prompt(button_id);
    });

    function addGender() {

        allFields.removeClass("ui-state-error");

        valid = check_gender_data_validity();

        if(valid) {

            var form_data = $('#gender-form').serializeArray();
            form_data.push({name: 'action', value: 'add'});
            form_data.push({name: 'token', value: csrf_token});

            //Trigger backend php to addgender
            $.ajax({
                type: 'POST',
                url: 'admin_panel/gender/gender_actions.php',
                data: form_data,
                success: function(response){
                    gender_dialog.dialog("close");
                    gender_table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid add case");
        }
    }

    function editGender() {

        allFields.removeClass("ui-state-error");

        valid = check_gender_data_validity();

        if(valid) {

            var form_data = $('#gender-form').serializeArray();
            form_data.push({name: 'row_id', value: gender_operation_id});
            form_data.push({name: 'action', value: 'edit'});
            form_data.push({name: 'token', value: csrf_token});

            //console.log("Edit form data: " + JSON.stringify(form_data));

            //Trigger backend php to addgender
            $.ajax({
                type: 'POST',
                url: 'admin_panel/gender/gender_actions.php',
                data: form_data,
                success: function(response){
                    gender_dialog.dialog("close");
                    gender_table.ajax.reload();
                },
                error: function(jqXHR, textStatus, errorThrown){
                    console.log(textStatus, errorThrown);
                }
            });

            console.log("End of valid add case");
        }
    }

    function deleteGender() {

        $.ajax({
            type: 'POST',
            url: 'admin_panel/gender/gender_actions.php',
            data: {'row_id': gender_operation_id, 'action': 'delete', 'token': csrf_token},
            success: function(response) {
                delete_gender_dialog.dialog("close");
                gender_table.ajax.reload();
            },
            error: function(jqXHR, textStatus, errorThrown){
                console.log(textStatus, errorThrown);
            }
        });

        console.log("End of valid delete case");
    }
});

function display_gender_prompt(button_id) {

    if(button_id === 'add_gender') {

        //Empty all fields
        $('#gender-form').trigger('reset');
        gender_dialog.dialog('open');

    } else if(button_id === 'edit_gender') {

        $('#gender-form').trigger('reset');
        prefill_gender_form(gender_row_data);
        gender_dialog.dialog('open');

    } else if(button_id === 'delete_gender') {

        $.get('admin_panel/gender/get_gender_instances.php', {'gender_id': gender_operation_id}).done(function (data) {

            console.log("Gender JSON: " + data);

            var json = $.parseJSON(data);
            var gender_count = json['gender_count'];

            console.log("Gender count: " + gender_count);

            //If occurrences are 0, call deleteGender, else display number of occurrences in dialog
            if(gender_count == 0) {

                delete_gender_dialog.dialog('open');

            } else {
                console.log("Inside gender used case.");

                //var dialog_text = "Cannot delete gender as there are " + gender_count + " instances of it in use.";

                //Add message to span to display within dialog
                //$("#prevent-gender-delete #prevent-gender-msg").text(dialog_text);

                prevent_delete_dialog.dialog('open');
            }
        });
    }
}

function prefill_gender_form(gender_row_data) {

    $('#gendername').val(gender_row_data.gendername);
    $('#description').val(gender_row_data.description);
}

function check_gender_data_validity() {

    var valid = true;

    valid = valid && checkLength($('#gendername'), "Gender", min, gender_name_max);
    valid = valid && checkLength($('#description'), "Description", min, description_max);

    return valid;
}

function set_gender_button_id(clicked) {
    button_id = clicked;
}