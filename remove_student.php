<?php
session_start();

// Connect to database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Check connection
if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

$matric_number = isset($_POST['matric_num']) ? mysqli_real_escape_string($conn, $_POST['matric_num']) : '';

if (!empty($matric_number)) {
    // Delete from hod_pending_students
    $deleteSql = "DELETE FROM hod_pending_students WHERE matric_num = '$matric_number'";
    if (mysqli_query($conn, $deleteSql)) {
        echo "Student removed from HOD pending list successfully.";
    } else {
        echo "Error removing student from HOD pending list: " . mysqli_error($conn);
    }

    // Delete from pgcommittee_pending_students
    $deleteSql2 = "DELETE FROM pgcommittee_pending_students WHERE matric_num = '$matric_number'";
    if (mysqli_query($conn, $deleteSql2)) {
        echo "Student removed from PG Committee pending list successfully.";
    } else {
        echo "Error removing student from PG Committee pending list: " . mysqli_error($conn);
    }

    // Delete from college_dean_pending_students
    $deleteSql3 = "DELETE FROM college_dean_pending_students WHERE matric_num = '$matric_number'";
    if (mysqli_query($conn, $deleteSql3)) {
        echo "Student removed from College Dean pending list successfully.";
    } else {
        echo "Error removing student from College Dean pending list: " . mysqli_error($conn);
    }

    // Delete from sub_dean_pending_students
    $deleteSql4 = "DELETE FROM sub_dean_pending_students WHERE matric_num = '$matric_number'";
    if (mysqli_query($conn, $deleteSql4)) {
        echo "Student removed from Sub Dean pending list successfully.";
    } else {
        echo "Error removing student from Sub Dean pending list: " . mysqli_error($conn);
    }

    // Delete from dean_pending_students
    $deleteSql5 = "DELETE FROM dean_pending_students WHERE matric_num = '$matric_number'";
    if (mysqli_query($conn, $deleteSql5)) {
        echo "Student removed from Dean pending list successfully.";
    } else {
        echo "Error removing student from Dean pending list: " . mysqli_error($conn);
    }

} else {
    echo "Invalid matric number";
}

mysqli_close($conn);
?>