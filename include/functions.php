<?php

include_once 'psl_config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Save session start
function sec_session_start() {
	$session_name = 'sec_session_id'; //Set a custom session name
	$secure = true;
	$httponly = true;
	//30 Tage gültig nach letztem Besuch
	$lifetime = 2592000;
	if(ini_set('session.use_only_cookies', 1) === FALSE){
		header('Location: ../error.php?err=Could not initiate a safe session (ini_set)');
		exit();
	}
	$cookieParams = session_get_cookie_params(); //Gets current cookie params
	session_set_cookie_params($lifetime,
							  $cookieParams['path'],
							  $cookieParams['domain'],
							  $secure,
							  $httponly);
	session_name($session_name);
	session_start();
	session_regenerate_id(true);
}


//Login function
function login($email, $password, $mysqli) {
    /* EM is over now */
    return false;

	if($stmt = $mysqli->prepare('SELECT id, name, password, salt
		FROM users
		WHERE email = ?
		LIMIT 1')) {
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$stmt->store_result();

		$stmt->bind_result($user_id, $name, $db_password, $salt);
		$stmt->fetch();

		if($stmt->num_rows == 1) {
			if(checkbrute($user_id, $mysqli) == true) {
				return false;
			}
			else {
				if(hash('whirlpool', $password.$salt) == $db_password) {
					$user_browser = $_SERVER['HTTP_USER_AGENT'];
					$user_id = preg_replace("/[^0-9]+/", "", $user_id);
					$_SESSION['user_id'] = $user_id;
					$name = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $name);
					$_SESSION['name'] = $name;
					$_SESSION['login_string'] = hash('sha512', $db_password . $user_id . 'atv867ewv6&/&/)v9f7a6ewb');
					//Login successfull
					return true;
				}
				else {
					$now = time();
					$mysqli->query("INSERT INTO login_attempts(id, time)
						VALUES ('$user_id', '$now')");
					//Login failed
					return false;
				}
			}
		}
		else {
			//No user exists
			return false;
		}
	}
}


//Brute force check
function checkbrute($user_id, $mysqli) {
	$now = time();
	$valid_attempts = $now - (2 * 60 * 60);
	if($stmt = $mysqli->prepare("SELECT time
		FROM login_attempts
		WHERE user_id = ?
		AND time > '$valid_attempts'")) {
		$stmt->bind_param('i', $user_id);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows > 5) {
			return true;
		}
		else {
			return false;
		}
	}
}


//Check if user is logged in
function login_check($mysqli) {
	if(isset($_SESSION['user_id'], $_SESSION['name'], $_SESSION['login_string'])) {
		$user_id = $_SESSION['user_id'];
		$login_string = $_SESSION['login_string'];
		$name = $_SESSION['name'];

		$user_browser = $_SERVER['HTTP_USER_AGENT'];

		if($stmt = $mysqli->prepare("SELECT password
			FROM users WHERE id = ? LIMIT 1")) {
			$stmt->bind_param('i', $user_id);
			$stmt->execute();
			$stmt->store_result();

			if($stmt->num_rows == 1) {
				$stmt->bind_result($password);
				$stmt->fetch();
				$login_check = hash('sha512', $password . $user_id . 'atv867ewv6&/&/)v9f7a6ewb');

				if(hash_equals($login_check, $login_string)) {
					//Already logged in
					return true;
				}
				else {
					//Not logged in
					return false;
				}
			}
			else {
				//Not logged in
				return false;
			}
		}
		else {
			//Not logged in
			return false;
		}
	}
	else {
		//Not logged in
		return false;
	}
}


//Registration
function register($name, $email, $password1, $password2, $mysqli) {
    $stmt = $mysqli->prepare("SELECT id
        FROM users
        WHERE email = ? or name = ?");
    $stmt->bind_param('ss', $email, $name);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0) {
        return false;
    }

    $salt = hash('whirlpool', $db_password.time().$email.'/Rv67769976b76/R/&(/8886dp9(?1hsfiu');
    $password = hash('whirlpool', $password1.$salt);

    $stmt = $mysqli->prepare("INSERT INTO users
        (id, email, password, salt, name)
        VALUES (NULL, ?, ?, ?, ?)");
    $stmt->bind_param('ssss', $email, $password, $salt, $name);
    $stmt->execute();

    return true;
}


//Sanitize PHP_SELF server variable
function esc_url($url) {
    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}


function getCount($value, $user, $mysqli) {
    $stmt = $mysqli->prepare("SELECT id
        FROM tipps
        WHERE user_id = ? AND points = ?");
    $stmt->bind_param('ii', $user, $value);
    $stmt->execute();
    $stmt->store_result();

    return $stmt->num_rows;
}


function getNextMatch($mysqli) {
	$time = time();
    $stmt = $mysqli->prepare("SELECT date, time, t1.name as 'name1', t1.flag as 'flag1', t2.name as 'name2', t2.flag as 'flag2'
        FROM matches
        INNER JOIN teams t1
            ON matches.home = t1.id
        INNER JOIN teams t2
            ON matches.away = t2.id
        WHERE matches.timestamp > ?
        ORDER BY timestamp
        LIMIT 1");
    $stmt->bind_param('i', $time);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($date, $time, $name1, $flag1, $name2, $flag2);

    $stmt->fetch();

    return Array('date' => $date, 'time' => $time, 'name1' => $name1, 'flag1' => $flag1, 'name2' => $name2, 'flag2' => $flag2);
}


function closeMatches($mysqli) {
    $stmt = $mysqli->query("UPDATE matches
        SET status = 'closed'
        WHERE status = 'opened' AND timestamp < ".(time() + 600));
}


function calculatePoints($mysqli){

	$stmt = $mysqli->query("SELECT *
		FROM matches
		WHERE matches.status = 'finished'");
	while($row = $stmt->fetch_assoc()){

		$match_id = $row['id'];

		//All tipps to one game
		$matchTip = $mysqli->query("SELECT *
			FROM tipps
			WHERE match_id = $match_id");
		while($line = $matchTip->fetch_assoc()){
			$points = 0;

			$user = $line['user_id'];

			//Correct tip
			if($line['home'] == $row['rHome'] && $line['away'] == $row['rAway']){
				$points = 3;
			}
			//Home has won
			elseif($line['home'] > $line['away'] && $row['rHome'] > $row['rAway']){
				$points = 1;
				//Tendenz richtig
				if(($line['home'] - $line['away']) == ($row['rHome'] - $row['rAway'])){
					$points = 2;
				}
			}
			//Away has won
			elseif($line['home'] < $line['away'] && $row['rHome'] < $row['rAway']){
				$points = 1;
				//Tendenz richtig
				if(($line['home'] - $line['away']) == ($row['rHome'] - $row['rAway'])){
					$points = 2;
				}
			}
      else if($row['rAway'] - $row['rHome'] == 0 && $line['away'] - $line['home'] == 0) {
          $points = 2;
      }
			$updateStmt = $mysqli->prepare("UPDATE tipps SET points = ?
				WHERE match_id = ? AND user_id = ?");
			$updateStmt->bind_param('iii', $points, $match_id, $user);
			$updateStmt->execute();
			$updateStmt->close();
		}
		$updateStmt = $mysqli->prepare("UPDATE matches SET status = 'calculated'
			WHERE id = ?");
		$updateStmt->bind_param('i', $match_id);
		$updateStmt->execute();
		$updateStmt->close();
	}
}

//All matches
function getMatches($mysqli){
	$user_id = $_SESSION['user_id'];
    $matches = array();

    closeMatches($mysqli);

	$stmt = $mysqli->query("SELECT date, time, status, matches.id as match_id, matches.rHome, matches.rAway,
    t1.name as nameA, t2.name as nameB,
    t1.flag as flagA, t2.flag as flagB,
		tipps.home as home, tipps.away as away,
		tipps.points as points
    FROM matches
    INNER JOIN teams t1 on matches.home = t1.id
    INNER JOIN teams t2 on matches.away = t2.id
		LEFT OUTER JOIN tipps on tipps.match_id = matches.id AND tipps.user_id = $user_id
		ORDER BY timestamp");
	while($row = $stmt->fetch_assoc()) {
		$matches[] = $row;
	}
	return $matches;
}


function getUserTable($mysqli){
	calculatePoints($mysqli);

	$users = array();

	$stmt = $mysqli->query("SELECT users.id as uid, name, sum(tipps.points) as points
		FROM users, tipps
		WHERE users.id = tipps.user_id
		GROUP BY name
		ORDER BY points DESC, name ASC");

    $last_points = -1;

    for($i = 0; $row = $stmt->fetch_assoc(); $i++) {
		$users[$i] = $row;

        if($last_points > $row['points'] || $last_points == -1) {
            $last_points = $row['points'];
            $users[$i]['num'] = $i + 1;
        }
        else {
            $users[$i]['num'] = '';
        }
	}
	return $users;
}

//Get Tipps from other Users
function getUserTippTable($mysqli){
	calculatePoints($mysqli);

	$games = array();

	$stmt = $mysqli->query("SELECT matches.id as mid, rHome, rAway, status, t1.name as tHome, t2.name as tAway,
		t1.flag as flagHome, t2.flag as flagAway
		FROM matches
		INNER JOIN teams t1 on matches.home = t1.id
		INNER JOIN teams t2 on matches.away = t2.id
		WHERE status = 'calculated' OR status = 'closed'
		ORDER BY  mid DESC
		LIMIT 5");

	while($row = $stmt->fetch_assoc()) {
		$games[] = $row;
	}
	return $games;
}

function getTippsForGame($match_id, $mysqli) {
	$tipps = array();

	$stmt = $mysqli->query("SELECT users.name as name, users.id as uid, tipps.home as home, tipps.away as away
		FROM tipps
		INNER JOIN users on users.id = tipps.user_id
		WHERE tipps.match_id = $match_id
		ORDER BY  name ASC");

	while($row = $stmt->fetch_assoc()) {
		$tipps[] = $row;
	}
	return $tipps;
}



//String escaping bei Ausgaben
function xss_escape($str) {
    return htmlentities($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

?>
