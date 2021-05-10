/*
 * render/json.rs
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

//! A simple renderer that outputs the `SyntaxTree` as JSON.
//!
//! This implementation of `Render` will produce the same JSON
//! output as is used in the AST tests at `src/test.rs`.

use super::prelude::*;

#[derive(Debug)]
pub struct JsonRender {
    /// Whether to use the human-readable JSON formatter or the minified formatter.
    pub pretty: bool,
}

impl JsonRender {
    #[inline]
    pub fn pretty() -> Self {
        JsonRender { pretty: true }
    }

    #[inline]
    pub fn compact() -> Self {
        JsonRender { pretty: false }
    }
}

impl Render for JsonRender {
    type Output = String;

    fn render(
        &self,
        log: &Logger,
        page_info: &PageInfo,
        syntax_tree: &SyntaxTree,
    ) -> String {
        info!(log, "Running JSON logger on syntax tree"; "pretty" => self.pretty);

        // Get the JSON serializer
        let writer = if self.pretty {
            serde_json::to_string_pretty
        } else {
            serde_json::to_string
        };

        // Wrapper struct to provide both page info and the AST in the JSON.
        #[derive(Serialize, Debug)]
        struct JsonWrapper<'a> {
            syntax_tree: &'a SyntaxTree<'a>,
            page_info: &'a PageInfo<'a>,
        }

        let output = JsonWrapper {
            syntax_tree,
            page_info,
        };

        writer(&output).expect("Unable to serialize JSON")
    }
}

#[test]
fn json() {
    // Expected outputs
    const PRETTY_OUTPUT: &str = r#"{
  "syntax_tree": {
    "elements": [
      {
        "element": "text",
        "data": "apple"
      },
      {
        "element": "text",
        "data": " "
      },
      {
        "element": "container",
        "data": {
          "type": "bold",
          "elements": [
            {
              "element": "text",
              "data": "banana"
            }
          ],
          "attributes": {}
        }
      }
    ],
    "styles": [
      "span.hidden-text { display: none; }"
    ]
  },
  "page_info": {
    "page": "some-page",
    "category": null,
    "site": "sandbox",
    "title": "A page for the age",
    "alt-title": null,
    "rating": 69.0,
    "tags": [
      "tale",
      "_cc"
    ],
    "language": "en"
  }
}"#;

    const COMPACT_OUTPUT: &str = r#"{"syntax_tree":{"elements":[{"element":"text","data":"apple"},{"element":"text","data":" "},{"element":"container","data":{"type":"bold","elements":[{"element":"text","data":"banana"}],"attributes":{}}}],"styles":["span.hidden-text { display: none; }"]},"page_info":{"page":"some-page","category":null,"site":"sandbox","title":"A page for the age","alt-title":null,"rating":69.0,"tags":["tale","_cc"],"language":"en"}}"#;

    let log = crate::build_logger();
    let page_info = PageInfo::dummy();

    // Syntax tree construction
    let elements = vec![
        text!("apple"),
        text!(" "),
        Element::Container(Container::new(
            ContainerType::Bold,
            vec![text!("banana")],
            AttributeMap::new(),
        )),
    ];
    let warnings = vec![];
    let styles = vec![cow!("span.hidden-text { display: none; }")];

    let result = SyntaxTree::from_element_result(elements, warnings, styles);
    let (tree, _) = result.into();

    // Perform renderings
    let output = JsonRender::pretty().render(&log, &page_info, &tree);
    assert_eq!(
        output, PRETTY_OUTPUT,
        "Pretty JSON syntax tree output doesn't match",
    );

    let output = JsonRender::compact().render(&log, &page_info, &tree);
    assert_eq!(
        output, COMPACT_OUTPUT,
        "Compact JSON syntax tree output doesn't match",
    );
}
