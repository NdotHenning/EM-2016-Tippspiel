<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'psl_config.php';
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
    
$mysqli->query("SET character_set_client=utf8");
$mysqli->query("SET character_set_connection=utf8");
$mysqli->query("SET character_set_results=utf8");
$mysqli->query("SET character_set_server=utf8");

?>
