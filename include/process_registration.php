<?php

include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

if(isset($_POST['name'], $_POST['email'], $_POST['password1'], $_POST['password2'])) {
    $name = $_POST['name'];
    $email =  $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];

    if($password1 != $password2) {
        //Passwords are not the same
        header('Location: ../register.php?error=pass-not-same'); 
        exit;
    }
    
    if(strlen($password1) < 6) {
        //Password is to short
        header('Location: ../register.php?error=pass-to-short'); 
        exit;
    }
    
	if(register($name, $email, $password1, $password2, $mysqli) == true) {
		//Registration successful
		header('Location: ../login.php?registration-successful');
        exit;
	}
	else {
		//Registration failed
		header('Location: ../register.php?error=same-id'); 
        exit;
	}
}
else {
	//The correct POST variables not sent
	die('Invalid Request');
}

?>
