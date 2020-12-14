<?php
function dbconnect($servername, $dbname, $username, $password) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        //PDO error code to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $conn;
    }
    catch (PDOException $e) {
        echo "Database is unavailable.";
        die();
    }
}
