const randomDplaItem = require('./lib/random-dpla-item')
const api_key = '4092ca2c3a8c4c4a7bc4c67b8abc56c1'
const cache_path = './blacklist-cache.json'

const blacklist = require('./lib/blacklist').openSync(cache_path)

randomDplaItem('cats', {blacklist, api_key})
  .then(item => console.log(item), console.warn)
