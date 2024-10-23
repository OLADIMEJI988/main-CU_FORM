<?php
session_start();

// Connect to database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Check connection
if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

$student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;

if ($student_id > 0) {
    $deleteSql = "DELETE FROM hod_pending_students WHERE id = $student_id";
    if (mysqli_query($conn, $deleteSql)) {
        echo "Student removed successfully";
    } else {
        echo "Error removing student: " . mysqli_error($conn);
    }
} else {
    echo "Invalid student ID";
}

if ($student_id > 0) {
    $deleteSql2 = "DELETE FROM pgcommittee_pending_students WHERE id = $student_id";
    if (mysqli_query($conn, $deleteSql2)) {
        echo "Student removed successfully";
    } else {
        echo "Error removing student: " . mysqli_error($conn);
    }
} else {
    echo "Invalid student ID";
}

if ($student_id > 0) {
    $deleteSql3 = "DELETE FROM college_dean_pending_students WHERE id = $student_id";
    if (mysqli_query($conn, $deleteSql3)) {
        echo "Student removed successfully";
    } else {
        echo "Error removing student: " . mysqli_error($conn);
    }
} else {
    echo "Invalid student ID";
}

mysqli_close($conn);
?>