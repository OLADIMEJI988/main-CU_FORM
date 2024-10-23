<?php
session_start();

// Connect to database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Check connection
if (!$conn) {
    die('Connection error: ' . mysqli_connect_error());
}

// Insert initial data into hod_pending_students from recommendation_of_supervisors  
$insertSql = "INSERT INTO college_dean_pending_students (id, stud_name, matric_num, arrived_at) 
                SELECT id, stud_name, matric_num, endorsed_at 
                FROM pgcommittee_attended_students 
                WHERE pgcommittee_comment IS NOT NULL 
                AND (stud_name, matric_num) NOT IN 
                (SELECT stud_name, matric_num FROM college_dean_pending_students)
                AND (stud_name, matric_num) NOT IN 
                (SELECT stud_name, matric_num FROM college_dean_attended_students)
              ";

// Execute the insert query and check for errors
if (!mysqli_query($conn, $insertSql)) {
    error_log('Insert error: ' . mysqli_error($conn)); // Log any errors
}

// Fetch the students from the database, excluding those already in hod_pending_students and hod_attended_students
$sql = "SELECT id, stud_name, matric_num FROM pgcommittee_attended_students WHERE pgcommittee_comment IS NOT NULL";

$result = mysqli_query($conn, $sql);
$students = mysqli_fetch_all($result, MYSQLI_ASSOC);

$studentJs = json_encode($students);
$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch removed students from the hod_attended_students table
$removedStudents = [];
$removedSql = "SELECT stud_name, matric_num FROM college_dean_attended_students";
$removedResult = mysqli_query($conn, $removedSql);
if ($removedResult && mysqli_num_rows($removedResult) > 0) {
    while ($row = mysqli_fetch_assoc($removedResult)) {
        $removedStudents[] = $row;
    }
}
$removedStudentsJs = json_encode($removedStudents);

// Free result set and close the connection
mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>COLLEGE DEAN SECTION</title>
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css" />
  <link rel="stylesheet" href="./styles.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="preview-page">
  <img class="preview-logo" src="./img/CU_LOGO.jpg" alt="" />
  <div class="text-center">
    <p>Recommendation for appointment of supervisors</p>
    <p>(Masters Degree)</p>
  </div>
  <p class="title">Pending Students</p>
  <div class="row mt-4">
    <p class="col-1">S/N</p>
    <p class="col-4">Student Name</p>
    <p class="col-3">Matric Number</p>
    <p class="col-3">Action</p>
  </div>

  <script>
        const studentJs = <?php echo $studentJs ?>;
        const removedStudents = <?php echo $removedStudentsJs ?>;
        let students;

        // Fetch hod_pending_students if it exists
        $.ajax({
            url: 'fetch_collegeDean_pending_students.php',
            method: 'GET',
            success: function(collegeDeanPendingStudents) {
            if (collegeDeanPendingStudents && collegeDeanPendingStudents.length > 0) {
                students = collegeDeanPendingStudents; 
            } else {
                students = studentJs;
            }
            renderStudents();
            },
            error: function() {
            students = studentJs;
            renderStudents();
            }
        });

        const studId = <?php echo $student_id ?>;

        // Function to render students in the DOM
        function renderStudents() {
            const body = document.querySelector("body");
            
            // Clear existing students from the DOM
            const existingStudents = document.querySelectorAll('.holder');
            existingStudents.forEach(studentDiv => studentDiv.remove());

            if (studId > 0) {
            let removedStudent = null;
            students = students.filter((item) => {
                if (studId == item.id) {
                removedStudent = item; 
                return false;
                }
                return true; 
            });

            if (removedStudent) {
                // Send AJAX request to update the database
                $.ajax({
                url: 'save_removed_student.php',
                method: 'POST',
                data: { student: JSON.stringify(removedStudent) },
                success: function() {
                    // Removing the student from the hod pending students list after endorsement/rejection
                    $.ajax({
                    url: 'remove_student.php',
                    method: 'POST',
                    data: { student_id: studId },
                    success: function() {
                        renderStudents();
                    },
                    error: function() {
                        alert("Error removing student from hod_pending_students");
                    }
                    });
                },
                error: function() {
                    alert("Error processing action");
                }
                });
            }
            }

            // Render students in the DOM
            students.map((student, index) => {
            const studentDiv = document.createElement("div");
            const numField = document.createElement("p");
            const actionLink = document.createElement("a");
            const action = document.createElement("button");
            
            actionLink.className = "col-3";
            actionLink.href = `./collegeDeanEndorse.php?id=${student.id}`;
            action.className = "endorseBtn";
            action.textContent = "Click to endorse";
            
            numField.className = "col-1";
            studentDiv.className = "row mt-2 holder";
            
            const nameField = document.createElement("p");
            nameField.className = "stud-name-text col-4";
            
            const matricField = document.createElement("p");
            matricField.className = "stud-matric-text col-3";
            
            numField.textContent = ++index;
            nameField.textContent = student.stud_name;
            matricField.textContent = student.matric_num;

            actionLink.appendChild(action);
            studentDiv.appendChild(numField);
            studentDiv.appendChild(nameField);
            studentDiv.appendChild(matricField);
            studentDiv.appendChild(actionLink);
            body.append(studentDiv);
            });
        }
  </script>  

</body>
</html>