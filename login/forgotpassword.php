<?php
session_start();

// Redirect to index page if logged in
if (isset($_SESSION['accountid'])) {
    header("Location: ../index.php");
    exit;
}

// Check if the form is submitted
if (isset($_POST['useremail'])) {
    $useremail = $_POST['useremail'];

    // Database connection details
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "dbHenrichFoodCorps";
    $port = 3306;

    // Create a database connection
    $conn = new mysqli($hostname, $username, $password, $dbname, $port);

    // Check database connection
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
        exit();
    }
    error_log("Connected to database successfully");

    // Check if the user email exists in the database
    $sql = "SELECT * FROM customeraccount WHERE useremail='$useremail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $accountId = $row['accountid'];
        $customerName = $row['customername'];

        // Generate a new password and hash it
        $newPassword = rand(100000, 999999);
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $sql2 = "UPDATE customeraccount SET password='$hashedPassword' WHERE accountid='$accountId'";
        $result2 = mysqli_query($conn, $sql2);

        if ($result2) {
            // Send the new password to the user's email
            $subject = "Forgot Password - Henrich";
            $message = "Dear $customerName,\n\nYour new password is $newPassword\n\nBest regards,\nHenrich Team";
            $headers = "From: henrich.noreply@gmail.com";

            if (mail($useremail, $subject, $message, $headers)) {
                header("Location: ../index.php?success=Your new password has been sent to your email");
                exit;
            } else {
                header("Location: ../index.php?error=Unable to send email");
                exit;
            }
        } else {
            header("Location: ../index.php?error=Unable to update password");
            exit;
        }
    } else {
        header("Location: ../index.php?error=User email does not exist");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <style>
        body {
            background-color: #f2f2f2;
        }

        .container {
            max-width: 400px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 100px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
        }

        .form-group input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #45a049;
        }

        .forgot {
            text-align: center;
            margin-top: 20px;
        }

        .forgot a {
            color: #4CAF50;
        }

        @media only screen and (max-width: 480px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            .form-group input[type="email"] {
                width: 100%;
            }

            .form-group button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">
            <img src="../resources/images/henrichlogo.png" alt="henrich logo">
        </div>
        <p class="description">Enter your email address to receive a new password.</p>
        <form action="forgotpassword.php" method="post">
            <div class="form-group">
                <label for="useremail">Email</label>
                <input type="email" name="useremail" id="useremail" required>
            </div>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
            <div class="forgot">
                <a href="../index.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>

</html>

