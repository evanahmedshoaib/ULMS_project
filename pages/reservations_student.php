<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../index.php");
    exit();
}

include '../includes/db_connection.php';
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            backdrop-filter: blur(5px);
            min-height: 100vh;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }

        .reservation-card {
            background: rgba(255, 255, 255, 0.6);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .reservation-card:hover {
            transform: scale(1.02);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: rgba(0, 123, 255, 0.1);
            border-bottom: none;
            font-weight: bold;
            font-size: x-large;


        }

        .card-body {
            border-radius: 15px;
        }

        .status-returned {
            color: green;
            font-weight: bold;
        }

        .status-not-returned {
            color: red;
            font-weight: bold;
        }
    </style>
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body style="background-color: #dae0e8;">

<!-- Include Navbar -->
<?php include '_navbar.php'; ?>

<div class="container py-4">
    <div class="glass p-4">
        <h2 class="text-center mb-4">My Reservations</h2>
        <div class="row">
            <?php
            $query = "SELECT  b.book_id, b.title, b.author, r.reservation_date, r.due_date, r.reservation_id,
                                         CASE 
                                            WHEN r.returned = 'No' THEN 'Not Returned'
                                            ELSE 'Returned'
                                         END AS status
                                  FROM books b
                                  JOIN reservations r ON b.book_id = r.book_id
                                  WHERE r.user_id = '$user_id'
                                  ORDER BY r.reservation_id DESC";

            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $statusClass = $row['status'] == 'Returned' ? 'status-returned' : 'status-not-returned';

                    echo "
                        <div class='col-md-4'>
                            <div class='reservation-card'>
                                <div class='card-header'>
                                    {$row['title']}
                                </div>
                                <div class='card-body'>
                                    <p><strong>Book ID:</strong> {$row['book_id']}</p>
                                    <p><strong>Author:</strong> {$row['author']}</p>
                                    <p><strong>Reserved Date:</strong> {$row['reservation_date']}</p>
                                    <p><strong>Due Date:</strong> {$row['due_date']}</p>
                                    <p><strong>Status:</strong> <span class='$statusClass'>{$row['status']}</span></p>
                                </div>
                            </div>
                        </div>
                    ";
                }
            } else {
                echo "<p class='text-center'>No reservations found.</p>";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>