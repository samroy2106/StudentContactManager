<?php
//require_once('../../../config.php');
//require_once('../../php/dbconnect.php');
//require_once('../../php/log.php');

//Record start time to measure page render time later
$start = microtime(true);

function add_log_entry($action, $details) {

    $log_details = create_log_details_array($action, $details);

    $action_time = $log_details['action_time'];
    $requester = $log_details['requester'];
    $ip_addr = $log_details['ip_addr'];
    $action = $log_details['action'];
    $details = $log_details['details'];

    //logger("log details: ".$action_time."----".implode(", ", $requester)."----".$ip_addr."----".$action."----".$details);

    $status = insert_log($action_time, $requester, $ip_addr, $action, $details);

    if($status == true) {
        $response = array("status" => "SUCCESS");
    } else {
        $response = array("status" => "FAIL");
    }

    $json_response = json_encode($response);
    //echo $json_response;
}

function insert_log($action_time, $requester, $ip_addr, $action, $details) {

    global $servername, $dbname, $username, $password;

    //Connect to the database
    $conn = dbconnect($servername, $dbname, $username, $password);

    try{
        $sql = $conn->prepare("INSERT INTO student_actions_log(action_time, requester, ip_addr, action, details) VALUES (:action_time, :requester, :ip_addr, :action, :details)");
        $sql->execute(array(':action_time' => $action_time,
                            ':requester' => $requester,
                            ':ip_addr' => $ip_addr,
                            ':action' => $action,
                            ':details' => $details));

        $status = true;
    } catch (PDOException $e) {
        $status = false;
    }

    $conn = null;
    return $status;
}

function create_log_details_array($action, $details) {

    $action_time = date("Y-m-d h:i:s");
    $requester = $_SERVER['REMOTE_USER'];
    $ip_addr = get_ip_address();

    $log_details = array("action_time" => $action_time,
                         "requester" => $requester,
                         "ip_addr" => $ip_addr,
                         "action" => $action,
                         "details" => $details);

    return $log_details;
}

function get_ip_address() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}