<?php
// Start the session
session_start();
include '../includes/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$alertMessage = ''; // Variable for alerts
$alertType = '';    // Variable for alert type

// Handle the book reservation
if (isset($_POST['reserve_book'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    $reservation_date = date("Y-m-d");
    $due_date = date("Y-m-d", strtotime("+7 days"));

    $check_query = "SELECT * FROM reservations WHERE book_id = '$book_id' AND returned = 'No'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $alertMessage = "Sorry, this book is already reserved.";
        $alertType = "danger";
    } else {
        $reserve_query = "INSERT INTO reservations (book_id, user_id, reservation_date, due_date) 
                          VALUES ('$book_id', '$user_id', '$reservation_date', '$due_date')";
        $update_query = "UPDATE books SET reserved = 'Yes' WHERE book_id = '$book_id'";

        if (mysqli_query($conn, $reserve_query) && mysqli_query($conn, $update_query)) {
            $alertMessage = "Book reserved successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Error reserving the book.";
            $alertType = "danger";
        }
    }
}

// Default query
$query = "SELECT * FROM books";

// Handle search and filter
if (isset($_POST['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_POST['search_term']);
    $genre_filter = mysqli_real_escape_string($conn, $_POST['genre_filter']);

    // Add conditions to the query based on user input
    $conditions = [];
    if (!empty($search_term)) {
        $conditions[] = "(title LIKE '%$search_term%' OR author LIKE '%$search_term%' OR genre LIKE '%$search_term%')";
    }
    if (!empty($genre_filter)) {
        $conditions[] = "genre = '$genre_filter'";
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(' AND ', $conditions);
    }
}
$query .= " ORDER BY title ASC"; //Order By title

// Fetch genres for the dropdown
$genres_query = "SELECT DISTINCT genre FROM books";
$genres_result = mysqli_query($conn, $genres_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-reserve {
            border: 1px solid #007bff;
            background: transparent;
            color: #007bff;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .btn-reserve:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
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

    <div class="glass p-4">
        <h1 class="text-center">Library Books</h1>

        <!-- Search and Filter Form -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="search_term" class="form-control" placeholder="Search by title, author, or genre" value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <select name="genre_filter" class="form-control">
                        <option value="">All Genres</option>
                        <?php while ($genre = mysqli_fetch_assoc($genres_result)): ?>
                            <option value="<?php echo htmlspecialchars($genre['genre']); ?>"
                                <?php echo isset($_POST['genre_filter']) && $_POST['genre_filter'] === $genre['genre'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($genre['genre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="search" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <?php
        // Fetch books based on the query
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            echo "
            <div class='table-container'>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Genre</th>
                            <th>ISBN</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            while ($book = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($book['title']) . "</td>";
                echo "<td>" . htmlspecialchars($book['author']) . "</td>";
                echo "<td>" . htmlspecialchars($book['genre']) . "</td>";
                echo "<td>" . htmlspecialchars($book['isbn']) . "</td>";

                if ($_SESSION['role'] == 'student') {
                    if ($book['reserved'] == 'No') {
                        echo "<td>
                                <form method='POST'>
                                    <input type='hidden' name='book_id' value='{$book['book_id']}'>
                                    <button type='submit' name='reserve_book' class='btn-reserve'>Reserve</button>
                                </form>
                              </td>";
                    } else {
                        echo "<td>Reserved</td>";
                    }
                } else {
                    echo "<td>-</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>
                </table>
            </div>";
        } else {
            echo "<div class='alert alert-info'>No books match your search.</div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>