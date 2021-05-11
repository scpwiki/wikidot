const path = require("path")
const fs = require("fs-extra")
const docster = require("svelte-docster/typescript")
const globby = require("globby")

const ROOT = path.resolve(__dirname, "../../modules")

generateDocumentation()

async function collectComponents() {
  const files = await globby("*/src/**/*.svelte", { cwd: ROOT })
  const map = {}
  for (const file of files) {
    const src = await fs.readFile(path.resolve(ROOT, file), "utf-8")
    map[file] = src.replaceAll("\r\n", "\n")
  }
  return map
}

async function generateDocumentation() {
  const components = await collectComponents()
  const docs = {}

  Object.entries(components).forEach(([file, src]) => {
    const name = path.basename(file)

    const info = docster({ content: src, filename: name })
    docs[file] = info
  })

  fs.outputFile("dist/sveltedoc/docs.json", JSON.stringify(docs, undefined, 2))
}
