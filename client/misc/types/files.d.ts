/**
 *  @file Modules for correctly typing imported files.
 */

declare module "*.toml" {
  const json: JSONObject
  export default json
}

declare module "*?bundled-worker" {
  const text: string
  export default text
}
