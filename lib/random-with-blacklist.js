const random = max => Math.floor(Math.random() * max)

module.exports = (max, blacklist) => {
  if (max === blacklist.size) {
    throw new Error('blacklist is full')
  }
  
  let rand
  do {
    rand = random(max)
  } while (blacklist.has(rand))

  return rand
}
