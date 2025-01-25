<?php
session_start();
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../index.php");
    exit();
}

include '../includes/db_connection.php';

// Fetch the name of the logged-in admin
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard_styles.css">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>

<body style="background-color: #dae0e8;">
<!-- Include Navbar -->
<?php include '_navbar.php'; ?>

<div class="header">
    <h1>Welcome back!  <?php echo htmlspecialchars($name); ?></h1>
</div>

<div class="btn-container">
    <!-- Containers Section -->
    <div class="das-container">
        <a href="books_admin.php" class="box">Manage Book Catalogs</a>
        <a href="reservations_admin.php" class="box">View Student Reservations</a>
        <a href="fines_admin.php" class="box">View Student Overdues</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
