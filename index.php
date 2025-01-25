
<?php
SESSION_START();
// Check if the user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // Redirect the user based on their role
    if ($_SESSION['role'] == 'admin') {
        header("Location: pages/dashboard_admin.php");
        exit;
    } elseif ($_SESSION['role'] == 'staff') {
        header("Location: pages/dashboard_staff.php");
        exit;
    } elseif ($_SESSION['role'] == 'student') {
        header("Location: pages/dashboard_student.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login_styles.css">
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon"/>
</head>

<body>
<div class="login-card d-flex flex-lg-row flex-column">
    <!-- Welcome Section -->
    <div class="welcome-section col-lg-6 d-none d-lg-flex">
        <div class="text-center">
            <h1>Welcome to ULMS</h1>
            <p>Your portal for accessing all academic and library services efficiently.</p>
        </div>
    </div>

    <!-- Login Form Section -->
    <div class="login-form-section col-lg-6">
        <div class="form-container">
            <div class="text-center mb-3">
                <img src="images/logo.png" alt="Logo" style="width: 100px; height: auto;">
            </div>
            <h2 class="text-center" style="font-weight: bolder">Sign in</h2>
            <form method="POST" action="authenticate.php">
                <div class="mb-3">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <!-- Display error message -->
                <?php
                session_start();
                if (isset($_SESSION['error_message'])) {
                    echo "<div class='error-message'>" . $_SESSION['error_message'] . "</div>";
                    unset($_SESSION['error_message']); // Clear the error message after displaying
                }
                ?>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
        <div class="go-back">
            <a href="pages/important_contact.php" class="btn-minimal" >Important Contact</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>