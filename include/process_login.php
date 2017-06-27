<?php

include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

if(isset($_POST['email'], $_POST['password'])) {
	$email =  $_POST['email'];
	$password = $_POST['password'];

	if(login($email, $password, $mysqli) == true) {
		//Login success
		header('Location: ../index.php');
        exit;
	}
	else {
		//Login failed
		header('Location: ../login.php?error'); 
        exit;
	}
}
else {
	//The correct POST variables not sent
	die('Invalid Request');
}

?>
