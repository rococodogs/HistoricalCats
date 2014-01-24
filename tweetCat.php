#!/usr/bin/php
<?php
require "twitteroauth/twitteroauth/twitteroauth.php";
require "keys.php";
require "historicalcat.php";

function getDBID($input) {
    $query = "select * from tweets where `id` = ?";
    $res = querydb($query, array($input));

    return empty($res) ? false : true;
}

function putDBID($id, $tweet) {
    $query = "insert into tweets(`id`, `tweet`) values (?, ?)";
    return querydb($query, array($id, $tweet));
}

function querydb($query, $value = array(), &$error = null) {
    $pdo = new PDO(DBINFO, DBUSER, DBPASS);
    $stmt = $pdo->prepare($query);
    $stmt->execute($value);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt->closeCursor();

    $error = $stmt->errorInfo();

    return $res;
}

$tweet = null;
$indb = false;

 
    // set up checks here:
    //  a) tweet has content and is 160 chars at most
    //  b) TODO: make sure it hasn't been tweeted before
    //  c) TODO: not specifically about the tweet, but update the 
    //     upper range of the random generator 
    //      (basically, more or less entries could show up and we don't
    //       want to get a response that our search is outside the bounds)

while (
       ( !$tweet || strlen($tweet) > 160 ) || ( $indb )
    ) {

    $cat = new HistoricalCat();
    $tweet = $cat->meow();

    $id = $cat->catId();
    $indb = getDBID($id) ? true : false;

    $catLength = strlen($tweet);
}

$connection = new TwitterOAuth(
    TWIT_CONSUMER_KEY,
    TWIT_CONSUMER_SECRET,
    TWIT_ACCESS_TOKEN,
    TWIT_ACCESS_SECRET
    );

$status = $connection->post('statuses/update', array('status' => $tweet));

putDBID($id, $tweet);

?>