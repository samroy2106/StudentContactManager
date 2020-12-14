<?php
//Length bounds
$SID_LENGTH = 10;
$MIN = 1;
$NAME_MAX = 20;
$ADDRESS_MAX = 50;
$MAJOR_MAX = 20;
$GENDER_MAX = 25;
$COMMENTS_MAX = 250;

$GENDER_DESCRIPTION_MAX = 80;

//Color values
$FIELD_VALID_COLOR = "";
$FIELD_ERROR_COLOR = "lightcoral";

//Regex
$SID_REGEX = "([S][0][0-9]+)";

//Student Table response messages
$INSERT_SUCCESS = "Student record inserted succesfully!";
$INSERT_FAIL = "Could not insert student record. Please try again.";

$EDIT_SUCCESS = "Student record was edited successfully!";
$EDIT_FAIL = "Student record edit could not be made. Please try agian later.";

$CLONE_SUCCESS = "Student record cloned successfully!";
$CLONE_FAIL = "Student record cloning failed. Please try again later.";

$DELETE_SUCCESS = "Student record deleted successfully!";
$DELETE_FAIL = "Student record deletion failed. Please try again later.";

//Gender Table response messages
$GENDER_INSERT_SUCCESS = "";
$GENDER_INSERT_FAIL = "";

$GENDER_EDIT_SUCCESS = "";
$GENDER_EDIT_FAIL = "";

$GENDER_DELETE_SUCCESS = "";
$GENDER_DELETE_FAIL = "";

//General form error messages
$ERR_DATABASE_UNAVAILABLE = "Invalid use of form/ DB is unavailable. Please try again.";
$ERR_NOT_SET = "Must not leave field blank.";
$ERR_LENGTH = "The value entered is either too short or too long.";
$ERR_NOT_STRING = "Only string values allowed. Please try again.";

//Student form error messages
$ERR_SID_FORMAT = "SID must begin with 'S0'. Please try again.";

//Student Form status response arrays
$ADD_STATUS = array("SUCCESS" => $INSERT_SUCCESS,
                        "FAIL" => $INSERT_FAIL,
                        "DB_FAIL" => $ERR_DATABASE_UNAVAILABLE);

$EDIT_STATUS = array("SUCCESS" => $EDIT_SUCCESS,
    "FAIL" => $EDIT_FAIL);

$CLONE_STATUS = array("SUCCESS" => $CLONE_SUCCESS,
    "FAIL" => $CLONE_FAIL);

$DELETE_STATUS = array("SUCCESS" => $DELETE_SUCCESS,
                        "FAIL" => $DELETE_FAIL);

//Gender Form status response arrays
$GENDER_ADD_STATUS = array("SUCCESS" => $GENDER_INSERT_SUCCESS,
                            "FAIL" => $GENDER_INSERT_FAIL,
                            "DB_FAIL" => $ERR_DATABASE_UNAVAILABLE);

$GENDER_EDIT_STATUS = array("SUCCESS" => $GENDER_EDIT_SUCCESS,
                            "FAIL" => $GENDER_EDIT_FAIL);

$GENDER_DELETE_STATUS = array("SUCCESS" => $GENDER_DELETE_SUCCESS,
                              "FAIL" => $GENDER_DELETE_FAIL);

//Student Form individual response array
$RESPONSE_TYPE = array(
    "sid" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING,
        "ERR_SID_FORMAT" => $ERR_SID_FORMAT),
    "firstname" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "lastname" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "address" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "major" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "gender" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "comments" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING)
);

//Gender Form individual response array
$GENDER_RESPONSE_TYPE = array(
    "gendername" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING),
    "description" => array("VALID" => "",
        "ERR_NOT_SET" => $ERR_NOT_SET,
        "ERR_LENGTH" => $ERR_LENGTH,
        "ERR_NOT_STRING" => $ERR_NOT_STRING)
);

//General form log messages
$LENGTH_LOG = "LENGTH OUT OF BOUNDS: Attribute entered in Student Form has length out of bounds.";
$STRING_LOG = "NOT A STRING: Attribute entered is not a string.";
$NOT_SET_LOG = "NOT SET: The student form was submitted without one or more required fields.";

//Student Table log messages
$INSERT_SUCCESS_LOG = "INSERT STUDENT: Successfully inserted student record.";
$INSERT_FAIL_LOG = "INSERT STUDENT: Student record insertion failed.";
$REGEX_LOG = "REGEX: SID regex failed.";

$EDIT_SUCCESS_LOG = "EDIT STUDENT: Success";
$EDIT_FAIL_LOG = "EDIT STUDENT: Fail";

$CLONE_SUCCESS_LOG = "CLONE STUDENT: Success";
$CLONE_FAIL_LOG = "CLONE STUDENT: Fail";

$DELETE_SUCCESS_LOG = "DELETE STUDENT: Success";
$DELETE_FAIL_LOG = "DELETE STUDENT: Fail";

//Gender Table log messages
$GENDER_INSERT_SUCCESS_LOG = "";
$GENDER_INSERT_FAIL_LOG = "";

$GENDER_EDIT_SUCCESS_LOG = "";
$GENDER_EDIT_FAIL_LOG = "";

$GENDER_DELETE_SUCCESS_LOG = "";
$GENDER_DELETE_FAIL_LOG = "";