<?php
// filepath: c:\xampp\htdocs\EVE\login.php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get the selected role

    if ($role == 'user') {
        // Check if the user is a regular user
        $sql_user = "SELECT * FROM User WHERE User_Email = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("s", $email);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();
            if (password_verify($password, $user['User_Password'])) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['user_name'] = $user['User_Name'];
                header("Location: user/dashboard.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid password for user.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No user account found with this email.</div>";
        }
    } elseif ($role == 'organiser') {
        // Check if the user is an organiser
        $sql_organiser = "SELECT * FROM Organiser WHERE Organiser_Email = ?";
        $stmt_organiser = $conn->prepare($sql_organiser);
        $stmt_organiser->bind_param("s", $email);
        $stmt_organiser->execute();
        $result_organiser = $stmt_organiser->get_result();

        if ($result_organiser->num_rows > 0) {
            $organiser = $result_organiser->fetch_assoc();
            if (password_verify($password, $organiser['Organiser_Password'])) {
                $_SESSION['organiser_id'] = $organiser['Organiser_ID'];
                $_SESSION['organiser_name'] = $organiser['Organiser_Name'];
                header("Location: organiser/dashboard.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Invalid password for organiser.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>No organiser account found with this email.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Invalid role selected.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Event Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #007bff, #6610f2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="user">User</option>
                    <option value="organiser">Organiser</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>