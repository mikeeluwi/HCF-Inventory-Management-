<?php
// session.php
session_start();

if (basename($_SERVER['PHP_SELF']) == 'app.php') {
    // Allow access to app.php
} elseif (!isset($_SESSION['accountid']) || !isset($_SESSION['useremail'])) {
    header('Location: index.php?error=Unauthorized');
    exit();
}

include_once dirname(dirname(__FILE__)) . "/database/dbconnect.php";
if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid']; // fetch user details from database

    try {
        $sql = "SELECT * FROM customeraccount WHERE accountid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $customerName = $row['customername'] ?? null;
            $customerAddress = $row['customeraddress'] ?? null;
            $customerphonenumber = $row['customerphonenumber'] ?? null;
            $profilePicture = $row['profilepicture'] ?? null;
            $accountDetails = array(
                'accountid' => $accountId,
                'customername' => $customerName,
                'customeraddress' => $customerAddress,
                'customerphonenumber' => $customerphonenumber,
                'profilepicture' => $profilePicture
            );
            echo json_encode($accountDetails);
        } else {
            throw new Exception("Account not found");
        }
    } catch (Exception $e) {
        header('Location: index.php?error=Error fetching account details');
    }
}


