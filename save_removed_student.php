<?php
// Database connection
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Check connection
if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

// Get student data from POST request
$student = json_decode($_POST['student'], true);

// Validate that student data is received
if (!isset($student['name']) || !isset($student['matric_number'])) {
    die('Invalid student data.');
}

// First query: Update the hod_attended_students table
$sql_hod = "UPDATE hod_attended_students 
            SET endorsed_at = NOW() 
            WHERE stud_name = ? AND matric_num = ?";

if ($stmt = $conn->prepare($sql_hod)) {
    $stmt->bind_param("ss", $student['name'], $student['matric_number']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Student updated successfully in hod_attended_students.\n";
    } else {
        echo "Error updating student in hod_attended_students or no rows affected.\n";
    }

    $stmt->close();
} else {
    echo "Error preparing hod_attended_students statement: " . $conn->error . "\n";
}

// Second query: Update the pgcommittee_attended_students table
$sql_pg = "UPDATE pgcommittee_attended_students 
           SET endorsed_at = NOW() 
           WHERE stud_name = ? AND matric_num = ?";

if ($stmt = $conn->prepare($sql_pg)) {
    $stmt->bind_param("ss", $student['name'], $student['matric_number']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Student updated successfully in pgcommittee_attended_students.\n";
    } else {
        echo "Error updating student in pgcommittee_attended_students or no rows affected.\n";
    }

    $stmt->close();
} else {
    echo "Error preparing pgcommittee_attended_students statement: " . $conn->error . "\n";
}

// Third query: Update the college_dean_attended_students table
$sql_collegeDean = "UPDATE college_dean_attended_students 
           SET endorsed_at = NOW() 
           WHERE stud_name = ? AND matric_num = ?";

if ($stmt = $conn->prepare($sql_collegeDean)) {
    $stmt->bind_param("ss", $student['name'], $student['matric_number']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Student updated successfully in college_dean_attended_students.\n";
    } else {
        echo "Error updating student in college_dean_attended_students or no rows affected.\n";
    }

    $stmt->close();
} else {
    echo "Error preparing college_dean_attended_students statement: " . $conn->error . "\n";
}

// Fourth query: Update the college_dean_attended_students table
$sql_subDean = "UPDATE sub_dean_attended_students 
           SET endorsed_at = NOW() 
           WHERE stud_name = ? AND matric_num = ?";

if ($stmt = $conn->prepare($sql_subDean)) {
    $stmt->bind_param("ss", $student['name'], $student['matric_number']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Student updated successfully in sub_dean_attended_students.\n";
    } else {
        echo "Error updating student in sub_dean_attended_students or no rows affected.\n";
    }

    $stmt->close();
} else {
    echo "Error preparing sub_dean_attended_students statement: " . $conn->error . "\n";
}

// Close database connection
$conn->close();
?>