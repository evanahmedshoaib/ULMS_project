<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../includes/db_connection.php';

$alertMessage = '';
$alertType = '';

// Handle Add User
if (isset($_POST['add_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Check if user_id already exists
    $checkQuery = "SELECT * FROM users WHERE user_id='$user_id'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        $alertMessage = "Error: User ID already exists!";
        $alertType = "danger";
    } else {
        $query = "INSERT INTO users (user_id, name, email, password, role) VALUES ('$user_id', '$name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $alertMessage = "User added successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Error adding user: " . mysqli_error($conn);
            $alertType = "danger";
        }
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $original_user_id = $_POST['original_user_id'];
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $query = "UPDATE users SET user_id='$user_id', name='$name', email='$email', password='$password', role='$role' WHERE user_id='$original_user_id'";
    if (mysqli_query($conn, $query)) {
        $alertMessage = "User updated successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error updating user: " . mysqli_error($conn);
        $alertType = "danger";
    }
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['original_user_id'];
    $query = "DELETE FROM users WHERE user_id='$user_id'";
    if (mysqli_query($conn, $query)) {
        $alertMessage = "User deleted successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error deleting user: " . mysqli_error($conn);
        $alertType = "danger";
    }
}

// Handle Search and Filter
$searchQuery = '';
$roleFilter = '';
$genres_result = mysqli_query($conn, "SELECT DISTINCT role FROM users");

if (isset($_POST['search'])) {
    $searchQuery = $_POST['search_query'];
    $roleFilter = $_POST['role_filter'];

    $searchQuery = mysqli_real_escape_string($conn, $searchQuery);
    $roleFilter = mysqli_real_escape_string($conn, $roleFilter);

    $sql = "SELECT * FROM users WHERE 
            (user_id LIKE '%$searchQuery%' OR name LIKE '%$searchQuery%' OR email LIKE '%$searchQuery%')";

    if ($roleFilter != '') {
        $sql .= " AND role='$roleFilter'";
    }

    $searchResults = mysqli_query($conn, $sql);
} else {
    $searchResults = mysqli_query($conn, "SELECT * FROM users");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">

<!-- Navbar -->
<?php include '_navbar.php'; ?>

<div class="container my-4">
    <!-- Popup Alert -->
    <?php if ($alertMessage): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo $alertMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Add User Section -->
    <div class="glass p-4 mb-4">
        <h2 class="text-center">Add User</h2>
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="user_id" class="form-label">User ID</label>
                <input type="number" class="form-control" name="user_id" required>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
                <label for="role" class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Library Staff</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary" name="add_user">Add User</button>
            </div>
        </form>
    </div>

    <!-- User List Section -->
    <div class="glass p-4">
        <h3 class="text-center">Existing Users</h3>

        <!-- Search and Filter Section -->
        <div class="p-4 ">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search_query" placeholder="Search by user ID, name, or email" value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                <div class="col-md-4">
                    <select name="role_filter" class="form-select" aria-label="Filter by role">
                        <option value="">All Roles</option>
                        <?php while ($genre = mysqli_fetch_assoc($genres_result)): ?>
                            <option value="<?php echo htmlspecialchars($genre['role']); ?>"
                                <?php echo isset($_POST['role_filter']) && $_POST['role_filter'] === $genre['role'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($genre['role']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button type="submit" name="search" class="btn btn-minimal">Search</button>
                </div>
            </form>
        </div>

        <!-- Table displaying the users -->
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($searchResults)) {
                    echo "<tr>
                                <form method='POST'>
                                    <td>
                                        <input type='text' class='form-control border-0' name='user_id' value='{$row['user_id']}' required>
                                        <input type='hidden' name='original_user_id' value='{$row['user_id']}'>
                                    </td>
                                    <td><input type='text' class='form-control border-0' name='name' value='{$row['name']}' required></td>
                                    <td><input type='text' class='form-control border-0' name='email' value='{$row['email']}' required></td>
                                    <td><input type='text' class='form-control border-0' name='password' value='{$row['password']}' required></td>
                                    <td>
                                        <select name='role' class='form-select border-0' required>
                                            <option value='admin' " . ($row['role'] === 'admin' ? 'selected' : '') . ">Admin</option>
                                            <option value='staff' " . ($row['role'] === 'staff' ? 'selected' : '') . ">Library Staff</option>
                                            <option value='student' " . ($row['role'] === 'student' ? 'selected' : '') . ">Student</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type='submit' class='btn-minimal' name='update_user'>Update</button>
                                        <button type='submit' class='btn-minimal' name='delete_user'>Delete</button>
                                    </td>
                                </form>
                            </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
