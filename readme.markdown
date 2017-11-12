[@HistoricalCats][twitter]
==========================

[![Remix on Glitch](https://cdn.glitch.com/2703baf2-b643-4da7-ab91-7ee2a2d00b5b%2Fremix-button.svg)](https://glitch.com/edit/#!/remix/historical-cats)

the code that runs a bot tweeting random cat-related items in the
[Digital Public Library of America][dpla]. this is a rewrite of
the original PHP code written in 2014 at [LibHack] in Philadelphia.


remixing / running on yr own
----------------------------

I tried to set this up so that this could be cloned + remixed as easily
as possible. After cloning / remixing: 

- the `.env` file will need to contain your [DPLA API key][dpla-key], your
  [twitter credentials](https://apps.twitter.com), and a secret key that
  you create.
- the `app-config.js` file contains things pertinent to your bot including:
  prefixed phrases, emoji, item title max-length, and the amount of items
  to keep track of so your bot doesn't run the risk of posting the same
  thing.

note, these files also contain this information + more in their respective
comments.


getting it to tweet
-------------------

so, this is a little annoying, but in order for this bot to tweet, you have
to poke it by means of a `POST` request with a special header. this is so
visitors to your endpoint don't cause your bot to get flagged by twitter and/or
dpla. the beauty of this glitch thing is that you can easily rewrite the stuff in
`server.js` to trigger a tweet whenever you want!

I have a cron job scheduled to send a `curl` request to this app with the 
special header running 3 times a day from a linode server.

so if, say, `process.env.SECRET_TOKEN_VALUE === 'hi there! ^_^`, then:

```
curl -X POST -H "x-tweet-me-maybe: hi there! ^_^" https://yr-app-here.glitch.com/tweet
```

would trigger a tweet.


questions?
----------

leave an [issue] or [tweet me][my-twitter].

[twitter]: https://twitter.com/HistoricalCats
[dpla]: https://dp.la
[LibHack]: http://web.archive.org/web/20140401203233/http://www.libhack.org/
[dpla-key]: https://dp.la/info/developers/codex/policies/#get-a-key
[issue]: https://github.com/malantonio/HistoricalCats/issues
[my-twitter]: https://twitter.com/afrmalantonio