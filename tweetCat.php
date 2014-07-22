#!/usr/bin/php
<?php
require "keys.php";
require "historicalcat.php";

HistoricalCat::$db_info = array(
    "info" => DBINFO,
    "user" => DBUSER,
    "pass" => DBPASS
);

HistoricalCat::$twitter_info = array(
    "consumer_key" => TWIT_CONSUMER_KEY,
    "consumer_secret" => TWIT_CONSUMER_SECRET,
    "access_token" => TWIT_ACCESS_TOKEN,
    "access_secret" => TWIT_ACCESS_SECRET
);

/* easy switches for debugging */
// HistoricalCat::$TWEET = false;
// HistoricalCat::$USE_DB = false;

$cat = new HistoricalCat(DPLAKEY);
$cat->meow();
