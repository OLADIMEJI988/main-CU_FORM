<?php
// Connecting to the database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Checking connection
if (!$conn) {
  die('Connection error: ' . mysqli_connect_error());
}

// Checking if the student ID and role are provided
$student_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$role = isset($_POST['role']) ? $_POST['role'] : null;

// Getting the posted data (comment and action)
$comment = isset($_POST['comment']) ? mysqli_real_escape_string($conn, $_POST['comment']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Ensure that student ID, role, comment, and action are provided
if ($student_id && !empty($comment) && !empty($action) && !empty($role)) {
    // Determine the action (endorsed or rejected)
    $status_action = ($action == 'endorsed') ? 'endorsed' : 'rejected';
    
    // Handling for HOD role
    if ($role === 'hod') {
        // First SQL: Insert into hod_attended_students
        $sql_hod = "INSERT INTO hod_attended_students (id, stud_name, matric_num, hod_comment, hod_action, endorsed_at)
                    SELECT id, stud_name, matric_num, ?, ?, NOW()
                    FROM recommmendation_of_supervisors
                    WHERE id = ?";

        if ($stmt = $conn->prepare($sql_hod)) {
            $stmt->bind_param("ssi", $comment, $status_action, $student_id);
            if ($stmt->execute()) {
                // Success: Insertion into hod_attended_students, now update approval_status
                $stmt->close();

                $sql_approval = "INSERT INTO approval_status (stud_name, matric_num, HOD)
                                 SELECT stud_name, matric_num, ?
                                 FROM hod_attended_students
                                 WHERE hod_attended_students.hod_action = ? 
                                 AND id = ?";

                if ($stmt_approval = $conn->prepare($sql_approval)) {
                    $stmt_approval->bind_param("ssi", $status_action, $status_action, $student_id);
                    if ($stmt_approval->execute()) {
                        echo 'Success: Data inserted into both hod_attended_students and approval_status tables';
                    } else {
                        echo 'Error in inserting into approval_status: ' . $stmt_approval->error;
                    }
                    $stmt_approval->close();
                } else {
                    echo 'Error preparing approval_status statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into hod_attended_students: ' . $stmt->error;
            }
        } else {
            echo 'Error preparing hod_attended_students statement: ' . $conn->error;
        }

    // Handling for PG Committee role
    } elseif ($role === 'collegePG') {
        // First SQL: Insert into pgcommittee_attended_students
        $sql_pgcommittee = "INSERT INTO pgcommittee_attended_students (id, stud_name, matric_num, pgcommittee_comment, pgcommittee_action, endorsed_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM hod_attended_students
                            WHERE id = ?";

        if ($stmt_pg = $conn->prepare($sql_pgcommittee)) {
            $stmt_pg->bind_param("ssi", $comment, $status_action, $student_id);
            if ($stmt_pg->execute()) {
                // Success: Insertion into pgcommittee_attended_students, now update approval_status
                $stmt_pg->close();

                $sql_approval_pg = "UPDATE approval_status
                                    SET college_pg_committee = ?
                                    WHERE stud_name = (SELECT stud_name FROM hod_attended_students WHERE id = ?)";

                if ($stmt_approval_pg = $conn->prepare($sql_approval_pg)) {
                    $stmt_approval_pg->bind_param("si", $status_action, $student_id);
                    if ($stmt_approval_pg->execute()) {
                        echo 'Success: Data inserted into both pgcommittee_attended_students and approval_status tables';
                    } else {
                        echo 'Error in updating approval_status: ' . $stmt_approval_pg->error;
                    }
                    $stmt_approval_pg->close();
                } else {
                    echo 'Error preparing approval_status update statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into pgcommittee_attended_students: ' . $stmt_pg->error;
            }
        } else {
            echo 'Error preparing pgcommittee_attended_students statement: ' . $conn->error;
        }
    } else if ($role === 'collegeDean') {
        // First SQL: Insert into college_dean_attended_students
        $sql_collegeDean = "INSERT INTO college_dean_attended_students (id, stud_name, matric_num, college_dean_comment, college_dean_action, endorsed_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM pgcommittee_attended_students
                            WHERE id = ?";

        if ($stmt_pg = $conn->prepare($sql_collegeDean)) {
            $stmt_pg->bind_param("ssi", $comment, $status_action, $student_id);
            if ($stmt_pg->execute()) {
                // Success: Insertion into college_dean_attended_students, now update approval_status
                $stmt_pg->close();

                $sql_approval_pg = "UPDATE approval_status
                                    SET college_dean_comment = ?
                                    WHERE stud_name = (SELECT stud_name FROM pgcommittee_attended_students WHERE id = ?)";

                if ($stmt_approval_pg = $conn->prepare($sql_approval_pg)) {
                    $stmt_approval_pg->bind_param("si", $status_action, $student_id);
                    if ($stmt_approval_pg->execute()) {
                        echo 'Success: Data inserted into both pgcommittee_attended_students and approval_status tables';
                    } else {
                        echo 'Error in updating approval_status: ' . $stmt_approval_pg->error;
                    }
                    $stmt_approval_pg->close();
                } else {
                    echo 'Error preparing approval_status update statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into college_dean_attended_students: ' . $stmt_pg->error;
            }
        } else {
            echo 'Error preparing college_dean_attended_students statement: ' . $conn->error;
        }
    } else {
        echo 'Invalid role provided.';
    }
} else {
    echo 'Invalid input: Missing required fields.';
}

// Closing database connection
mysqli_close($conn);
?>