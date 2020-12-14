<?php

require_once('../../../config.php');
require_once('../../php/log.php');
require_once('../../php/dbconnect.php');

if(isset($_GET['gender_id'])) {
    $current_id = filter_var($_GET['gender_id']);

    $json_response = get_gender_instances($current_id);

    echo $json_response;
}

function get_gender_instances($current_id) {

    global $servername, $dbname, $username, $password;

    $conn = dbconnect($servername, $dbname, $username, $password);

    $sql = $conn->prepare("SELECT COUNT(id) as gender_count FROM students WHERE gender_id = $current_id");
    $sql->execute();

    $conn = null;

    $result = $sql->fetchAll();

    $gender_count = $result[0]['gender_count'];
    $response = array(
        "gender_count" => $gender_count
    );

    $response = json_encode($response);
    return $response;
}