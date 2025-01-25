<?php
session_start();
include '../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

$base_fine = 20; // Fine amount per overdue day
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fines</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">

<!-- Navbar -->
<?php include '_navbar.php'; ?>

<div class="container my-4">
    <div class="glass p-4">
        <h1 class="text-center">Fines</h1>

        <?php
        $query = "
            SELECT b.title AS book_title, 
                   r.due_date, 
                   DATEDIFF(CURDATE(), r.due_date) AS overdue_days,
                   CASE 
                       WHEN DATEDIFF(CURDATE(), r.due_date) > 0 
                       THEN (DATEDIFF(CURDATE(), r.due_date) * $base_fine)
                       ELSE 0
                   END AS fine
            FROM reservations r
            JOIN books b ON r.book_id = b.book_id
            WHERE r.user_id = '$user_id' AND r.returned = 'No'
            ORDER BY fine DESC";

        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "
            <div class='table-container'>
                <table>
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Due Date</th>
                            <th>Overdue Days</th>
                            <th>Fine (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
                echo "<td>" . ($row['overdue_days'] > 0 ? htmlspecialchars($row['overdue_days']) : "0") . "</td>";
                echo "<td>" . htmlspecialchars($row['fine']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody>
                </table>
            </div>";
        } else {
            echo "<div class='alert alert-info'>You have no overdue books or fines.</div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>