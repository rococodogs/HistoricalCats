<?php

/**
 *  HistoricalCat
 *  -------------
 *  Constructs a tweet of cat-related images from the DPLA api
 *  (with the option to write it to a mysql database to prevent repeats)
 *  
 *  tweet format:
 *  [opening phrase ($catPhrases)]. [title of dpla record]. [link to dpla entry]
 *
 *  @author Adam Malantonio, January 2014.
 *  
 */

class HistoricalCat {

    // phrases that prepend the 
    public static $catPhrases = array(
        "Meow!", 
        "Purrrr!", 
        "Prrrr!",
        "Some kitten!",
        "What a cat!",
        "Fine Feline!",
        "Such meow!"
    );

    // terms to search against dpla api.
    public static $searchTerms = array(
        "cat",
        "cats",
        "kitten",
        "kittens",
        "feline"
    );

    // TODO: allow for custom searches (eg: "cat AND feline OR dog AND canine")
    public static $searchBoolean = "OR";

    // api base (using the 'items' search, not collections)
    private $urlBase = "http://api.dp.la/v2/items?";

    // an option to write tweets to a database and prevent repeat tweets
    // ( db connector is PDO, so use those credentials! )
    public static $USE_DB = true;
    public static $db_info = array(
        "info" => "",
        "user" => "",
        "pass" => ""
    );

    // an option to turn off tweeting (helpful for debugging)
    public static $TWEET = true;

    // number of chars to cut off a title at
    public static $tweetThreshold = 100;

    // twitter info for posting
    public static $twitter_info = array(
        "consumer_key" => "",
        "consumer_secret" => "",
        "access_token" => "",
        "access_secret" => ""
    );

    /**
     *  __construct
     *  -----------
     *  does a lot of work for a constructor:
     *  a) does a first-pass search of the DPLA api to retrieve a count of the results
     *  b) conducts a second search using the aforementioned count in generating a random
     *     page of results
     *     1) if $USE_DB is turned on, we'll see if this id has found its way into the
     *        database, and if so, we'll start the search again
     *  c) reduce the multitude of information into the 3 items needed (plus choose a random
     *     entry from the catPhrases array)
     *
     */

    public function __construct($dplakey) {
        
        $this->dplakey = $dplakey;
        $this->query = implode("+" . self::$searchBoolean . "+", self::$searchTerms);

        // our first pass will be solely to grab the outer boundary of our
        // random number (previously, an educated stab in the dark resulted 
        // in blank tweets). this should help to cut down on errors and multiple
        // requests.

        $firstPassURL = $this->buildAPIRequest($this->query);
        $firstPass = $this->fetchResults($firstPassURL);
        $this->count = $firstPass['count'];

        $purr = false;

        while ( !$purr ) {
            shuffle(self::$catPhrases);

            $rand = rand(1, $this->count);
            $nextPassURL = $this->buildAPIRequest($this->query, array("page" => $rand));
            $nextPass = $this->fetchResults($nextPassURL);

            $cat = $nextPass['docs'][0];

            if ( self::$USE_DB ) {
                $purr = $this->canHaz($cat['id']);

            } else {
                $purr = true;
            }

        }

        $this->cat = array(
            "id" => $cat['id'],
            "intro" => self::$catPhrases[0],
            "title" => !is_array($cat['sourceResource']['title']) ? $cat['sourceResource']['title'] : $cat['sourceResource']['title'][0],
            "description" => @$cat['sourceResource']['description'] ?: ""
        );

        $this->cat['title'] = substr($this->cat['title'], 0, 1) == "\"" ? $this->cat['title'] : "\"" . $this->cat['title'] . "\"";

    }


    /**
     *  getCat
     *  ------
     *  @return array  cat array (id, intro, title, description)
     *
     */

    public function getCat() {
        return $this->cat;
    }


    /**
     *  getDescription
     *  --------------
     *  @return string  dpla description
     *
     */

    public function getDescription() {
        return $this->cat['description'];
    }


    /**
     *  getId
     *  -----
     *  @return string  dpla id hash
     *
     */

    public function getId() {
        return $this->cat['id'];
    }


    /**
     *  getIntro
     *  --------
     *  @return string  randomly chosen intro phrase
     *
     */

    public function getIntro() {
        return $this->cat['intro'];
    }


