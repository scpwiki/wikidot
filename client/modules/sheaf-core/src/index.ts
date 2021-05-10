/**
 * @file Exports {@link SheafCore}, the core class that wraps around CodeMirror 6.
 */

import { EditorState, Extension, Compartment } from "@codemirror/state"
import {
  EditorView,
  ViewPlugin,
  ViewUpdate,
  drawSelection,
  keymap,
  highlightActiveLine,
  highlightSpecialChars
} from "@codemirror/view"
import { history, historyKeymap } from "@codemirror/history"
import { foldGutter, foldKeymap } from "@codemirror/fold"
import { syntaxTree, indentOnInput } from "@codemirror/language"
import { lineNumbers } from "@codemirror/gutter"
import { defaultKeymap, defaultTabBinding } from "@codemirror/commands"
import { bracketMatching } from "@codemirror/matchbrackets"
import { closeBrackets, closeBracketsKeymap } from "@codemirror/closebrackets"
import { highlightSelectionMatches, searchKeymap } from "@codemirror/search"
import { autocompletion, completionKeymap } from "@codemirror/autocomplete"
import { commentKeymap } from "@codemirror/comment"
import { rectangularSelection } from "@codemirror/rectangular-selection"
import { redo } from "@codemirror/history"
import { copyLineDown } from "@codemirror/commands"
import { nextDiagnostic, openLintPanel } from "@codemirror/lint"

import { writable } from "svelte/store"

import { debounce } from "wj-util"
import { printTree } from "./print-tree"
import { confinement } from "./theme"
import { indentHack } from "./extensions/indent-hack"

export * from "./adapters/svelte-lifecycle-element"
export * from "./adapters/svelte-dom"
export * from "./adapters/svelte-panel"

interface EditorStore {
  /** The current document of the editor. */
  doc: EditorState["doc"]
  /** The current 'value' (content) of the editor. */
  value: string
}

export class SheafCore {
  /** The element the editor is attached to. */
  parent!: Element

  /** The CodeMirror `EditorState` the editor has currently.
   *  The state is immutable and is replaced as the editor updates. */
  state = EditorState.create()

  /** The CodeMirror `EditorView` instance the editor interacts with the DOM with. */
  view!: EditorView

  /** A store that allows reactive access to editor state. */
  store = writable<EditorStore>({ doc: this.state.doc, value: "" })
  subscribe = this.store.subscribe
  set = this.store.set

  /** The lines currently being interacted with by the user.
   *  This includes all selected lines, the line the cursor is present on, etc. */
  activeLines = writable(new Set<number>())

  private spellcheckCompartment = new Compartment()
  private guttersCompartment = new Compartment()

  /** Starts the editor. */
  async init(parent: Element, doc: string, extensions: Extension[] = []) {
    this.parent = parent

    const updateState = debounce(() => this.refresh(), 50)

    const updateHandler = ViewPlugin.define(() => ({
      update: (update: ViewUpdate) => {
        // update store on change
        if (update.docChanged) updateState()
        // get active lines
        if (update.selectionSet || update.docChanged) {
          const activeLines: Set<number> = new Set()
          for (const range of update.state.selection.ranges) {
            const lnStart = update.state.doc.lineAt(range.from).number
            const lnEnd = update.state.doc.lineAt(range.to).number
            if (lnStart === lnEnd) activeLines.add(lnStart - 1)
            else {
              const diff = lnEnd - lnStart
              for (let lineNo = 0; lineNo <= diff; lineNo++) {
                activeLines.add(lnStart + lineNo - 1)
              }
            }
          }
          this.activeLines.set(activeLines)
        }
      }
    }))

    this.view = new EditorView({
      parent,
      state: EditorState.create({
        doc,
        extensions: [
          ...getExtensions(),
          ...extensions,
          this.spellcheckCompartment.of(
            EditorView.contentAttributes.of({ spellcheck: "false" })
          ),
          this.guttersCompartment.of(EditorView.editorAttributes.of({ class: "" })),
          updateHandler
        ]
      })
    })

    this.refresh()
  }

  /** The `Text` object of the editor's current state. */
  get doc() {
    return this.view.state.doc
  }

  get scrollTop() {
    return this.view.scrollDOM.scrollTop
  }

  set scrollTop(val: number) {
    this.view.scrollDOM.scrollTop = val
  }

  /** Destroys the editor.
   *  Usage of the editor object after destruction is obviously not recommended. */
  destroy() {
    this.view.destroy()
  }

  refresh() {
    this.state = this.view.state
    const value = this.doc.toString()
    this.store.set({
      doc: this.doc,
      value: value
    })
  }

  /** Returns the scroll-offset from the top of the editor for the specified line. */
  heightAtLine(line: number) {
    return this.view.visualLineAt(this.doc.line(line).from).top
  }

  printTree() {
    return printTree(syntaxTree(this.state), this.doc.toString())
  }

  /** Whether or not the browser's spellchecker is enabled for the editor. */
  set spellcheck(state: boolean) {
    this.view.dispatch({
      effects: this.spellcheckCompartment.reconfigure(
        EditorView.contentAttributes.of({ spellcheck: String(state) })
      )
    })
  }

  /** Whether or not the line-numbers and associated gutter panel is shown. */
  set gutters(state: boolean) {
    this.view.dispatch({
      effects: this.guttersCompartment.reconfigure(
        EditorView.editorAttributes.of({ class: state ? "" : "hide-gutters" })
      )
    })
  }
}

export function getExtensions() {
  return [
    lineNumbers(),
    highlightSpecialChars(),
    history(),
    foldGutter(),
    drawSelection(),
    EditorState.allowMultipleSelections.of(true),
    indentOnInput(),
    bracketMatching(),
    closeBrackets(),
    highlightSelectionMatches(),
    autocompletion(),
    rectangularSelection(),
    highlightActiveLine(),
    EditorView.lineWrapping,
    indentHack,
    keymap.of([
      ...closeBracketsKeymap,
      ...defaultKeymap,
      ...searchKeymap,
      ...historyKeymap,
      ...foldKeymap,
      ...commentKeymap,
      ...completionKeymap,
      { key: "Mod-l", run: openLintPanel, preventDefault: true },
      { key: "F8", run: nextDiagnostic, preventDefault: true },
      { key: "Mod-Shift-z", run: redo, preventDefault: true },
      { key: "Mod-d", run: copyLineDown, preventDefault: true },
      defaultTabBinding
    ]),
    confinement
  ]
}
