import iterate from "iterare"
import { intersect, product, re } from "../util"
import type { Aff, Flags, FlagSet } from "./index"

export class CompoundRule {
  declare flags: Flags
  declare regex: RegExp
  declare partialRegex: RegExp

  constructor(rule: string, aff: Aff) {
    let parts: string[]
    if (rule.includes("(")) {
      this.flags = aff.parseFlags(/\((.+?)\)/.exec(rule)?.slice(1) ?? "")
      parts = /\([^*?]+?\)[*?]?/.exec(rule)?.slice(1) ?? []
    } else {
      this.flags = aff.parseFlags(rule.replaceAll(/[\*\?]/g, ""))
      parts = iterate(rule.matchAll(/[^*?][*?]?/g))
        .map(part => part[0].replaceAll(")", "\\)"))
        .toArray()
    }

    this.regex = re`/^${parts.join("")}$/`
    this.partialRegex = re`/^${parts.reduceRight((acc, cur) => `${cur}(${acc})?`)}$/`
  }

  match(flags: FlagSet, partial = false) {
    return iterate(flags)
      .map(f => product(...intersect(this.flags, f)))
      .some(fc => {
        const joined = iterate(fc).join("")
        return partial ? this.partialRegex.test(joined) : this.regex.test(joined)
      })
  }
}