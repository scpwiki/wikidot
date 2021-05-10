/*
 * render/utils.rs
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

use crate::log::prelude::*;
use crate::tree::{ElementCondition, ElementConditionType};
use crate::PageInfo;

pub fn check_ifcategory(
    log: &Logger,
    info: &PageInfo,
    conditions: &[ElementCondition],
) -> bool {
    let category = match &info.category {
        Some(category) => category,
        None => "_default",
    };

    debug!(
        log,
        "Checking ifcategory";
        "category" => category,
        "conditions-len" => conditions.len(),
    );

    // Check if any positive conditions match
    //
    // This isn't "all" because there is only on category,
    // at most one of those conditions could match,
    // so this behavior is much more sensible as OR.
    let positive_match = conditions
        .iter()
        .filter(|cond| cond.condition == ElementConditionType::Present)
        .any(|cond| cond.check_single(category));
    if !positive_match {
        return false;
    }

    // Check if all negative conditions match
    let negative_match = conditions
        .iter()
        .filter(|cond| cond.condition == ElementConditionType::Absent)
        .all(|cond| cond.check_single(category));
    if !negative_match {
        return false;
    }

    // All conditions match
    true
}

#[inline]
pub fn check_iftags(
    log: &Logger,
    info: &PageInfo,
    conditions: &[ElementCondition],
) -> bool {
    debug!(
        log,
        "Checking iftags";
        "tags-len" => info.tags.len(),
        "conditions-len" => conditions.len(),
    );

    // The check for iftags is all positive and negative conditions,
    // or AND for all, so this logic is much simpler.
    conditions.iter().all(|cond| cond.check(&info.tags))
}
