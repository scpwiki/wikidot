/*
 * test.rs
 *
 * ftml - Library to parse Wikidot text
 * Copyright (C) 2019-2021 Wikijump Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

//! Retrieves tests from JSON in the root `/test` directory, and runs them.
//!
//! Additionally performs some other tests from the parser which are better
//! in a dedicated test file.

use crate::includes::DebugIncluder;
use crate::log::prelude::*;
use crate::parsing::{ParseWarning, ParseWarningKind, Token};
use crate::render::html::HtmlRender;
use crate::render::text::TextRender;
use crate::render::Render;
use crate::tree::{Element, SyntaxTree};
use crate::PageInfo;
use std::borrow::Cow;
use std::fs::{self, File};
use std::io::Read;
use std::path::{Path, PathBuf};
use void::ResultVoidExt;

/// Temporary measure to not run certain tests.
///
/// This is meant to help with development, or in specific circumstances
/// where it is known functionality is broken while alternatives are
/// being developed.
const SKIP_TESTS: &[&str] = &[];

/// Temporary measure to only run certain tests.
///
/// This can assist with development, when you only care about specific
/// tests to check if certain functionality is working as expected.
const ONLY_TESTS: &[&str] = &[];

lazy_static! {
    static ref TEST_DIRECTORY: PathBuf = {
        let mut path = PathBuf::from(env!("CARGO_MANIFEST_DIR"));
        path.push("test");
        path
    };
}

macro_rules! cow {
    ($text:expr) => {
        Cow::Borrowed(&$text)
    };
}

macro_rules! file_name {
    ($entry:expr) => {
        $entry.file_name().to_string_lossy()
    };
}

#[cfg(not(target_os = "windows"))]
fn process_newlines(_: &mut String) {}

#[cfg(target_os = "windows")]
fn process_newlines(text: &mut String) {
    while let Some(idx) = text.find("\r\n") {
        let range = idx..idx + 2;
        text.replace_range(range, "\n");
    }
}

#[derive(Serialize, Deserialize, Debug)]
struct Test<'a> {
    #[serde(skip)]
    name: String,
    input: String,
    tree: SyntaxTree<'a>,
    warnings: Vec<ParseWarning>,

    #[serde(skip)]
    html: String,

    #[serde(skip)]
    text: String,
}

impl Test<'_> {
    pub fn load(path: &Path, name: &str) -> Self {
        assert!(path.is_absolute());

        macro_rules! open_file {
            ($path:expr) => {
                match File::open(&$path) {
                    Ok(file) => file,
                    Err(error) => {
                        panic!("Unable to open file '{}': {}", $path.display(), error)
                    }
                }
            };
        }

        macro_rules! load_output {
            ($name:expr, $extension:expr, $trim_newline:expr) => {{
                let mut path = PathBuf::from(path);
                path.set_extension($extension);

                let mut file = open_file!(path);
                let mut contents = String::new();

                if let Err(error) = file.read_to_string(&mut contents) {
                    panic!(
                        "Unable to read {} file '{}': {}",
                        $name,
                        path.display(),
                        error,
                    );
                }

                process_newlines(&mut contents);

                if $trim_newline && contents.ends_with('\n') {
                    contents.pop();
                }

                contents
            }};
        }

        // Load JSON file
        let mut file = open_file!(path);
        let mut test: Self = match serde_json::from_reader(&mut file) {
            Ok(test) => test,
            Err(error) => {
                panic!("Unable to parse JSON file '{}': {}", path.display(), error)
            }
        };

        test.name = str!(name);
        test.html = load_output!("HTML", "html", true);
        test.text = load_output!("text", "txt", false);
        test
    }

    pub fn run(&self, log: &Logger) {
        info!(
            &log,
            "Running syntax tree test case";
            "name" => &self.name,
            "input" => &self.input,
        );

        if SKIP_TESTS.contains(&&*self.name) {
            println!("+ {} [SKIPPED]", self.name);
            return;
        }

        if !ONLY_TESTS.is_empty() && !ONLY_TESTS.contains(&&*self.name) {
            println!("+ {} [SKIPPED]", self.name);
            return;
        }

        println!("+ {}", self.name);

        let page_info = PageInfo {
            page: Cow::Owned(format!("page-{}", self.name)),
            category: None,
            site: cow!("test"),
            title: cow!(self.name),
            alt_title: None,
            rating: 0.0,
            tags: vec![cow!("fruit"), cow!("component")],
            language: cow!("default"),
        };

        let (mut text, _pages) =
            crate::include(log, &self.input, DebugIncluder, || unreachable!())
                .void_unwrap();

        crate::preprocess(log, &mut text);
        let tokens = crate::tokenize(log, &text);
        let result = crate::parse(log, &tokens);
        let (tree, warnings) = result.into();
        let html_output = HtmlRender.render(log, &page_info, &tree);
        let text_output = TextRender.render(log, &page_info, &tree);

        fn json<T>(object: &T) -> String
        where
            T: serde::Serialize,
        {
            let mut output = serde_json::to_string_pretty(object)
                .expect("Unable to serialize JSON to stdout");

            output.insert_str(0, "Generated JSON: ");
            output
        }

        if tree != self.tree {
            panic!(
                "Running test '{}' failed! AST did not match:\nExpected: {:#?}\nActual: {:#?}\n{}\nWarnings: {:#?}",
                self.name,
                self.tree,
                tree,
                json(&tree),
                &warnings,
            );
        }

        if warnings != self.warnings {
            panic!(
                "Running test '{}' failed! Warnings did not match:\nExpected: {:#?}\nActual: {:#?}\n{}\nTree (correct): {:#?}",
                self.name,
                self.warnings,
                warnings,
                json(&warnings),
                &tree,
            );
        }

        if html_output.body != self.html {
            panic!(
                "Running test '{}' failed! HTML does not match:\nExpected: {:?}\nActual: {:?}\n\n{}\n\nTree (correct): {:#?}",
                self.name,
                self.html,
                html_output.body,
                html_output.body,
                &tree,
            );
        }

        if text_output != self.text {
            panic!(
                "Running test '{}' failed! Text output does not match:\nExpected: {:?}\nActual: {:?}\n\n{}\n\nTree (correct): {:#?}",
                self.name,
                self.text,
                text_output,
                text_output,
                &tree,
            );
        }
    }
}

#[test]
fn ast_and_html() {
    let log = crate::build_logger();

    // Warn if any test are being skipped
    if !SKIP_TESTS.is_empty() {
        println!("=========");
        println!(" WARNING ");
        println!("=========");
        println!();
        println!("The following tests are being SKIPPED:");

        for test in SKIP_TESTS {
            println!("- {}", test);
        }

        println!();
    }

    // Warn if we're only checking certain tests
    if !ONLY_TESTS.is_empty() {
        println!("=========");
        println!(" WARNING ");
        println!("=========");
        println!();
        println!("Only the following tests are being run.");
        println!("All others are being SKIPPED!");

        for test in ONLY_TESTS {
            println!("- {}", test);
        }

        println!();
    }

    // Load tests from JSON files
    let entries = fs::read_dir(&*TEST_DIRECTORY) //
        .expect("Unable to read directory");

    let tests_iter = entries.filter_map(|entry| {
        let entry = entry.expect("Unable to read directory entry");
        let ftype = entry.file_type().expect("Unable to get file type");
        if !ftype.is_file() {
            println!("Skipping non-file {}", file_name!(entry));
            return None;
        }

        let path = entry.path();
        let stem = path
            .file_stem()
            .expect("Unable to get file stem")
            .to_string_lossy();

        let extension = path.extension().map(|s| s.to_str()).flatten();
        match extension {
            // Load JSON test data
            Some("json") => Some(Test::load(&path, &stem)),

            // We expect these, don't print anything
            Some("html") | Some("txt") => None,

            // Print for other, unexpected files
            _ => {
                println!("Skipping non-JSON file {}", file_name!(entry));
                None
            }
        }
    });

    // Sort tests by name
    let mut tests: Vec<Test> = tests_iter.collect();
    tests.sort_by(|a, b| (a.name).cmp(&b.name));

    // Run tests
    println!("Running tests:");
    for test in tests {
        test.run(&log);
    }
}

/// Test the parser's recursion limit.
///
/// Manually implemented test, since this test would be
/// tremendously huge on disk as a JSON file, and
/// also goes past serde_json's recursion limit, lol.
#[test]
fn recursion_depth() {
    let log = crate::build_logger();

    // Build wikitext input
    let mut input = String::new();

    for _ in 0..101 {
        input.push_str("[[div]]\n");
    }

    for _ in 0..101 {
        input.push_str("[[/div]]\n");
    }

    // Run parser steps
    crate::preprocess(&log, &mut input);
    let tokens = crate::tokenize(&log, &input);
    let (tree, warnings) = crate::parse(&log, &tokens).into();

    // Check outputted warnings
    let warning = warnings.get(0).expect("No warnings produced");
    assert_eq!(warning.token(), Token::LeftBlock);
    assert_eq!(warning.rule(), "block-div");
    assert_eq!(warning.span(), 800..802);
    assert_eq!(warning.kind(), ParseWarningKind::RecursionDepthExceeded);

    // Check syntax tree
    //
    // It outputs the entire input string as text

    let SyntaxTree { elements, .. } = tree;
    assert_eq!(elements.len(), 1);

    let element = elements.get(0).expect("No elements produced");
    let input_cow = Cow::Borrowed(input.as_ref());
    assert_eq!(element, &Element::Text(input_cow));
}

/// Test the parser's ability to process large bodies
#[test]
fn large_payload() {
    const ITERATIONS: usize = 50;

    let log = crate::build_logger();

    // Build wikitext input
    let mut input = String::new();

    for _ in 0..ITERATIONS {
        // Lines intentionally broken in weird places
        input.push_str("
[[div]]
Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Maecenas sed risus sed ex suscipit ultricies ac quis metus.
Mauris facilisis dui quam, in mollis velit ultrices vitae. Nam pretium accumsan arcu eu ultricies. Sed viverra eleifend elit at blandit. Aenean tempor vitae ipsum vitae lacinia.
Proin eu maximus nulla, id imperdiet libero. Duis convallis posuere arcu vitae sodales. Cras porta ac ligula non porttitor.
Proin et sodales arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris eget ante maximus, tincidunt enim nec, dignissim mi.
Quisque tincidunt convallis faucibus. Praesent vel semper dolor, vel tincidunt mi.

In hac habitasse platea dictumst. Vestibulum fermentum libero nec erat porttitor fermentum. Etiam at convallis odio, gravida commodo ipsum. Phasellus consequat nisl vitae ultricies pulvinar. Integer scelerisque eget nisl id fermentum. Pellentesque pretium, enim non molestie rhoncus, dolor diam porta mauris, eu cursus dolor est condimentum nisi. Phasellus tellus est, euismod non accumsan at, congue eget erat.

% ]] ! $ * -- @< _
[[/div]]
        ");
    }

    // Run parser steps
    crate::preprocess(&log, &mut input);
    let tokens = crate::tokenize(&log, &input);
    let (_tree, warnings) = crate::parse(&log, &tokens).into();

    // Check output
    assert_eq!(warnings.len(), ITERATIONS * 3);
}
