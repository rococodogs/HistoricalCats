HistoricalCats
==============

Grabs an item at random via the [Digital Public Library of America](http://db.la)'s [API platform](http://dp.la/developers/) and tweets it out. Built at [LibHack 2014](http://libhack.org) in Philadelphia.

### getting set up

change `keys.config.php` to `keys.php` and fill with appropriate keys/passwords/dbinfo (info on getting a DPLA developer key can be found [here](http://dp.la/info/developers/codex/policies/#get-a-key))

### basic usage

```php
require "keys.php";
require "historicalcat.php";

// if you're writing to a db, make sure to fill out this array
HistoricalCat::$db_info = array(
    "info" => DBINFO,
    "user" => DBUSER,
    "pass" => DBPASS
);

// same for the twitter account
HistoricalCat::$twitter_info = array(
    "consumer_key" => TWIT_CONSUMER_KEY,
    "consumer_secret" => TWIT_CONSUMER_SECRET,
    "access_token" => TWIT_ACCESS_TOKEN,
    "access_secret" => TWIT_ACCESS_SECRET
);

$cat = new HistoricalCat(DPLAKEY);
$cat->meow();

```

### customizing

`HistoricalCat::$searchTerms` can be altered to search for different topics, and `HistoricalCat::$catPhrases` stores the prepending phrases. So, for example, an HistoricalDog generator would look like:

```php
HistoricalCat::$catPhrases = array(
    "Woof!", "Very doge!", "Such wow!", "Bark!!"
);

HistoricalCat::$searchTerms = array(
    "dog", "dogs", "puppy", "puppies", "canine"
);

HistoricalCat::$db_info = array( ... );
HistoricalCat::$twitter_info = array( ... );

$dog = new HistoricalCat(DPLAYKEY);
$dog->meow();
```

If you were looking to just generate the tweets without storing them or posting to twitter, switch the `$db_storage` and/or `$tweet` vars to `false` __before__ instantiating a new instance.

```php
HistoricalCat::$db_storage = false;
HistoricalCat::$tweet = false;
```