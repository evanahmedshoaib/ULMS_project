<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header("Location: ../index.php");
    exit();
}

$base_fine = 20; // Fine amount per overdue day
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Fines</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">

<!-- Navbar -->
<?php include '_navbar.php'; ?>

<div class="container my-4">
    <div class="glass p-4">
        <h1 class="text-center">Fine Summary</h1>

        <?php
        $query = "
            SELECT u.user_id AS student_id,
                   u.name AS student_name, 
                   b.title AS book_title, 
                   r.due_date, 
                   DATEDIFF(CURDATE(), r.due_date) AS overdue_days,
                   (DATEDIFF(CURDATE(), r.due_date) * $base_fine) AS fine
            FROM users u
            JOIN reservations r ON u.user_id = r.user_id
            JOIN books b ON r.book_id = b.book_id
            WHERE r.returned = 'No' AND r.due_date < CURDATE()
            ORDER BY r.due_date ASC";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "
            <div class='table-container'>
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Book Title</th>
                            <th>Due Date</th>
                            <th>Overdue Days</th>
                            <th>Fine (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['overdue_days']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fine']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody>
                </table>
            </div>";
        } else {
            echo "<div class='alert alert-info'>No overdue books or fines found.</div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>