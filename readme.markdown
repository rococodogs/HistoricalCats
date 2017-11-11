[@HistoricalCats][twitter]
==========================

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

Note, these files also contain this information + more in their respective
comments.


getting it to tweet
-------------------

sending a tweet is a matter of sending a `POST` request to the server with the
header provided.

so if, say, `process.env.SECRET_TOKEN_VALUE === 'hi there! ^_^`, then:

```
curl \
-X POST \
-H "x-tweet-me-maybe: hi there! ^_^" \
https://yr-app-here.glitch.com/tweet
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