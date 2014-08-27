#! /usr/bin/php -e
<?php
require dirname(__FILE__) . DIRECTORY_SEPARATOR . "keys.php"; 

$pdo = new PDO(DBINFO, DBUSER, DBPASS);
$stmt = $pdo->prepare("delete from tweets where tweets.timestamp < (NOW() - INTERVAL 6 MONTH)");
$stmt->execute();
$stmt->closeCursor();

