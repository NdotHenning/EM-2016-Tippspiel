<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'include/db_connect.php';
include_once 'include/functions.php';

sec_session_start();

$user_id = $_SESSION['user_id'];

if(login_check($mysqli) == false || $user_id > 2) {
	header('Location: login.php');
	exit();
}

//All matches
$matches = getMatches($mysqli);
//User Table
$users = getUserTable($mysqli);
//Next matches
$nextMatch = getNextMatch($mysqli);

?>

<!DOCTYPE html>
<html>
<head>
  <title>EM2016Tippspiel</title>
  <meta charset="utf-8" />
	<meta http-equiv="CACHE-CONTROL" content="no-cache">
  <meta http-equiv="PRAGMA" content="no-cache">
  <meta name="viewport" content="initial-scale=1, width=device-width" />
  <link rel="stylesheet" href="static/css/styles.css" />
  <link rel="stylesheet" href="static/css/normalize.min.css">
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css' />
	<?php echo '<script>var user_id = '.$user_id.'</script>'; ?>
	<script src="static/js/jquery.min.js"></script>
  <script src="static/js/script.js"></script>
</head>

<body>

  <header id="top_header" class="clearfix">
    <div class="wrapper">
      <p class="logo">EM<span>2016</span>Tippspiel</p>
    </div>
  </header>

  <section id="banner">
    <div class="wrapper">
      <p class="next">Nächstes Spiel:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<span class="date">
					<?php echo xss_escape($nextMatch['date']); ?>&nbsp;<?php echo xss_escape($nextMatch['time']); ?> Uhr
				</span>
			</p>
      <div class="nextgame">
        <img src="static/images/<?php echo xss_escape($nextMatch['flag1']); ?>" alt="" class="flag">
        <p class="teamA"><?php echo xss_escape($nextMatch['name1']); ?></p>
        <p class="point">:</p>
        <p class="teamB"><?php echo xss_escape($nextMatch['name2']); ?></p>
        <img src="static/images/<?php echo xss_escape($nextMatch['flag2']); ?>" alt="" class="flag">
      </div>
    </div>
  </section>

  <section id="content">
    <div class="wrapper">
      <header class="menu">
        <p class="item active" onclick="">Ergebniss eintragen</p>
      </header>

      <main id="tipps" class="showing">

				<?php foreach ($matches as $match): ?>

					<?php if($match['status'] == 'closed'): ?>

	        <div class="game tipped <?php echo $match['match_id']; ?>">

	          <div class="line clearfix">
	            <p class="date">
								<?php echo xss_escape($match['date']); ?>
							</p>
	            <p class="time">
	            	<?php echo xss_escape($match['time']); ?>
	            </p>
	          </div>
	          <div class="match">
							<div class="teamA">
	              <img src="static/images/<?php echo xss_escape($match['flagA']); ?>" alt="" class="flag">
	              <span class="teams"><?php echo $match['nameA']; ?></span>
	            </div>
	            <div class="point">:</div>
	            <div class="teamB">
	              <span class="teams"><?php echo $match['nameB']; ?></span>
	              <img src="static/images/<?php echo xss_escape($match['flagB']); ?>" alt="" class="flag">
	            </div>
	          </div>
	          <div class="tipps">

  						<input type="number" min="0" max="100" autocomplete="off" class="home<?php echo $match['match_id']; ?>"
  							value="" name="home">
              <span>:</span>
              <input type="number" min="0" max="100" autocomplete="off" class="away<?php echo $match['match_id']; ?>"
  							value="" name="away">

	          </div>

	          <div class="button" data-match="<?php echo $match['match_id']; ?>"
							onclick="setResult(this)">
	            Beenden
	          </div>

	        </div>

				<?php endif; ?>


				<?php endforeach; ?>
      </main>

    </div>
  </section>

</body>
