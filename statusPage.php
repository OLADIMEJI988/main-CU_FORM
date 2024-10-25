<?php
// Connecting to the database
$conn = mysqli_connect('localhost', 'sholanke', 'shinnely_JR1', 'recommend_supe');

// Checking connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to fetch student approval statuses
$sql = "SELECT stud_name, matric_num, HOD, college_pg_committee, college_dean, sub_dean, dean 
        FROM approval_status";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STATUS</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.css" />
    <link rel="stylesheet" href="./styles.css" />

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>

</head>
<body>
    <img class="preview-logo mt-3" src="./img/CU_LOGO.jpg" alt="" />
    <div class="text-center">
      <p>Approval Status of Recommendation for appointment of supervisors</p>
      <p>(Masters Degree)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th colspan="2">Student Details</th>
                <th colspan="5">Approval Status</th>
            </tr>
            <tr>
                <th>Student Name</th>
                <th>Matric Number</th>
                <th>HOD</th>
                <th>College PG Committee</th>
                <th>College Dean</th>
                <th>Sub-Dean</th>
                <th>Dean</th>
            </tr>
        </thead>
        <tbody>

            <?php
                if (mysqli_num_rows($result) > 0) {
                    // Fetch and display
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['stud_name'] . "</td>";
                        echo "<td>" . $row['matric_num'] . "</td>";
                        echo "<td>" . $row['HOD'] . "</td>";
                        echo "<td>" . $row['college_pg_committee'] . "</td>";
                        echo "<td>" . $row['college_dean'] . "</td>";
                        echo "<td>" . $row['sub_dean'] . "</td>";
                        echo "<td>" . $row['dean'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No records found</td></tr>";
                }
            ?>

        </tbody>
    </table>

    <?php
        // Close the database connection
        mysqli_close($conn);
    ?>

</body>

<script src="./form.js"></script>

</html>