<?php
session_start();

// Include the database connection
include '../includes/db_connection.php';

$alertMessage = ''; // Variable to hold alert message
$alertType = '';    // Variable to hold alert type (success or danger)

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header("Location: ../index.php");
    exit();
}

// Handle Add Book
if (isset($_POST['add_book'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $reserved = 'No'; // New books are not reserved by default

    // Check if book_id already exists
    $check_book_id_query = "SELECT * FROM books WHERE book_id='$book_id'";
    $check_book_id_result = mysqli_query($conn, $check_book_id_query);

    // Check if isbn already exists
    $check_isbn_query = "SELECT * FROM books WHERE isbn='$isbn'";
    $check_isbn_result = mysqli_query($conn, $check_isbn_query);

    if (mysqli_num_rows($check_book_id_result) > 0) {
        $alertMessage = "Error: Book ID already exists!";
        $alertType = "danger";
    } elseif (mysqli_num_rows($check_isbn_result) > 0) {
        $alertMessage = "Error: ISBN already exists!";
        $alertType = "danger";
    } else {
        $query = "INSERT INTO books (book_id, title, author, genre, isbn, reserved) 
                  VALUES ('$book_id', '$title', '$author', '$genre', '$isbn', '$reserved')";
        if (mysqli_query($conn, $query)) {
            $alertMessage = "Book added successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Error adding book: " . mysqli_error($conn);
            $alertType = "danger";
        }
    }
}

// Handle Update Book
if (isset($_POST['update_book'])) {
    $original_book_id = $_POST['original_book_id'];
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $reserved = $_POST['reserved'];

    // Check if book_id already exists (excluding the current record)
    $check_book_id_query = "SELECT * FROM books WHERE book_id='$book_id' AND book_id != '$original_book_id'";
    $check_book_id_result = mysqli_query($conn, $check_book_id_query);

    // Check if isbn already exists (excluding the current record)
    $check_isbn_query = "SELECT * FROM books WHERE isbn='$isbn' AND book_id != '$original_book_id'";
    $check_isbn_result = mysqli_query($conn, $check_isbn_query);

    if (mysqli_num_rows($check_book_id_result) > 0) {
        $alertMessage = "Error: Book ID already exists!";
        $alertType = "danger";
    } elseif (mysqli_num_rows($check_isbn_result) > 0) {
        $alertMessage = "Error: ISBN already exists!";
        $alertType = "danger";
    } else {
        $query = "UPDATE books 
                  SET book_id='$book_id', title='$title', author='$author', genre='$genre', isbn='$isbn', reserved='$reserved' 
                  WHERE book_id='$original_book_id'";
        if (mysqli_query($conn, $query)) {
            $alertMessage = "Book updated successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Error updating book: " . mysqli_error($conn);
            $alertType = "danger";
        }
    }
}

// Handle Delete Book
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['original_book_id'];
    $query = "DELETE FROM books WHERE book_id='$book_id'";
    if (mysqli_query($conn, $query)) {
        $alertMessage = "Book deleted successfully!";
        $alertType = "success";
    } else {
        $alertMessage = "Error deleting book: " . mysqli_error($conn);
        $alertType = "danger";
    }
}

// Default query
$query = "SELECT * FROM books";

// Handle Search and Filter
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
    <title>Manage Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
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

    <!-- Add Book Section -->
    <div class="glass p-4 mb-4">
        <h2 class="text-center">Add Book</h2>
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="book_id" class="form-label">Book ID</label>
                <input type="number" class="form-control" name="book_id" required>
            </div>
            <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="col-md-6">
                <label for="author" class="form-label">Author</label>
                <input type="text" class="form-control" name="author" required>
            </div>
            <div class="col-md-6">
                <label for="genre" class="form-label">Genre</label>
                <input type="text" class="form-control" name="genre" required>
            </div>
            <div class="col-md-6">
                <label for="isbn" class="form-label">ISBN</label>
                <input type="text" class="form-control" name="isbn" required>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary" name="add_book">Add Book</button>
            </div>
        </form>
    </div>

    <!-- Manage Books Section -->
    <div class="glass p-4">
        <h3 class="text-center">Manage Books</h3>

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

        <!-- Books Table -->
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>ISBN</th>
                    <th>Reserved</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $result = mysqli_query($conn, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                                <form method='POST'>
                                    <td>
                                        <input type='text' class='form-control border-0' name='book_id' value='{$row['book_id']}' required>
                                        <input type='hidden' name='original_book_id' value='{$row['book_id']}'>
                                    </td>
                                    <td><input type='text' class='form-control border-0' name='title' value='{$row['title']}' required></td>
                                    <td><input type='text' class='form-control border-0' name='author' value='{$row['author']}' required></td>
                                    <td><input type='text' class='form-control border-0' name='genre' value='{$row['genre']}' required></td>
                                    <td><input type='text' class='form-control border-0' name='isbn' value='{$row['isbn']}' required></td>
                                    <td>
                                        <select name='reserved' class='form-select border-0' required>
                                            <option value='No' " . ($row['reserved'] === 'No' ? 'selected' : '') . ">No</option>
                                            <option value='Yes' " . ($row['reserved'] === 'Yes' ? 'selected' : '') . ">Yes</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type='submit' class='btn-minimal' name='update_book'>Update</button>
                                        <button type='submit' class='btn-minimal' name='delete_book'>Delete</button>
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