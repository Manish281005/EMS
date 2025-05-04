<?php
session_start();
include 'db.php';

// Fetch all events
$sql = "SELECT * FROM Events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home - Event Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .logo {
            width: 250px; /* Adjust the size of the logo */
            height: 250px; /* Ensure the height matches the width */
            border-radius: 50%; /* Makes the logo circular */
            margin: 20px auto;
            border: 10px solid #007bff; /* Optional: Add a border around the logo */
            box-shadow: 10px 10px 8px rgba(0, 0, 0, 0.2); /* Optional: Add a shadow for better aesthetics */
        }

        .content {
            margin-top: 20px;
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <img src="assets/images/logo.png" alt="Event Management Logo" class="logo">

    <!-- Welcome Content -->
    <div class="content">
        <h1 class="display-4">Welcome to Event Management System</h1>
        <p class="lead">Your one-stop solution for managing and booking events!</p>

        <!-- Buttons -->
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
            <a href="register.php" class="btn btn-secondary btn-lg">Register</a>
            <a href="about.php" class="btn btn-info btn-lg">About Us</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Event Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>