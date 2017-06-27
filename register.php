<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'include/db_connect.php';
include_once 'include/functions.php';

sec_session_start();

/* EM is over now */
if(true || login_check($mysqli) == true) {
	header('Location: index.php');
	exit();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>EM2016Tippspiel</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />

  <script src="https://use.fontawesome.com/670a99c5ee.js"></script>
  <link rel="stylesheet" href="static/css/styles.css" />
  <link rel="stylesheet" href="static/css/normalize.min.css">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
  <script src="static/js/jquery.min.js"></script>
  <script src="static/js/script.js"></script>
</head>

<body>

  <header id="top_header" class="clearfix">
    <div class="wrapper">
      <p class="logo">EM<span>2016</span>Tippspiel</p>
      <a class="register" href="login.php">Login</a>
    </div>
  </header>

  <section id="banner" class="login">
    <div class="wrapper">
      <h1>EM<span>2016</span>Tippspiel</h1>

    <?php 
        if(isset($_GET['error']) && $_GET['error'] == 'same-id') {
            echo '<p class="error">Name oder E-Mail-Adresse existiert bereits!</p>';
        }
        else if(isset($_GET['error']) && $_GET['error'] == 'pass-not-same') {
            echo '<p class="error">Passwörter stimmen nicht überein!</p>';
        }
        else if(isset($_GET['error']) && $_GET['error'] == 'pass-to-short') {
            echo '<p class="error">Passwort muss mindestens 6 Zeichen enthalten!</p>';
        }
    ?>

    <form action="include/process_registration.php" method="post" name="login_form">
        <div class="line clearfix">
            <i class="fa fa-user icon"></i>
            <input type="text" placeholder="Name" name="name">
        </div>
        <div class="line clearfix">
          <i class="fa fa-user icon"></i>
          <input type="email" placeholder="E-Mail" name="email">
        </div>
        <div class="line clearfix">
          <i class="fa fa-lock icon"></i>
          <input type="password" placeholder="Passwort" name="password1">
        </div>
        <div class="line clearfix">
          <i class="fa fa-lock icon"></i>
          <input type="password" placeholder="Bestätigen" name="password2">
        </div>
        <input type="submit" value="REGISTRIEREN" class="submit">
      </form>
    </div>
  </section>
</body>
