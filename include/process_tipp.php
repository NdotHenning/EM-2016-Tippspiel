<?php

include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

if(login_check($mysqli)) {
  closeMatches($mysqli);
  if(isset($_POST['match_id'], $_POST['tHome'], $_POST['tAway'], $_SESSION['user_id'])) {
    $user_id = $mysqli->real_escape_string($_SESSION['user_id']);
    $match_id = $mysqli->real_escape_string($_POST['match_id']);
    $tHome = $mysqli->real_escape_string($_POST['tHome']);
    $tAway = $mysqli->real_escape_string($_POST['tAway']);

    $isOpenStmt = $mysqli->query("SELECT *
      FROM matches
      WHERE matches.id = $match_id AND matches.status = 'opened'");
    if($isOpenStmt->num_rows == 1){

      if($tHome != NULL AND $tAway != NULL) {

        $stmt = $mysqli->query("SELECT *
      		FROM tipps
      		WHERE tipps.user_id = $user_id AND tipps.match_id = $match_id");
        if($stmt->num_rows == 1){
          $stmt2 = $mysqli->prepare("UPDATE tipps SET home = ?, away = ?
            WHERE tipps.match_id = ? AND tipps.user_id = ?");
            $stmt2->bind_param('iiii', $tHome, $tAway, $match_id, $user_id);
            $stmt2->execute();
            $stmt2->close();
            die('t##success##'.$match_id.'##'.$tHome.'##'.$tAway);
        }
        else{
          if($mysqli->query("INSERT INTO tipps (id, match_id, user_id, home, away, points)
            VALUES (NULL, '$match_id', '$user_id', '$tHome', '$tAway', 0)")) {

            die('t##success##'.$match_id.'##'.$tHome.'##'.$tAway);
          }
          else{
            //SQL Statement failed
            die('t##error##1');
          }
        }
      }
      //Home or away are empty
      die('t##error##2');
    }
    //Match is closed
    die('t##error##3');
  }
  //Invalid Request
  die('t##error##3');
}
else {
  //Not logged in
  die('t##error4');
}
