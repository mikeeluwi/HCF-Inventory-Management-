<?php
session_start();
include "../database/dbconnect.php";

if (isset($_POST['useremail']) && isset($_POST['password'])) {

	function validate($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	$useremail = validate($_POST['useremail']);
	$accountid = validate($_POST['accountid']);
	$password = validate($_POST['password']);

	if (empty($useremail)) {
		header("Location: ../index.php?error=useremail is required");
		exit();
	} else if (empty($password)) {
		header("Location: ../index.php?error=Password is required");
		exit();
	} else {
		$sql = "SELECT * FROM customeraccount WHERE useremail='$useremail'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
			if (password_verify($password, $row['password'])) {
				$_SESSION['useremail'] = $row['useremail'];
				$_SESSION['accountid'] = $row['accountid'];
				header("Location: ../app.php");
				exit();
			} else {
				header("Location: ../index.php?error=Incorrect useremail or password");
				exit();
			}
		} else {

			header("Location: ../index.php?error=User does not exist");
			exit();
		}
	}
} else {
	header("Location: index.php?error=You've been logged out");
	exit();
}

