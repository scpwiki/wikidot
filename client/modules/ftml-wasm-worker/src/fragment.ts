import { toFragment } from "wj-util"
import * as FTML from "./index"

export class FTMLFragment {
  private declare style: string
  private declare fragment: DocumentFragment
  private declare src: string

  ready = false

  constructor(src: string) {
    this.src = src
  }

  async render() {
    if (!this.ready) {
      const { html, style } = await FTML.render(this.src)
      const fragment = toFragment(html)
      this.fragment = fragment
      this.style = style
      this.ready = true
    }
    return this.unwrap()!
  }

  unwrap() {
    if (this.ready) {
      return { fragment: this.fragment.cloneNode(), style: this.style }
    }
  }
}
