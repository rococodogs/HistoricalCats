const {
  emoji,
  tweet_prefixes,
  item_title_max_length,
} = require('../app-config')

const trimTitle = title => (
  title.length > item_title_max_length
  ? title.substr(0, item_title_max_length - 1) + '…'
  : title
)

const getTitle = rawTitle => {
  const str = Array.isArray(rawTitle) ? rawTitle[0] : rawTitle
  const pre_quote = str[0] === '"' ? '' : '"'
  let trimmed = trimTitle(pre_quote + str)

  if (trimmed.substr(-1) !== '"') {
    trimmed = `${trimmed}"`
  }

  return trimmed
}

const randomEntryFn = arr => () => arr[Math.floor(Math.random() * arr.length)]
const randomEmoji = emoji ? randomEntryFn(emoji) : () => ''
const randomPrefix = randomEntryFn(tweet_prefixes)

const buildDplaUrl = id => `https://dp.la/item/${id}`

module.exports = item => {
  const prefix = randomPrefix()
  const emoji = randomEmoji()
  const title = getTitle(item.sourceResource.title)
  const url = buildDplaUrl(item.id)

  return `${prefix} ${emoji ? emoji + ' ' : ''}${title} ${url}`
}