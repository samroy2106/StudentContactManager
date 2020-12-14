<?php
require_once('../../config.php');


$student_details = array(
    array("Steven", "Curtis", "Trent", "Glen", "Kostas", "Jordan", "Jurgen", "Takumi", "Sadio", "Mohammed"),
    array("Gerrard", "Jones", "Alexander-Arnold", "Tsimikas", "Henderson", "Klopp", "Minamino", "Mane", "Salah"),
    array("123, ABC Street, Victoria BC", "456, DEF Avenue, Vancouver BC", "789, GHI Blvd, Quebec"),
    array("Physics", "Chemistry", "Mathematics", "Biology", "Computer Science"),
    array(1, 3, 5, 7, 9, 11, 13, 15, 17, 19)
);

//Prepare the insert statement
$insert = "INSERT INTO students(sid, firstname, lastname, address, major, gender_id) VALUES ";
$sid_file  = fopen("sid.txt", "r") or die("Unable to read from sid.txt.");

function create_tuple($insert, $sid_file, $student_details) {

    $separator = "', '";

    $insert.= "('";

    $sid = rtrim(fgets($sid_file));
    $insert.= $sid;
    $insert.= $separator;

    $firstname = $student_details[0][array_rand($student_details[0])];
    $insert.= $firstname;
    $insert.= $separator;

    $lastname = $student_details[1][array_rand($student_details[1])];
    $insert.= $lastname;
    $insert.= $separator;

    $address = $student_details[2][array_rand($student_details[2])];
    $insert.= $address;
    $insert.= $separator;

    $major = $student_details[3][array_rand($student_details[3])];
    $insert.= $major;
    $insert.= $separator;

    $gender_id = $student_details[4][array_rand($student_details[4])];
    $insert.= $gender_id;

    return $insert;
}

//Draw SID from file and a random value from each array to construct a tuple for insertion
//Append tuple to insert statement
for($i = 0; $i < 20000; $i++) {
    if($i != 19999) {
        $insert = create_tuple($insert, $sid_file, $student_details);
        $insert.= "'), ";
    } else {
        $insert = create_tuple($insert, $sid_file, $student_details);
        $insert.= "') ON DUPLICATE KEY UPDATE firstname=firstname, lastname=lastname, address=address, major=major, gender_id=gender_id;";
    }
}

fclose($sid_file);

//Test if statement is syntactically correct
//echo $insert;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //PDO error code to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Execute SQL statement
    $sql = $conn->prepare($insert);
    $sql->execute();

}
catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;