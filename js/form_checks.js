var input, sid_length, sid_regex, min, name_max, address_max, major_max, comments_max, gendername_max, description_max, field_valid, field_err;

$.getJSON("php/validation_constants.php", function (constants) {

    sid_length = constants.sid_length;
    sid_regex = constants.sid_regex;
    min = constants.min;
    name_max = constants.name_max;
    address_max = constants.address_max;
    major_max = constants.major_max;
    comments_max = constants.comments_max;
    gendername_max = constants.gender_max;
    description_max = constants.gender_description_max;

    field_valid = constants.field_valid_color;
    field_err = constants.field_error_color;
});

function validate_on_input(fieldname) {

    input = document.getElementById(fieldname).value;

    switch (fieldname) {
        case 'sid':
            check_if_valid('sid', input, min, sid_length);
            break;
        case 'firstname':
            check_if_valid('firstname', input, min, name_max);
            break;
        case 'lastname':
            check_if_valid('lastname', input, min, name_max);
            break;
        case 'address':
            check_if_valid('address', input, min, address_max);
            break;
        case 'major':
            check_if_valid('major', input, min, major_max);
            break;
        case 'comments':
            check_if_valid('comments', input, min, comments_max);
            break;
        case 'gendername':
            check_if_valid('gendername', input, min, gendername_max);
            break;
        case 'description':
            check_if_valid('description', input, min, description_max);
            break;
    }
}

function check_if_valid(fieldname ,input, min, max) {

    if(fieldname === 'sid') {
        if(input.length == max && input_is_string(input) && check_regex(input)) {
            set_field_background(fieldname, field_valid);
        } else {
            //Make text box background red
            set_field_background(fieldname, field_err);
        }
    } else {
        if(length_in_bounds(input, min, max) && input_is_string(input)) {
            set_field_background(fieldname, field_valid);
        } else {
            //Make text box background red
            set_field_background(fieldname, field_err);
        }
    }
}

function set_field_background(fieldname, field_status) {
    document.getElementById(fieldname).style.backgroundColor = field_status;
}

function length_in_bounds(input, min, max) {

    if(input.length > min && input.length < max) {
        return true;
    } else {
        return false;
    }
}

function input_is_string(input) {
    if((typeof input === 'string') || (input instanceof String)) {
        return true;
    } else {
        return false;
    }
}

function check_regex(input) {

    var regex = new RegExp(sid_regex);

    //console.log("SID Field Input: "+input);
    //console.log("Regex Match on input: "+regex.test(input));

    if(regex.test(input)) {
        return true;
    } else {
        return false;
    }
}

$(document).ready(function() {
    $('#sid_icon').tooltip();
    $('#fname_icon').tooltip();
    $('#lname_icon').tooltip();
    $('#addr_icon').tooltip();
    $('#major_icon').tooltip();
    $('#gender_icon').tooltip();
    $('#comments_icon').tooltip();
    $('#gname_icon').tooltip();
    $('#description_icon').tooltip();

    //Gender dropdown
    $('#gender').selectmenu();
});