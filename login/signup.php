<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN UP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        form {
            padding: 20px;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        .back {
            font-size: 24px;
            color: #666;
            cursor: pointer;
        }

        .back:hover {
            color: #4CAF50;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .ca {
            text-align: center;
            display: block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
            transition: color 0.3s;
        }

        .ca:hover {
            color: #388E3C;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 15px;
        }

        .message.success {
            color: green;
        }

        @media only screen and (max-width: 400px) {
            .container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <?php 
    require '../database/dbconnect.php'; 
    $randomAccountId = rand(100000, 999999); // Generate a random account ID
    $randomCustomerId = rand(100000, 999999); // Generate a random customer ID
    ?>
    <div class="container">
        <form action="signup-check.php" method="post">
            <a href="index.php" class="back"><i class="fas fa-chevron-left"></i></a>
            <h2 style="text-align:center; color: #333;">SIGN UP</h2>
            <?php if (isset($_GET['error'])) { ?>
                <p class="message"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
                <p class="message success"><?php echo htmlspecialchars($_GET['success']); ?></p>
            <?php } ?>
            <label for="customername">Customer Name</label>
            <input type="text" id="customername" name="customername" placeholder="Customer Name" value="<?php echo htmlspecialchars($_GET['customername'] ?? ''); ?>">
            
            <label for="customeraddress">Customer Address</label>
            <input type="text" id="customeraddress" name="customeraddress" placeholder="Customer Address" value="<?php echo htmlspecialchars($_GET['customeraddress'] ?? ''); ?>">
            
            <label for="customerphonenumber">Customer Phone Number</label>
            <input type="text" id="customerphonenumber" name="customerphonenumber" placeholder="Customer Phone Number" value="<?php echo htmlspecialchars($_GET['customerphonenumber'] ?? ''); ?>">
            
            <label for="useremail">User Email</label>
            <input type="email" id="useremail" name="useremail" placeholder="User Email" value="<?php echo htmlspecialchars($_GET['useremail'] ?? ''); ?>">
            
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" value="<?php echo htmlspecialchars($_GET['username'] ?? ''); ?>">
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($_GET['password'] ?? ''); ?>">
            
            <label for="confirmpassword">Confirm Password</label>
            <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Confirm Password" value="<?php echo htmlspecialchars($_GET['confirmpassword'] ?? ''); ?>">
            
            <label for="accountid">Account ID</label>
            <input type="text" id="accountid" name="accountid" placeholder="Account ID" value="<?php echo htmlspecialchars($randomAccountId); ?>" readonly>
            
            <label for="customerid">Customer ID</label>
            <input type="text" id="customerid" name="customerid" placeholder="Customer ID" value="<?php echo htmlspecialchars($randomCustomerId); ?>" readonly>
            
            <button type="submit">Sign Up</button>
            <a href="../index.php" class="ca">Already have an account?</a>
        </form>

    </div>
    <script>
        var inputFields = document.querySelectorAll('input');
        inputFields.forEach(function(inputField) {
            inputField.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            inputField.addEventListener('blur', function() {
                this.parentNode.classList.remove('focused');
            });
        });
        var passwordInput = document.querySelector('input[name="password"]');
        var confirmPasswordInput = document.querySelector('input[name="confirmpassword"]');
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Passwords do not match');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        });
    </script>
</body>
</html>


