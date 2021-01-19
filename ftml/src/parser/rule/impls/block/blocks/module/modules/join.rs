/*
 * parser/rule/impls/block/blocks/module/modules/join.rs
 *
 * ftml - Library to parse Wikidot text
 * Copyright (C) 2019-2021 Ammon Smith
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

use super::prelude::*;

pub const MODULE_JOIN: ModuleRule = ModuleRule {
    name: "module-join",
    accepts_names: &["Join"],
    parse_fn,
};

fn parse_fn<'r, 't>(
    log: &slog::Logger,
    _parser: &mut Parser<'r, 't>,
    name: &'t str,
    mut arguments: Arguments<'t>,
) -> ParseResult<'r, 't, Module<'t>> {
    debug!(log, "Parsing join module");
    assert_module_name(&MODULE_JOIN, name);

    let button_text = arguments.get("button");
    let id = arguments.get("id");
    let class = arguments.get("class");
    let style = arguments.get("style");

    ok!(Module::Join {
        button_text,
        id,
        class,
        style
    })
}