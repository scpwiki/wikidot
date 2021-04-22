import { defineConfig } from "laravel-vite"

export default defineConfig({
  resolve: {
    dedupe: ["@codemirror/state"]
  },
  build: {
    sourcemap: true,
    target: "esnext",
    minify: "esbuild",
    brotliSize: false
  }
})
