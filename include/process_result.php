<?php

include_once 'db_connect.php';
include_once 'functions.php';

sec_session_start();

if(login_check($mysqli)) {
  if(isset($_POST['match_id'], $_POST['rHome'], $_POST['rAway'], $_SESSION['user_id'])) {
    $user_id = $mysqli->real_escape_string($_SESSION['user_id']);
    $match_id = $mysqli->real_escape_string($_POST['match_id']);
    $rHome = $mysqli->real_escape_string($_POST['rHome']);
    $rAway = $mysqli->real_escape_string($_POST['rAway']);

    if($user_id <= 2){
      if($rHome != NULL AND $rAway != NULL) {
        if($stmt2 = $mysqli->prepare("UPDATE matches SET rHome = ?, rAway = ?, status = 'finished'
          WHERE id = ?")){
          $stmt2->bind_param('iii', $rHome, $rAway, $match_id);
          $stmt2->execute();
          $stmt2->close();
          calculatePoints($mysqli);
          die('r##success##'.$match_id.'##'.$rHome.'##'.$rAway);
        }
        else {
          //Statement failed
          die('r##error##1');
        }
      }
      //No values for home and away
      die('r##error##2');
    }
    //Wrong user
    die('r##error##3');
  }
  //Invalid Request
  die('r##error##4');
}
else{
  //Not logged in
  die('r##error##5');
}
