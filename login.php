<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'include/db_connect.php';
include_once 'include/functions.php';

sec_session_start();

if(login_check($mysqli) == true) {
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
			<a class="register" href="register.php">Registrieren</a>
    </div>
  </header>

  <section id="banner" class="login">
    <div class="wrapper">
      <h1>EM<span>2016</span>Tippspiel</h1>

    <?php
        if(isset($_GET['error'])) {
            echo '<p class="error">Login fehlgeschlagen!</p>';
        }
        else if(isset($_GET['registration-successful'])) {
            echo '<p class="notice">Registrierung erfolgreich!</p>';
        }
        
        /* EM is over now */
    ?>

      <form action="login.php" method="post" name="login_form">
        <div class="line clearfix">
          <i class="fa fa-user icon"></i>
          <input type="text" placeholder="E-Mail" name="email" readonly="readonly">
        </div>
        <div class="line clearfix">
          <i class="fa fa-lock icon"></i>
          <input type="password" placeholder="Passwort" name="password" readonly="readonly">
        </div>
        <input type="submit" value="LOGIN" class="submit">
      </form>
    </div>
  </section>
</body>
