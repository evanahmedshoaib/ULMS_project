<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Important Contacts</title>
    <style>
        body {
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        h1 {
            font-size: 2rem;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        table th, table td {
            text-align: center;
            vertical-align: middle;
            padding: 15px;
            border: 1px solid #ddd;
        }

        table thead th {
            background-color: #007bff;
            color: #fff;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 50%;
        }

        .text-center {
            text-align: center;
        }

        .text-muted {
            color: #6c757d;
        }

        .table-primary {
            background-color: #007bff;
            color: #fff;
        }

        .go-back {
            text-align: center;
            margin-top: 20px;
        }

        a.button {
            border: 1px solid #007bff;
            background: transparent;
            color: #007bff;
            padding: 5px 15px;
            font-size: 14px;
            border-radius: 12px;
            text-decoration: none; /* To remove the underline */
            transition: all 0.3s ease;
            cursor: pointer;
        }

        a.button:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
    <link rel="shortcut icon" href="../images/logo.png" type="image/x-icon"/>
</head>
<body>
<div class="container">
    <div class="text-center">
        <img src="../images/logo.png" alt="Logo">
        <h1 class="mb-3">Important Contacts</h1>
        <p class="text-muted">Preferred communication method is email and preferred time for phone calls is from 10.00 AM to 5.00 PM</p>
    </div>
    <table>
        <thead class="table-primary">
        <tr>
            <th>Purpose</th>
            <th>Phone Numbers</th>
            <th>Email</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                Any profile update or fine related issue.
            </td>
            <td>
                <p>Number 1: 01601269544</p>
                <p>Number 2: 01923355579</p>
            </td>
            <td>admin@seu.library.bd</td>
        </tr>
        <tr>
            <td>
                Any book related query.
            </td>
            <td>
                <p>Number 1: 01680180663</p>
                <p>Number 2: 01925734040</p>
            </td>
            <td>support@seu.library.bd</td>
        </tr>
        </tbody>
    </table>
</div>
<div class="go-back">
    <a href="../index.php" class="button" >Back to Signin</a>
</body>
</html>