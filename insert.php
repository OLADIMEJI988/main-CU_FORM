<?php
// Connecting to the database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Checking connection
if (!$conn) {
  die('Connection error: ' . mysqli_connect_error());
}

// Getting the matric number from the URL
$student_matric_num = isset($_GET['matric_num']) ? mysqli_real_escape_string($conn, $_GET['matric_num']) : null;
$role = isset($_POST['role']) ? $_POST['role'] : null;

// Getting the posted data (comment and action)
$comment = isset($_POST['comment']) ? mysqli_real_escape_string($conn, $_POST['comment']) : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Ensuring that matric number, role, comment, and action are provided
if ($student_matric_num && !empty($comment) && !empty($action) && !empty($role)) {
    // Determining the action (endorsed or rejected)
    $status_action = ($action == 'endorsed') ? 'endorsed' : 'rejected';
     // Determining the action (approved or rejected)
    $dean_status_action = ($action == 'approved') ? 'approved' : 'rejected';

    // Handling for HOD role
    if ($role === 'hod') {
        // SQL to Insert into hod_attended_students based on matric number
        $sql_hod = "INSERT INTO hod_attended_students (id, stud_name, matric_num, hod_comment, hod_action, endorsed_at)
                    SELECT id, stud_name, matric_num, ?, ?, NOW()
                    FROM recommmendation_of_supervisors
                    WHERE matric_num = ?";

        if ($stmt = $conn->prepare($sql_hod)) {
            $stmt->bind_param("sss", $comment, $status_action, $student_matric_num);
            if ($stmt->execute()) {
                $stmt->close();

                // Insert into approval_status table
                $sql_approval = "INSERT INTO approval_status (stud_name, matric_num, HOD)
                                 SELECT stud_name, matric_num, ?
                                 FROM hod_attended_students
                                 WHERE hod_action = ? AND matric_num = ?";

                if ($stmt_approval = $conn->prepare($sql_approval)) {
                    $stmt_approval->bind_param("sss", $status_action, $status_action, $student_matric_num);
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
        // SQL to Insert into pgcommittee_attended_students based on matric number
        $sql_pgcommittee = "INSERT INTO pgcommittee_attended_students (id, stud_name, matric_num, pgcommittee_comment, pgcommittee_action, endorsed_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM hod_attended_students
                            WHERE matric_num = ?";

        if ($stmt_pg = $conn->prepare($sql_pgcommittee)) {
            $stmt_pg->bind_param("sss", $comment, $status_action, $student_matric_num);
            if ($stmt_pg->execute()) {
                $stmt_pg->close();

                // Update approval_status table
                $sql_approval_pg = "UPDATE approval_status
                                    SET college_pg_committee = ?
                                    WHERE matric_num = ?";

                if ($stmt_approval_pg = $conn->prepare($sql_approval_pg)) {
                    $stmt_approval_pg->bind_param("ss", $status_action, $student_matric_num);
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

    // Handling for College Dean role
    } elseif ($role === 'collegeDean') {
        // SQL to Insert into college_dean_attended_students based on matric number
        $sql_collegeDean = "INSERT INTO college_dean_attended_students (id, stud_name, matric_num, college_dean_comment, college_dean_action, endorsed_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM pgcommittee_attended_students
                            WHERE matric_num = ?";

        if ($stmt_collegedean = $conn->prepare($sql_collegeDean)) {
            $stmt_collegedean->bind_param("sss", $comment, $status_action, $student_matric_num);
            if ($stmt_collegedean->execute()) {
                $stmt_collegedean->close();

                // Update approval_status table
                $sql_approval_collegedean = "UPDATE approval_status
                                    SET college_dean = ?
                                    WHERE matric_num = ?";

                if ($stmt_approval_collegedean = $conn->prepare($sql_approval_collegedean)) {
                    $stmt_approval_collegedean->bind_param("ss", $status_action, $student_matric_num);
                    if ($stmt_approval_collegedean->execute()) {
                        echo 'Success: Data inserted into both college_dean_attended_students and approval_status tables';
                    } else {
                        echo 'Error in updating approval_status: ' . $stmt_approval_collegedean->error;
                    }
                    $stmt_approval_collegedean->close();
                } else {
                    echo 'Error preparing approval_status update statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into college_dean_attended_students: ' . $stmt_collegedean->error;
            }
        } else {
            echo 'Error preparing college_dean_attended_students statement: ' . $conn->error;
        }
      
    // Handling for Sub-dean role    
    } elseif ($role === 'subDean') {
        // SQL to Insert into sub_dean_attended_students based on matric number
        $sql_subDean = "INSERT INTO sub_dean_attended_students (id, stud_name, matric_num, sub_dean_comment, sub_dean_action, endorsed_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM college_dean_attended_students
                            WHERE matric_num = ?";

        if ($stmt_subdean = $conn->prepare($sql_subDean)) {
            $stmt_subdean->bind_param("sss", $comment, $status_action, $student_matric_num);
            if ($stmt_subdean->execute()) {
        
                $stmt_subdean->close();

                // Update approval_status table
                $sql_approval_subdean = "UPDATE approval_status
                                    SET sub_dean = ?
                                    WHERE matric_num = ?";

                if ($stmt_approval_subdean = $conn->prepare($sql_approval_subdean)) {
                    $stmt_approval_subdean->bind_param("ss", $status_action, $student_matric_num);
                    if ($stmt_approval_subdean->execute()) {
                        echo 'Success: Data inserted into both sub_dean_attended_students and approval_status tables';
                    } else {
                        echo 'Error in updating approval_status: ' . $stmt_approval_subdean->error;
                    }
                    $stmt_approval_subdean->close();
                } else {
                    echo 'Error preparing approval_status update statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into sub_dean_attended_students: ' . $stmt_subdean->error;
            }
        } else {
            echo 'Error preparing sub_dean_attended_students statement: ' . $conn->error;
        }

    // Handling for Dean role    
    } elseif ($role === 'dean') {
        // SQL to Insert into dean_attended_students based on matric number
        $sql_dean = "INSERT INTO dean_attended_students (id, stud_name, matric_num, dean_comment, dean_action, approved_at)
                            SELECT id, stud_name, matric_num, ?, ?, NOW()
                            FROM sub_dean_attended_students
                            WHERE matric_num = ?";

        if ($stmt_dean = $conn->prepare($sql_dean)) {
            $stmt_dean->bind_param("sss", $comment, $dean_status_action, $student_matric_num);
            if ($stmt_dean->execute()) {
                
                $stmt_dean->close();

                // Update approval_status table
                $sql_approval_dean = "UPDATE approval_status
                                    SET dean = ?
                                    WHERE matric_num = ?";

                if ($stmt_approval_dean = $conn->prepare($sql_approval_dean)) {
                    $stmt_approval_dean->bind_param("ss", $dean_status_action, $student_matric_num);
                    if ($stmt_approval_dean->execute()) {
                        echo 'Success: Data inserted into both dean_attended_students and approval_status tables';
                    } else {
                        echo 'Error in updating approval_status: ' . $stmt_approval_dean->error;
                    }
                    $stmt_approval_dean->close();
                } else {
                    echo 'Error preparing approval_status update statement: ' . $conn->error;
                }
            } else {
                echo 'Error in inserting into dean_attended_students: ' . $stmt_subdean->error;
            }
        } else {
            echo 'Error preparing dean_attended_students statement: ' . $conn->error;
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