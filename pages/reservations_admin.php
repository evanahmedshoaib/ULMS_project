<?php
session_start();

// Include the database connection
include '../includes/db_connection.php';

$alertMessage = ''; // Variable to hold alert message
$alertType = '';    // Variable to hold alert type (success or danger)

// Check if the user is logged in and is an admin or staff
if (!isset($_SESSION['user_id'])  || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header("Location: ../index.php");
    exit();
}

// Handle Update Reservation Status
if (isset($_POST['update_reservation'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];

    // Fetch the book ID associated with the reservation
    $reservation_query = "SELECT book_id FROM reservations WHERE reservation_id = '$reservation_id'";
    $reservation_result = mysqli_query($conn, $reservation_query);

    if ($reservation_row = mysqli_fetch_assoc($reservation_result)) {
        $book_id = $reservation_row['book_id'];

        // Update the reservation status
        $update_reservation_query = "UPDATE reservations SET returned='$status' WHERE reservation_id='$reservation_id'";
        $update_reservation_result = mysqli_query($conn, $update_reservation_query);

        if ($update_reservation_result) {
            // Update the `books` table based on the returned status
            if ($status === 'Yes') {
                // If returned, make the book available
                $update_book_query = "UPDATE books SET reserved='No', reserved_by=NULL WHERE book_id='$book_id'";
            } else {
                // If not returned, keep it reserved by the current user
                $update_book_query = "UPDATE books SET reserved='Yes', reserved_by=(SELECT user_id FROM reservations WHERE reservation_id='$reservation_id') WHERE book_id='$book_id'";
            }

            if (mysqli_query($conn, $update_book_query)) {
                $alertMessage = "Reservation and book status updated successfully!";
                $alertType = "success";
            } else {
                $alertMessage = "Error updating book status: " . mysqli_error($conn);
                $alertType = "danger";
            }
        } else {
            $alertMessage = "Error updating reservation: " . mysqli_error($conn);
            $alertType = "danger";
        }
    } else {
        $alertMessage = "Error fetching reservation details.";
        $alertType = "danger";
    }
}

// Handle Delete Reservation
if (isset($_POST['delete_reservation']) && $_SESSION['role'] === 'admin') {
    $reservation_id = $_POST['reservation_id'];

    // Fetch the book ID associated with the reservation
    $reservation_query = "SELECT book_id FROM reservations WHERE reservation_id = '$reservation_id'";
    $reservation_result = mysqli_query($conn, $reservation_query);

    if ($reservation_row = mysqli_fetch_assoc($reservation_result)) {
        $book_id = $reservation_row['book_id'];

        // Delete the reservation
        $delete_reservation_query = "DELETE FROM reservations WHERE reservation_id = '$reservation_id'";
        $delete_reservation_result = mysqli_query($conn, $delete_reservation_query);

        if ($delete_reservation_result) {
            // Update the `books` table to make the book available
            $update_book_query = "UPDATE books SET reserved='No', reserved_by=NULL WHERE book_id='$book_id'";
            if (mysqli_query($conn, $update_book_query)) {
                $alertMessage = "Reservation deleted successfully!";
                $alertType = "success";
            } else {
                $alertMessage = "Error updating book status: " . mysqli_error($conn);
                $alertType = "danger";
            }
        } else {
            $alertMessage = "Error deleting reservation: " . mysqli_error($conn);
            $alertType = "danger";
        }
    } else {
        $alertMessage = "Error fetching reservation details.";
        $alertType = "danger";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">

<!-- Include Navbar -->
<?php include '_navbar.php'; ?>

<div class="container my-4">
    <!-- Popup Alert -->
    <?php if ($alertMessage): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo $alertMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="glass p-9">
        <h2 class="text-center">Manage Reservations</h2>
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>Book ID</th>
                    <th>Book Title</th>
                    <th>User ID</th>
                    <th>Reservation Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Fetch all reservations
                $query = "SELECT r.reservation_id, r.book_id, b.title AS book_title, r.user_id, 
                                     r.reservation_date, r.due_date, r.returned 
                              FROM reservations r
                              JOIN books b ON r.book_id = b.book_id
                              ORDER BY r.reservation_id DESC";
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    // Check if the due date + 1 day has passed and status is not returned
                    $isOverdue = strtotime($row['due_date'] . ' +1 day') < time() && $row['returned'] === 'No';

                    // Determine the row class
                    $rowClass = $isOverdue ? "class='text-danger'" : ($row['returned'] === 'Yes' ? "class='text-black'" : "");

                    echo "<tr $rowClass>
                        <form method='POST' action=''>
                            <td>{$row['reservation_id']}</td>
                            <td>{$row['book_id']}</td>
                            <td>{$row['book_title']}</td>
                            <td>{$row['user_id']}</td>
                            <td>{$row['reservation_date']}</td>
                            <td>{$row['due_date']}</td>
                            <td>
                                <select name='status' class='form-select border-0' required>
                                    <option value='No' " . ($row['returned'] === 'No' ? 'selected' : '') . ">Not Returned</option>
                                    <option value='Yes' " . ($row['returned'] === 'Yes' ? 'selected' : '') . ">Returned</option>
                                </select>
                            </td>
                            <td>
                                <input type='hidden' name='reservation_id' value='{$row['reservation_id']}'>
                                <button type='submit' class='btn-minimal' name='update_reservation'>Update</button>
                                " . ($_SESSION['role'] === 'admin' ? "<button type='submit' class='btn-minimal text-danger' name='delete_reservation'>Delete</button>" : "") . "
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