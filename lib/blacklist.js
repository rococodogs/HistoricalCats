// instead of using a database, let's use a flat file
const fs = require('fs')
const path = require('path')

class Blacklist extends Set {
  static open (filepath, opts, cb) {
    if (typeof opts === 'function') {
      cb = opts
      opts = {path: filepath}
    }

    const resolvedPath = path.resolve(filepath)
    opts.resolved_path = resolvedPath

    return fs.readFile(resolvedPath, 'utf8', (err, data) => {
      // if the file doesn't exist yet, let's create it
      if (err) {
        if (err.code === 'ENOENT') {
          data = '[]'
        } else {
          return cb(err)
        }
      }

      try {
        const parsed = this.parse(data)
        parsed.path = resolvedPath

        return cb(null, parsed)
      } catch (e) {
        return cb(e)
      }
    })
  }

  static openSync (filepath) {
    const resolvedPath = path.resolve(filepath)

    // if the file doesn't exist yet, let's create it
    let raw

    try {
      raw = fs.readFileSync(resolvedPath, 'utf8')
    } catch (e) {
      if (e.code === 'ENOENT') {
        raw = '[]'
      } else {
        throw e
      }
    }

    const parsed = this.parse(raw)
    parsed.path = resolvedPath

    return parsed
  }

  static parse (str) {
    return new this(JSON.parse(str))
  }

  resize (size) {
    if (this.size <= size) {
      return
    }

    const iter = this.values()

    while (this.size > size) {
      this.delete(iter.next().value)
    }
  }

  save (filepath, cb) {
    if (typeof filepath === 'function' && this.path) {
      cb = filepath
      filepath = this.path
    }

    const fullPath = path.resolve(filepath)
    return fs.writeFile(fullPath, this.toString(), 'utf8', cb)
  }

  toString () {
    return JSON.stringify([...this])
  }
}

module.exports = Blacklist