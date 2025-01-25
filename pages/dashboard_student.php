<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

include '../includes/db_connection.php';
$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$base_fine = 20; // fine amount per day due
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard_styles.css">
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">
<!-- Include Navbar -->
<?php include '_navbar.php'; ?>

<div class="header">
    <h1>Welcome back!  <?php echo htmlspecialchars($name); ?></h1>
    <p>Hey there! I hope you are having an absolutely splendid day today!</p>
</div>

<div class="btn-container">
    <!-- Containers Section -->
    <div class="das-container">
        <a href="books_student.php" class="box">Books</a>
        <a href="reservations_student.php" class="box">Reservations</a>
        <a href="fines_student.php" class="box">
            <?php
            // Calculate fines based on overdue books
            $query = "SELECT SUM(GREATEST(DATEDIFF(CURDATE(), r.due_date), 0) * $base_fine) AS fine
                      FROM reservations r
                      WHERE r.user_id = '$user_id' 
                      AND r.returned = 'No' 
                      AND r.due_date < CURDATE()";  // Only consider overdue books

            // Execute the query
            $result = mysqli_query($conn, $query);

            // Check if a result is returned
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $fine = $row['fine'] ? $row['fine'] : 0;  // If no fine, set to 0
                echo "<p>Total Fine: $fine BDT</p>";
            } else {
                echo "<p>Error in calculating fine.</p>";
            }
            ?>
        </a>
    </div>
</div>

<script>
    const proxyUrl = 'https://api.allorigins.win/get?url=';
    const apiUrl = 'https://type.fit/api/quotes';

    async function fetchQuote() {
        try {
            const response = await fetch(`${proxyUrl}${encodeURIComponent(apiUrl)}`);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            // Parse the JSON response from the proxy
            const data = await response.json();

            // Extract the actual quotes data from the "contents" field
            const quotes = JSON.parse(data.contents);

            // Select a random quote
            const randomQuote = quotes[Math.floor(Math.random() * quotes.length)];

            // Display the random quote
            document.getElementById('random-quote').innerText = `"${randomQuote.text}" - ${randomQuote.author || 'Unknown'}`;
        } catch (error) {
            console.error('Error fetching quote:', error);
            document.getElementById('random-quote').innerText = 'Could not fetch a quote at the moment. Please try again later.';
        }
    }

    // Fetch a random quote on page load
    fetchQuote();
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>