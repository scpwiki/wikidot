import svelte from "@sveltejs/vite-plugin-svelte"
import sveltePreprocess from "svelte-preprocess"
import workerPlugin from "../../scripts/vite-plugin-bundled-worker.js"
import tomlPlugin from "../../scripts/vite-plugin-toml.js"

/** @type import("sass").Options */
const SASS_OPTIONS = {
  sourceMapEmbed: true,
  sourceMapContents: true,
  sourceMap: true
}

/** @type {import('vite').UserConfig} */
const config = {
  publicDir: "../public",
  root: "./src",

  resolve: {
    dedupe: ["@codemirror/state", "@codemirror/view", "@codemirror/language"]
  },

  build: {
    outDir: "../dist",
    emptyOutDir: true,
    assetsDir: "static/assets",
    manifest: true,
    sourcemap: true,
    target: "esnext",
    minify: "esbuild",
    brotliSize: false,
    cssCodeSplit: false,
    cleanCssOptions: {
      sourceMap: true
    }
  },

  css: {
    preprocessorOptions: {
      scss: SASS_OPTIONS
    }
  },

  plugins: [
    workerPlugin(),
    tomlPlugin(),
    svelte({
      preprocess: [
        sveltePreprocess({
          sass: { sourceMapEmbed: true, sourceMapContents: true, sourceMap: true }
        })
      ]
    })
  ]
}

export default config
