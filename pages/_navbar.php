<?php
// Include the database connection
include '../includes/db_connection.php';

// Handle the search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search_query'];
    $query = "SELECT * FROM books WHERE title LIKE '%$search_query%' OR author LIKE '%$search_query%' OR genre LIKE '%$search_query%' LIMIT 8";
    $result = mysqli_query($conn, $query);
} else {
    // Default query to fetch all books
    $result = null;
}

// Handle Delete Book
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];
    $query = "DELETE FROM books WHERE book_id='$book_id'";
}
?>

<!-- Navbar -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../css/navbar_styles.css">

<!--Navbar-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <?php if ($_SESSION['role'] == 'student') { ?>
            <a class="navbar-brand fw-bold" href="dashboard_student.php">ULMS</a>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'admin') { ?>
            <a class="navbar-brand fw-bold" href="dashboard_admin.php">ULMS</a>
        <?php } ?>
        <?php if ($_SESSION['role'] == 'staff') { ?>
            <a class="navbar-brand fw-bold" href="dashboard_staff.php">ULMS</a>
        <?php } ?>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION['role'] == 'student') { ?>

                    <li class="nav-item"><a class="nav-link" href="books_student.php">Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservations_student.php">Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="fines_student.php">Accumulated Fine</a></li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'admin') { ?>
                    <li class="nav-item"><a class="nav-link" href="books_admin.php">Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="users_admin.php">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservations_admin.php">Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="fines_admin.php">Student Overdues</a></li>
                <?php } ?>

                <?php if ($_SESSION['role'] == 'staff') { ?>

                    <li class="nav-item"><a class="nav-link" href="books_admin.php">Books</a></li>
                    <li class="nav-item"><a class="nav-link" href="reservations_admin.php">Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="fines_admin.php">Student Overdues</a></li>
                <?php } ?>
            </ul>

            <!-- Responsive Search Bar -->
            <div class="d-flex align-items-center">
                <form id="search-form-desktop" class="d-none d-lg-flex align-items-center" method="GET" action="<?= basename($_SERVER['PHP_SELF']); ?>">
                    <button class="btn btn-outline-light me-2" id="search-btn" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                    <input class="form-control border-0 bg-light d-none" id="search-input" type="text" name="search_query" placeholder="Search" value="<?= htmlspecialchars($search_query) ?>">
                    <button class="btn btn-outline-light d-none" id="search-submit" type="submit" name="search">Go</button>
                </form>
                <form class="d-flex d-lg-none justify-content-center w-100" method="GET" action="<?= basename($_SERVER['PHP_SELF']); ?>">
                    <input class="form-control form-control-sm me-2" type="text" name="search_query" placeholder="Search" value="<?= htmlspecialchars($search_query) ?>">
                    <button class="btn btn-outline-light btn-sm" type="submit" name="search">Go</button>
                </form>
            </div>

            <ul class="navbar-nav ms-3">
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Display Search Results -->
<?php if ($search_query && $result): ?>
    <div class="container mt-3">
        <h3>Search Results for "<?= htmlspecialchars($search_query) ?>"</h3>
        <div class="search-results">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($result)): ?>
                    <div class="search-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                            <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($book['genre']) ?></p>
                            <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                            <p class="status">
                                <?= ($book['reserved'] == 'Yes' ? '<span class="text-danger">Unavailable</span>' : '<span class="text-success">Available</span>') ?>
                            </p>
                            <div class="btn-group">
                                <?php if ($_SESSION['role'] == 'student' && $book['reserved'] == 'No'): ?>
                                    <form method="POST" action="books_student.php">
                                        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                        <button class="btn reserve-btn" type="submit" name="reserve_book">Reserve</button>
                                    </form>
                                <?php elseif ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'staff'): ?>
                                    <form method="POST" action="books_admin.php">
                                        <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                        <button class="btn delete-btn" type="submit" name="delete_book">Delete</button>
                                    </form>
                                <?php elseif ($book['reserved'] == 'Yes'): ?>
                                    <span class="text-danger">Reserved</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No books found matching the search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>


<!--JavaScript for Expandable Search Bar-->
<script>
    document.getElementById("search-btn").addEventListener("click", function () {
        const input = document.getElementById("search-input");
        const submit = document.getElementById("search-submit");
        if (input.classList.contains("d-none")) {
            input.classList.remove("d-none");
            submit.classList.remove("d-none");
            input.focus();
        } else if (input.value.trim() !== "") {
            document.getElementById("search-form-desktop").submit(); // Submit if input has text
        } else {
            input.classList.add("d-none");
            submit.classList.add("d-none");
        }
    });


</script>
