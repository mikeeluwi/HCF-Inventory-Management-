<?php

session_start();
require '../database/dbconnect.php';

if (isset($_POST['accountid']) && isset($_POST['customername']) 
    && isset($_POST['customeraddress']) && isset($_POST['customerphonenumber'])
    && isset($_POST['customerid']) && isset($_POST['username']) 
    && isset($_POST['password']) && isset($_POST['useremail'])) {

    function validate($data){
       $data = trim($data);
       $data = stripslashes($data);
       $data = htmlspecialchars($data);
       return $data;
    }

    $accountid = validate($_POST['accountid']);
    $customername = validate($_POST['customername']);
    $customeraddress = validate($_POST['customeraddress']);
    $customerphonenumber = validate($_POST['customerphonenumber']);
    $customerid = validate($_POST['customerid']);
    $username = validate($_POST['username']);
    $password = password_hash(validate($_POST['password']), PASSWORD_DEFAULT);
    $useremail = validate($_POST['useremail']);

    $user_data = 'accountid='. $accountid. '&customername='. $customername. '&username='. $username;

    // Check if all fields are filled
    if (empty($accountid)) {
        header("Location: ./signup.php?error=Account ID is required&$user_data");
        exit();
    } elseif (empty($customername)) {
        header("Location: ./signup.php?error=Customer name is required&$user_data");
        exit();
    } elseif (empty($customeraddress)) {
        header("Location: ./signup.php?error=Customer address is required&$user_data");
        exit();
    } elseif (empty($customerphonenumber)) {
        header("Location: ./signup.php?error=Customer phone number is required&$user_data");
        exit();
    } elseif (empty($customerid)) {
        header("Location: ./signup.php?error=Customer ID is required&$user_data");
        exit();
    } elseif (empty($username)) {
        header("Location: ./signup.php?error=Username is required&$user_data");
        exit();
    } elseif (empty($password)) {
        header("Location: ./signup.php?error=Password is required&$user_data");
        exit();
    } elseif (empty($useremail)) {
        header("Location: ./signup.php?error=User email is required&$user_data");
        exit();
    }

    // Check if the customer ID or username or useremail is already taken
    $sql = "SELECT * FROM customeraccount WHERE customerid='$customerid' OR username='$username' OR useremail='$useremail'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        header("Location: ./signup.php?error=The customer ID, username or user email is already taken, try another&$user_data");
        exit();
    }

    // Insert the user into the database
    $sql2 = "INSERT INTO customeraccount (accountid, customername, customeraddress, customerphonenumber, customerid, username, password, useremail) VALUES('$accountid', '$customername', '$customeraddress', '$customerphonenumber', '$customerid', '$username', '$password', '$useremail')";
    $result2 = mysqli_query($conn, $sql2);

    if ($result2) {
        header("Location: ./signup.php?success=Your account has been created successfully");
        echo "<script>
        setTimeout(function () {
            window.location.href = './login.php';
        }, 3000);
        </script>";
        exit();
    } else {
        header("Location: ./signup.php?error=An unknown error occurred while creating the account&$user_data");
        exit();
    }
} else {
    header("Location: ./signup.php?error=Please fill in all required fields");
    exit();
}



