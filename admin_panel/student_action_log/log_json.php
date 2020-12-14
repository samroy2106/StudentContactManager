<?php
require_once('../../php/startsession.php');
require_once('../../../config.php');
require_once('../../php/dbconnect.php');
require_once('../../php/log.php');
require_once('log_actions.php');
require_once('../../php/csrf_check.php');

global $servername, $dbname, $username, $password;

$start = microtime(true);

$conn = dbconnect($servername, $dbname, $username, $password);


//Perform CSRF check
if(!check_csrf($_GET['token'])) {
    logger("CSRF check failed.");
    //add_log_entry("csrf_log_json", "Warning");
}

//Check URL for 'draw' parameter
if(isset($_GET['draw'])) {
    $draw = filter_var($_GET['draw'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $draw = 1;
}

//Check URL for 'start' parameter (Number of records to offset by)
if(isset($_GET['start'])) {
    $start = filter_var($_GET['start'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $start = 0;
}

//Check URL for 'search' parameter
if(isset($_GET['search']['value'])) {
    $search = filter_var($_GET['search']['value'], FILTER_SANITIZE_SPECIAL_CHARS);
    $search = "%" . $search . "%";
} else {
    $search = '';
}

//Check URL for 'length' parameter (number of records to display per page)
if(isset($_GET['length'])) {
    $length = filter_var($_GET['length'], FILTER_SANITIZE_NUMBER_INT);
} else {
    $length = 15;
}

//Get all records without filtering
try {
    $records = $conn->prepare("SELECT COUNT(id) as all_logs FROM student_actions_log");
    $records->execute();
    $row = $records->fetch(PDO::FETCH_ASSOC);
    $all_records = $row['all_logs'];
} catch (PDOException $e) {
    logger("Log table is unavailable.");
    die();
}

//Get filtered (searched for) records
try {
    if($search != ''){
        $records = $conn->prepare("SELECT COUNT(id) as log_count FROM student_actions_log WHERE action_time LIKE '$search' OR requester LIKE '$search' OR ip_addr LIKE '$search' OR action LIKE '$search' OR details LIKE '$search'");
        $records->execute();
        $row = $records->fetch(PDO::FETCH_ASSOC);
        $filtered_records = $row['log_count'];
    } else {
        $filtered_records = $length;
    }
} catch (PDOException $e) {
    logger("Log table is unavailable.");
    die();
}

//Fetch data to display
if ($search != '') {
    $sql = $conn->prepare("SELECT id, action_time, requester, ip_addr, action, details FROM student_actions_log WHERE action_time LIKE '$search' OR requester LIKE '$search' OR ip_addr LIKE '$search' OR action LIKE '$search' OR details LIKE '$search' LIMIT $length OFFSET $start");
} else {
    $sql = $conn->prepare("SELECT id, action_time, requester, ip_addr, action, details FROM student_actions_log LIMIT $length OFFSET $start");
}

$sql->execute();

//Create data array
while($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    $log_array[] = array(
        'DT_RowId' => $row['id'],
        'id' => $row['id'],
        'action_time' => $row['action_time'],
        'requester' => $row['requester'],
        'ip_addr' => $row['ip_addr'],
        'action' => $row['action'],
        'details' => $row['details']
    );
}

$response = array(
    'draw' => intval($draw),
    'recordsTotal' => $all_records,
    'recordsFiltered' => $filtered_records,
    'data' => $log_array
);

//If gender array is NULL, set "" as the data in the response array
if(empty($log_array)) {
    $response['data'] = "";
}

//JSON encode data and echo to endpoint as datasource
echo json_encode($response);

$conn = null;

$end = microtime(true);
$render_time = ($end-$start);
//printf("Page rendered in %f seconds.", $render_time);