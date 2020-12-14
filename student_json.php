<?php
require_once('php/startsession.php');
require_once('../config.php');
require_once('php/dbconnect.php');
require_once('php/log.php');
require_once('admin_panel/student_action_log/log_actions.php');
require_once('php/csrf_check.php');

global $servername, $dbname, $username, $password;

$start = microtime(true);

$conn = dbconnect($servername, $dbname, $username, $password);

//logger("Student Table Datasource: " . implode(" ", $_GET));

//Perform CSRF check
if(!check_csrf($_GET['token'])) {
    logger("CSRF check failed.");
    //add_log_entry("csrf_student_json", "Warning");
}

//Check URL for 'draw' parameter
if(isset($_GET['draw'])) {
    $draw = filter_var($_GET['draw'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $draw = 1;
}

//echo var_dump($draw);

//Check URL for 'start' parameter (Number of records to offset by)
if(isset($_GET['start'])) {
    $start = filter_var($_GET['start'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $start = 0;
}

//echo var_dump($start);

//Check URL for 'search' parameter
if(isset($_GET['search']['value'])) {
    $search = filter_var($_GET['search']['value'], FILTER_SANITIZE_SPECIAL_CHARS);
    $search = "%" . $search . "%";
} else {
    $search = '';
}

//echo var_dump($search);

//Check URL for 'length' parameter (number of records to display per page)
if(isset($_GET['length'])) {
    $length = filter_var($_GET['length'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $length = 15;
}

//echo var_dump($length);

//Get all records without filtering
try {
    $records = $conn->prepare("SELECT COUNT(sid) as all_students FROM student_view");
    $records->execute();
    $row = $records->fetch(PDO::FETCH_ASSOC);
    $all_records = $row['all_students'];
} catch (PDOException $e) {
    logger("Student database is unavailable.");
    die();
}

//echo var_dump($all_records);

//Get filtered (searched for) records
try {
    if($search != ''){
        $records = $conn->prepare("SELECT COUNT(id) as student_count FROM student_view WHERE sid LIKE '$search' OR firstname LIKE '$search' OR lastname LIKE '$search' OR address LIKE '$search' OR major LIKE '$search' OR gender LIKE '$search' OR comments LIKE '$search'");
        $records->execute();
        $row = $records->fetch(PDO::FETCH_ASSOC);
        $filtered_records = $row['student_count'];
    } else {
        $filtered_records = $length;
    }
} catch (PDOException $e) {
    logger("Student database is unavailable.");
    die();
}

//echo var_dump($filtered_records);

//Fetch data to display
if ($search != '') {
    $sql = $conn->prepare("SELECT id, sid, firstname, lastname, address, major, gender, comments FROM student_view WHERE sid LIKE '$search' OR firstname LIKE '$search' OR lastname LIKE '$search' OR address LIKE '$search' OR major LIKE '$search' OR gender LIKE '$search' OR comments LIKE '$search' LIMIT $length OFFSET $start");
} else {
    $sql = $conn->prepare("SELECT id, sid, firstname, lastname, address, major, gender, comments FROM student_view LIMIT $length OFFSET $start");
}

$sql->execute();

//Create data array
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $name_array[] = array(
        'DT_RowId' => $row['id'],
        'sid' => $row['sid'],
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'address' => $row['address'],
        'major' => $row['major'],
        'gender' => $row['gender'],
        'comments' => $row['comments'],
        'operation_id' => $row['id']
    );
}

$response = array(
    'draw' => intval($draw),
    'recordsTotal' => $all_records,
    'recordsFiltered' => $filtered_records,
    'data' => $name_array
);

//If name array is NULL, set "" as the data in the response array
if(empty($name_array)) {
    $response['data'] = "";
}

//JSON encode data and echo to endpoint as datasource
echo json_encode($response);

$conn = null;

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);
