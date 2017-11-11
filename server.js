const express = require('express')
const Twitter = require('twitter')
const app = express()
const { query } = require('./app-config')
const randomItem = require('./lib/random-dpla-item')
const createTweet = require('./lib/create-tweet')
const blacklist = require('./lib/blacklist').openSync('./.data/blacklist.json')

const api_key = process.env.DPLA_KEY
const SECRET = process.env.SECRET_TOKEN

const {
  TWITTER_CONSUMER_KEY,
  TWITTER_CONSUMER_SECRET,
  TWITTER_ACCESS_TOKEN,
  TWITTER_ACCESS_SECRET
} = process.env

const twitterClient = new Twitter({
  consumer_key: TWITTER_CONSUMER_KEY,
  consumer_secret: TWITTER_CONSUMER_SECRET,
  access_token_key: TWITTER_ACCESS_TOKEN,
  access_token_secret: TWITTER_ACCESS_SECRET
})

const postToTwitter = status => twitterClient.post('statuses/update', {status})

app.get('/', (req, res) => {
  res.json({status: '🐈'})
})

app.post('/tweet', (req, res) => {
  const header = req.get('x-tweet-me-maybe')

  if (!header || header !== SECRET) {
    res.status(401)
    return res.json({
      error: 'unauthorized',
      message: 'sorry friend, only authorized users plz',
    })
  }

  randomItem(query, {blacklist, api_key})
    .then(createTweet)
    .then(postToTwitter)
    .then(tweet => res.json({status: 'success', tweet}))
    .catch(err => {
      res.status(500)
      res.json({status: 'error', errors: err})
    })
})

app.listen(8000)