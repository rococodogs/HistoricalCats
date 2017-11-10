const promisify = require('util').promisify
const request = require('./dpla-request')
const randomWithBlacklist = require('./random-with-blacklist')

module.exports = (query, {blacklist, api_key}) => {
  const getCount = json => json.count
  const getRandomPage = total => randomWithBlacklist(total, blacklist)
  const getEntry = num => request(query, {api_key, page: num})
  const saveEntryToBlacklist = response => new Promise((resolve, reject) => {
    blacklist.add(response.start)
    blacklist.save(err => {
      if (err) return reject(err)
      return resolve(response)
    })
  })
  
  const extractDoc = results => results.docs[0]

  return request(query, {api_key})
    // first get the count
    .then(getCount)
    
    // then determine a random entry (that isn't in our blacklist)
    .then(getRandomPage)

    // then _get_ that entry
    .then(getEntry)

    // store the entry # 
    // (`results.start`, since we're limiting `page_size` to 1)
    .then(saveEntryToBlacklist)

    // then get the entry out of there
    .then(extractDoc)
}