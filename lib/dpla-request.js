const r2 = require('r2')
const qs = require('querystring')

const API_BASE_URL = 'https://api.dp.la/v2/items'

const defaultOptions = {
  field: 'sourceResource.subject.name',
  page_size: 1,
  page: 1,
  type: 'image'
}

module.exports = async (query, {field, page_size, page, type, api_key}) => {
  const options = {
    [field || 'sourceResource.subject.name']: query,
    'sourceResource.type': type || 'image',
    page_size: page_size || 1,
    page: page || 1,
    api_key,
  }

  if (!options.api_key) {
    return Promise.reject(new Error('`api_key` wasn\'t defined'))
  }
  
  const url = `${API_BASE_URL}?${qs.stringify(options)}`

  try {
    const jsonResponse = await r2(url).json
    return Promise.resolve(jsonResponse)
  } catch (err) {
    return Promise.reject(err)
  }
}