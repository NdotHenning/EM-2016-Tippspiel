<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'include/db_connect.php';
include_once 'include/functions.php';
    
sec_session_start();

if(login_check($mysqli) == false) {
	header('Location: login.php');
	exit();
}

//All matches
$matches = getMatches($mysqli);
//User Table
$users = getUserTable($mysqli);
//User Id
$user_id = $_SESSION['user_id'];
//Next matche
$nextMatch = getNextMatch($mysqli);

$games = getUserTippTable($mysqli);

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
			<?php if($user_id <= 2): ?>
				<a class="register" href="results.php">Verwalten</a>
			<?php endif; ?>
    </div>
  </header>

  <section id="banner">
    <div class="wrapper"><?php
        
$stmt = $mysqli->query("SELECT status
                        FROM matches
                        WHERE id = 51
                        LIMIT 1");
                               
$last_match = $stmt->fetch_assoc();

if($last_match['status'] != 'calculated') {
        echo '
      <p class="next">Nächstes Spiel:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span class="date">
          '.xss_escape($nextMatch['date']).'&nbsp;'.xss_escape($nextMatch['time']).' Uhr
        </span>
      </p>
      <div class="nextgame">
        <img src="static/images/'.xss_escape($nextMatch['flag1']).'" alt="" class="flag">
        <p class="teamA">'.xss_escape($nextMatch['name1']).'</p>
        <p class="point">:</p>
        <p class="teamB">'.xss_escape($nextMatch['name2']).'</p>
        <img src="static/images/'.xss_escape($nextMatch['flag2']).'" alt="" class="flag">';
}
else {
    $stmt = $mysqli->query("SELECT users.id as uid, name, sum(tipps.points) as points
                           FROM users, tipps
                           WHERE users.id = tipps.user_id
                           GROUP BY name
                           ORDER BY points DESC, name ASC
                           LIMIT 1");
                           
    $winner = $stmt->fetch_assoc();
    
    echo '
      <p class="next">Herzlichen Gl&uuml;ckwunsch!</p>
      <div class="nextgame">
        <p class="teamA">Sieger:</p>
        <p class="teamB">'.$winner['name'].' ('.$winner['points'].' P.)</p>';
}
?>
      </div>
    </div>
  </section>

  <section id="content">
    <div class="wrapper">
      <header class="menu">
        <p class="item active" onclick="javascript: showInfo('tipps', this)">Tipps</p>
        <p class="item" onclick="javascript: showInfo('results', this)">Ergebnisse</p>
        <p class="item" onclick="javascript: showInfo('table', this)">Tabelle</p>
				<p class="item" onclick="javascript: showInfo('otherTipps', this)">Mitspieler</p>
      </header>

      <main id="tipps" class="showing">
				<p class="desc_text">
					Ergebnisse nach dem Elfmeterschießen.
				</p>

				<?php foreach ($matches as $match): ?>

					<?php if($match['status'] == 'opened'): ?>

	        <div class="game tipped <?php echo $match['match_id']; ?>">

						<?php if($match['home'] != NULL AND $match['away'] != NULL): ?>
	          	<img class="marked" src="static/images/bookmarg.svg">
						<?php else: ?>
							<img class="marked hide" src="static/images/bookmarg.svg">
						<?php endif; ?>

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

							<?php if($match['home'] != NULL AND $match['away'] != NULL): ?>
		            <input type="number" min="0" max="100" autocomplete="off" class="home<?php echo $match['match_id']; ?>"
									value="<?php echo xss_escape($match['home']); ?>" name="home">
		            <span>:</span>
		            <input type="number" min="0" max="100" autocomplete="off" class="away<?php echo $match['match_id']; ?>"
									value="<?php echo xss_escape($match['away']); ?>" name="away">
							<?php else: ?>
								<input type="number" min="0" max="100" autocomplete="off" class="home<?php echo $match['match_id']; ?>"
									value="" name="home">
		            <span>:</span>
		            <input type="number" min="0" max="100" autocomplete="off" class="away<?php echo $match['match_id']; ?>"
									value="" name="away">
							<?php endif; ?>

	          </div>

						<?php if($match['home'] != NULL AND $match['away'] != NULL): ?>
		          <div class="button" data-match="<?php echo $match['match_id']; ?>"
								onclick="setTipps(this)">
		            Ändern
		          </div>
						<?php else: ?>
		          <div class="button" data-match="<?php echo $match['match_id']; ?>"
								onclick="setTipps(this)">
		            Tippen
		          </div>
						<?php endif; ?>

	        </div>

				<?php endif; ?>


				<?php endforeach; ?>
      </main>

      <main id="results" class="">
				<?php foreach (array_reverse($matches) as $match): ?>

					<?php if($match['status'] != 'opened'): ?>
		        <div class="game tipped <?php echo $match['match_id']; ?>">
		          <div class="line clearfix">
		            <p class="date"><?php echo xss_escape($match['date']); ?></p>
		            <p class="time"><?php echo xss_escape($match['time']); ?></p>
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
								<?php if($match['status'] == 'closed'): ?>
									<span class="result">Spiel läuft gerade...</span>
								<?php else: ?>
			            <span class="result"><?php echo xss_escape($match['rHome']); ?></span>
			            <span>:</span>
			            <span class="result"><?php echo xss_escape($match['rAway']); ?></span>
								<?php endif; ?>
		          </div>
							<div class="line clearfix">
		            <p class="date">Dein Tipp</p>
		            <p class="time">
									<?php echo xss_escape($match['points']); ?>
									&nbsp;Punkte
								</p>
		          </div>
							<?php if($match['home'] != NULL AND $match['away'] != NULL): ?>
								<div class="tipps">
			            <span class="result"><?php echo xss_escape($match['home']); ?></span>
			            <span>:</span>
			            <span class="result"><?php echo xss_escape($match['away']); ?></span>
			          </div>
							<?php else: ?>
								<div class="tipps">
			            <span>Kein Tipp abgegeben</span>
			            </div>
							<?php endif; ?>
		        </div>
					<?php endif; ?>
				<?php endforeach; ?>
      </main>

      <main id="table" class="">
        <h1>Gesamtwertung:</h1>
        <table>
          <tbody>
          <tr>
            <th>Rang</th>
						<th></th>
            <th>Richtig (3P)</th>
            <th>Differenz (2P)</th>
            <th>Tendenz (1P)</th>
            <th>Punkte</th>
          </tr>
					<?php foreach ($users as $user): ?>
						<?php if($user_id == $user['uid']): ?>
							<tr class="blue">
						<?php else: ?>
							<tr>
						<?php endif; ?>
							<td>
								<?php echo $user['num']; ?>
							</td>
							<td>
	            	<?php echo xss_escape($user['name']); ?>
	            </td>
	            <td><?php echo getCount(3, $user['uid'], $mysqli); ?></td>
	            <td><?php echo getCount(2, $user['uid'], $mysqli); ?></td>
	            <td><?php echo getCount(1, $user['uid'], $mysqli); ?></td>
	            <td class="points">
            	<?php echo $user['points']; ?>
              </td>
	          </tr>
					<?php endforeach; ?>
        </table>
      </main>

			<main id="otherTipps" class="">
        <h1>Alle Tipps der letzen Spiele:</h1>
				<?php foreach ($games as $game): ?>
					<div class="game">
						<p class="home">
							<img src="static/images/<?php echo xss_escape($game['flagHome']); ?>" alt="" class="flag">
							<?php echo xss_escape($game['tHome']); ?>
						</p>
						<p class="dots">:</p>
						<p>
							<?php echo xss_escape($game['tAway']); ?>
							<img src="static/images/<?php echo xss_escape($game['flagAway']); ?>" alt="" class="flag">
						</p>
						<img src="static/images/down.svg" alt="" class="down">
					</div>
					<div class="game results">
						<?php if($game['status'] == 'closed'): ?>
							<p class="home">
								Spiel läuft gerade...
							</p>
						<?php else: ?>
							<p class="home">
								<?php echo xss_escape($game['rHome']); ?>
							</p>
							<p class="dots">:</p>
							<p>
								<?php echo xss_escape($game['rAway']); ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="table">
		        <table>
							<tr>
								<th></th>
								<th><?php echo xss_escape($game['tHome']); ?></th>
								<th><?php echo xss_escape($game['tAway']); ?></th>
							<tr>
							<?php foreach (getTippsForGame($game['mid'], $mysqli) as $tipp): ?>
								<?php if($tipp['uid'] == $user_id): ?>
								<tr class="blue">
								<?php else: ?>
								<tr>
								<?php endif; ?>
									<td><?php echo xss_escape($tipp['name']); ?></td>
									<td><?php echo xss_escape($tipp['home']); ?></td>
									<td><?php echo xss_escape($tipp['away']); ?></td>
								</tr>
							<?php endforeach; ?>
		        </table>
					</div>
				<?php endforeach; ?>
      </main>
    </div>
  </section>

</body>
