<?php
session_start();

// Connect to database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Check connection
if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

// Fetch students from hod_pending_students
$sql = "SELECT id, stud_name, matric_num FROM hod_pending_students";
$result = mysqli_query($conn, $sql);

$students = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($students);

// Free result set and close the connection
mysqli_free_result($result);
mysqli_close($conn);
?>