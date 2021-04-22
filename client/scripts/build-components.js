const { build, cliopts, stdoutStyle: styl, fmtDuration } = require("estrella")
const { readdirSync } = require("fs")
const { performance } = require("perf_hooks")
const globby = require("globby")

const { nodeExternalsPlugin } = require("esbuild-node-externals")
const compileWorkersPlugin = require("./esbuild-compile-worker")

const sveltePreprocess = require("svelte-preprocess")
const sveltePlugin = require("esbuild-svelte")

// -- CONSTANTS, COMMAND LINE ARGUMENTS

const [opts] = cliopts.parse(["tests", "Compile the tests instead."])

const DEV = Boolean(cliopts.watch)
const TESTS = opts.tests ?? false

if (DEV && TESTS) throw new Error("Can't use -watch and -tests at the same time!")

const fmt = TESTS ? styl.orange : styl.blue

buildComponents()

async function buildComponents() {
  if (!DEV) console.log(fmt(`[components]`), "Building!")
  let start = performance.now()

  const index = `./src/index.ts`
  const sveltes = await globby("./src/**/*.svelte")

  let tests
  if (TESTS) {
    try {
      tests = filesOf(`./tests`).map(name => `./tests/${name}`)
      if (tests.length === 0) {
        console.log(fmt(`[components]`), "No tests, skipping.")
        return
      }
    } catch {
      // we'll be here if the `tests` folder doesn't exist
      console.log(fmt(`[components]`), "No tests, skipping.")
      return
    }
  }

  // see `build-modules.js` for a lot more explanation on this
  // most of this is taken from there

  build({
    // esbuild settings
    entryPoints: [index, ...sveltes],
    outdir: "dist",
    bundle: true,
    treeShaking: true,
    splitting: true,
    format: "esm",
    platform: "browser",
    sourcemap: true,
    sourcesContent: true,
    plugins: [
      nodeExternalsPlugin(),
      compileWorkersPlugin,
      sveltePlugin({ preprocess: sveltePreprocess() })
    ],

    // estrella settings
    tslint: false,
    quiet: true,

    // test compiling
    // prettier-ignore
    ...(!TESTS ? {} : {
      entry: [...tests],
      outdir: `./tests/dist`,
      bundle: true,
      minify: false,
      splitting: false,
      format: "cjs",
      platform: "node",
      sourcemap: false,
      target: undefined,
      outExtension: { ".js": ".cjs" },
      define: {
        "window": "globalThis"
      }
    }),

    // -- LOGGING HANDLERS

    onStart(_, changed) {
      if (DEV && changed && changed.length) {
        start = performance.now()
        console.log(
          fmt(`[components]`),
          "Rebuilding!",
          styl.orange(`[${changed.join(", ")}]`)
        )
      }
    },

    onEnd() {
      const elapsed = fmtDuration(performance.now() - start)
      console.log(fmt(`[components]`), "Finished.", styl.green(`${elapsed}`))
    }
  })
}

/** Gets the files (shallow) of a folder. */
function filesOf(source) {
  return readdirSync(source, { withFileTypes: true })
    .filter(dirent => dirent.isFile())
    .map(dirent => dirent.name)
}