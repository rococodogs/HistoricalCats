module.exports = {
  // this is the query text used in the DPLA search. for best results
  // chain the singular + plural of your term(s) using OR
  query: 'cat OR cats OR kitten OR kittens OR feline',

  // DPLA field to query against. we're using `sourceResource.subject.name`
  // by default to capture items containing subjects matching the above
  // terms
  field: 'sourceResource.subject.name',

  // this allows us to limit the type of item to query against. previously
  // we were getting results that contained items without images, which 
  // aren't super exciting in a twitter bot
  source_resource_type: 'image',

  // tweets are set-up to be "<prefix> <emoji> <item title> <object url>"

  // one of these phrases is chosen at random to prefix the item.
  tweet_prefixes: [
    'Meow!', 
    'Purrrr!', 
    'Prrrr!',
    'Some kitten!',
    'What a cat!',
    'Fine Feline!',
    'Such meow!',
  ],

  // one of these emoji is chosen at random to be inserted between the prefix
  // and item title. if you'd like to omit it completely, just delete the array.
  // only want to use one emoji? have it be the only object in the array.
  emoji: [
    '🐱',
    '😺',
    '😸',
    '😽',
    '😻',
    '😼',
    '🐈',
  ],

  // we trim titles of 100 chars+ so that they fit in tweets. this was done to
  // meet the 140 character limit which has (as of November 2017) been bumped
  // up to 280 characters. you could increase this number if you'd like the full
  // title to appear. I personally prefer the shorter tweet style and am leaving
  // this be for now.
  item_title_max_length: 100,

  // in the old PHP app, we used a MySQL database to store the previous entries.
  // this allowed us to make sure we weren't reposting any content within a 
  // 6 month window. this process has been converted to a javascript Map (stored
  // as a JSON array in `.data/blacklist.json`) for this app. what is stored is
  // the result page # (really the item index #, since every request is limited
  // to 1 item per page), as we're assuming the query will remain static. this
  // was chosen to prevent API requests every time that a random request returned
  // an item within the embargo period. now we hang on to the 5000 most recent
  // page #s.
  //
  // note: before deploying your bot, make a query and note the total # of results
  // returned. you'll want to update this value so that it is not more than the
  // total number (ideally, you'd want to set it to something like total - 50).
  // when the number of stored entries in the Blacklist === this value, the 
  // oldest entries are popped until this limit is reached.
  max_stored_entries: 5000,
}