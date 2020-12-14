<?php

require_once('constants_config.php');

global $SID_LENGTH, $SID_REGEX, $MIN, $NAME_MAX, $ADDRESS_MAX, $MAJOR_MAX, $GENDER_MAX, $COMMENTS_MAX, $GENDER_DESCRIPTION_MAX, $FIELD_VALID_COLOR, $FIELD_ERROR_COLOR;

//Constants as datasource for JS
$constants = array(
"sid_length" => $SID_LENGTH,
"sid_regex" => $SID_REGEX,
"min" => $MIN,
"name_max" => $NAME_MAX,
"address_max" => $ADDRESS_MAX,
"major_max" => $MAJOR_MAX,
"gender_max" => $GENDER_MAX,
"comments_max" => $COMMENTS_MAX,
"gender_description_max" => $GENDER_DESCRIPTION_MAX,
"field_valid_color" => $FIELD_VALID_COLOR,
"field_error_color" => $FIELD_ERROR_COLOR
);

echo json_encode($constants);