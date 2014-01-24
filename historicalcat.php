<?php

class HistoricalCat {

    private $catPhrases = array(
        "Meow!", 
        "Purrrr!", 
        "Prrrr!",
        "Rrrrrr!", 
        "What a cat!",
        "Fine Feline!"
    );

    function __construct($max = 1500) {
        $this->max = $max;
        $queryArray = array(
            "cat", "cats", "kitten", "kittens", "feline"
        );

        $query = implode("+OR+", $queryArray);

        $random = rand(1, $this->max);

        $url = "http://api.dp.la/v2/items?sourceResource.subject.name=" . $query 
             . "&page_size=1"
             . "&page=" . $random 
             . urlencode("&type[]=image") 
             . "&api_key=" . DPLAKEY;

        $this->response = json_decode(file_get_contents($url), true);
        $this->getCat();
    }

    function getCat() {
        return $this->cat = $this->response['docs'][0];
    }

    function catId() {
        return $this->cat['id'];
    }

    function catTitle() {
        $title = is_array($this->cat['sourceResource']['title']) ?
                    $this->cat['sourceResource']['title'][0]
                  : $this->cat['sourceResource']['title'];

        $title = str_replace(array("\"", "'"), "", $title);

        return strlen($title) > 100 ?
                substr($title, 0, 100) . "&#8230;"
               : $title;
    }

    function catDPLAUrl() {
        return "http://dp.la/item/" . $this->catId();
    }

    function introPhrase() {
        shuffle($this->catPhrases);
        return $this->catPhrases[0];
    }


    function meow() {
        return $this->introPhrase() 
              . " "
              . "\"" . $this->catTitle() . "\""
              . " " 
              . $this->catDPLAUrl();
    }
}

?>