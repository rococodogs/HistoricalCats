const express = require('express')
const app = express()
const { query } = require('./app-config')
const randomItem = require('./lib/random-dpla-item')
const createTweet = require('./lib/create-tweet')
const blacklist = require('./lib/blacklist').openSync('./.data/blacklist.json')

const api_key = process.env.DPLA_KEY
const SECRET = process.env.SECRET_TOKEN

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
    .then(tweet => {
      res.json({tweet})
    })
})

app.listen(8000)