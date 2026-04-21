<?php
$conn = new mysqli("localhost", "root", "", "student_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$day = $_POST['day'];
$month = $_POST['month'];
$year = $_POST['year'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$gender = $_POST['gender'];
$address = $_POST['address'];
$city = $_POST['city'];
$pin = $_POST['pin'];
$state = $_POST['state'];
$country = $_POST['country'];

// Convert hobbies array to string
$hobbies = "";

// Normal hobbies
if(isset($_POST['hobbies'])){
    $hobbies = implode(",", $_POST['hobbies']);
}

// Add "Other" hobby if typed
if(!empty($_POST['other_hobby'])){
    if(!empty($hobbies)){
        $hobbies .= ",";
    }
    $hobbies .= $_POST['other_hobby'];
}

$course = $_POST['course'];
$board_x = $_POST['board_x'];
$percent_x = $_POST['percent_x'];
$year_x = $_POST['year_x'];

$board_xii = $_POST['board_xii'];
$percent_xii = $_POST['percent_xii'];
$year_xii = $_POST['year_xii'];

$board_grad = $_POST['board_grad'];
$percent_grad = $_POST['percent_grad'];
$year_grad = $_POST['year_grad'];

$board_master = $_POST['board_master'];
$percent_master = $_POST['percent_master'];
$year_master = $_POST['year_master'];
// Insert query
$sql = "INSERT INTO students 
(first_name, last_name, day, month, year, email, mobile, gender, address, city, pin, state, country, hobbies, course)
VALUES 
('$first_name','$last_name','$day','$month','$year','$email','$mobile','$gender','$address','$city','$pin','$state','$country','$hobbies','$course')";

if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