    /**
     *  getLink
     *  -------
     *  @return string  dpla url
     *
     */

    public function getLink() {
        return $this->buildDPLAUrl($this->cat['id']);
    }


    /**
     *  getTitle
     *  --------
     *  @return string  dpla title
     *
     */

    public function getTitle() {
        return $this->cat['title'];
    }


    /**
     *  meow
     *  ----
     *  tweet constructor. if static $tweet is true, we'll post to twitter and return true,
     *  otherwise we'll return the tweet as a string.
     *
     *  @return bool | string  confirmation of tweet or the message itself
     *
     */

    public function meow() {
        $message = $this->cat['intro']
                 . " "
                 . (strlen($this->cat['title']) > self::$tweetThreshold ? 
                        substr($this->cat['title'], 0, self::$tweetThreshold) . "…" . "\""
                      : $this->cat['title'])
                 . " "
                 . $this->buildDPLAUrl($this->cat['id'])
                 ;
        
        if ( self::$USE_DB ) {
            $this->querydb("insert into `tweets`(id, tweet) values(:id, :tweet)", array("id" => $this->cat['id'], "tweet" => $message), $error);
        }

        if ( self::$TWEET) {
            require "twitteroauth/twitteroauth/twitteroauth.php";

            $chirp = new TwitterOAuth(
                self::$twitter_info['consumer_key'],
                self::$twitter_info['consumer_secret'],
                self::$twitter_info['access_token'],
                self::$twitter_info['access_secret']
            );

            $post = $chirp->post('statuses/update', array('status' => $message));

            return $post ? true : false;
        
        } else {
            return $message;
        }

    }




    /**
     *  buildAPIRequest
     *  -------------
     *  our needs for the DPLA api are slimmer and more specific than 3ft9's php connector 
     *  (https://github.com/3ft9/php-dpla/) would be able provide us, so we'll build it ourselves.
     *  _NOTE:_ the type[] field isn't an officially documented one, but I pulled it from a search request
     *         using the web interface.
     *
     *  @param array of options
     *  @return uri string
     *
     */

    private function buildAPIRequest($query, $options = array()) {
        $defaultOptions = array(
            "field" => "sourceResource.subject.name",
            "page_size" => 1,
            "page" => 1,
            "type" => "image"
        );

        $options = array_merge($defaultOptions, $options);

        $url = $this->urlBase
             . $options['field'] . "=" . $this->query
             . "&page_size=" . $options['page_size']
             . "&page=" . $options['page']
             . "&sourceResource.type=" . $options['type']
             . "&api_key=" . $this->dplakey
             ;

        return $url;
    }

    /**
     *  buildDPLAUrl
     *  ------------
     *  @param string  dpla id hash
     *  @return string  dpla url
     */

    private function buildDPLAUrl($id) {
        return "http://dp.la/item/" . $id;
    }


    /**
     *  canHaz (suggested by @mrgunn)
     *  -----------------------------
     *  checks db to see if a tweet about the provided id exists
     *
     *  @param id  a dpla id hash
     *  @return bool  (true if not exists, false if does)
     *
     */

    public function canHaz($id) {
        $query = "select * from tweets where id = :id";
        $res = $this->querydb($query, array("id" => $id), $error);

        //if ( $error ) { return $error; }

        return count($res) > 0 ? false : true;
    }


    /**
     *  fetchResults
     *  ------------
     *  I wish there was a good cat equivalent to 'fetch'. 
     *  uses file_get_contents instead of the curl library
     *
     *  @param string  uri to fetch
     *  @param boolean  leave json-ld results or translate into php associative array
     *  @return array | (json) string  
     *
     */

    public function fetchResults($url, $json = false) {
        $contents = file_get_contents($url);
        return $json ? $contents : json_decode($contents, true);
    }


    /**
     *  querydb
     *  -------
     *  basic pdo connector
     *
     *  @param string  db query
     *  @param array  values to pass into prepared statement
     *  @param empty  var to store error message in
     *
     */

    private function querydb($query, $values = array(), &$errors = null) {
        $pdo = new PDO(self::$db_info['info'], self::$db_info['user'], self::$db_info['pass']);
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);

        $err = $stmt->errorInfo();
        $error = $err[2] ?: null;

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $res;
    }

}
