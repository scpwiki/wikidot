/*
|--------------------------------------------------------------------------
| Main entry point
|--------------------------------------------------------------------------
| Files in the "resources/scripts" directory are considered entrypoints
| by default.
|
| -> https://vitejs.dev
| -> https://github.com/innocenzi/laravel-vite
*/

// styling
import "normalize.css/normalize.css"
import "@/css/app.css"

// mount editor
import { SheafCore } from "sheaf-core"
import { FTMLLanguage } from "cm-lang-ftml"
import { perfy } from "wj-util"
import * as FTML from "ftml-wasm-worker"

window.addEventListener("DOMContentLoaded", async () => {
  const editor = new SheafCore()
  const res = await fetch("/static/misc/ftml-test.ftml")
  if (!res) return
  const src = await res.text()
  await editor.init(document.querySelector(".editor-container")!, src, [
    FTMLLanguage.load()
  ])
  editor.subscribe(({ value }) => {
    ;(async () => {
      const log = perfy("ftml-perf", 5)
      console.log(await FTML.render(value))
      log()
    })()
  })
})
