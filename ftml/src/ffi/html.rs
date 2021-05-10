/*
 * ffi/html.rs
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

use super::prelude::*;
use super::warning::ftml_warning;
use crate::parsing::ParseWarning;
use crate::render::html::{HtmlMeta, HtmlMetaType, HtmlOutput};

#[repr(C)]
#[derive(Debug)]
pub enum ftml_html_meta_type {
    META_NAME,
    META_HTTP_EQUIV,
    META_PROPERTY,
}

impl From<HtmlMetaType> for ftml_html_meta_type {
    #[inline]
    fn from(tag_type: HtmlMetaType) -> ftml_html_meta_type {
        use ftml_html_meta_type::*;

        match tag_type {
            HtmlMetaType::Name => META_NAME,
            HtmlMetaType::HttpEquiv => META_HTTP_EQUIV,
            HtmlMetaType::Property => META_PROPERTY,
        }
    }
}

#[repr(C)]
#[derive(Debug)]
pub struct ftml_html_meta {
    pub tag_type: ftml_html_meta_type,
    pub name: *mut c_char,
    pub value: *mut c_char,
}

impl From<HtmlMeta> for ftml_html_meta {
    fn from(meta: HtmlMeta) -> ftml_html_meta {
        let HtmlMeta {
            tag_type,
            name,
            value,
        } = meta;

        ftml_html_meta {
            tag_type: tag_type.into(),
            name: string_to_cstr(name),
            value: string_to_cstr(value),
        }
    }
}

#[repr(C)]
#[derive(Debug)]
pub struct ftml_html_output {
    pub html: *mut c_char,
    pub style: *mut c_char,
    pub meta_list: *mut ftml_html_meta,
    pub meta_len: usize,
    pub warning_list: *mut ftml_warning,
    pub warning_len: usize,
}

impl ftml_html_output {
    pub fn write_from(&mut self, output: HtmlOutput, warnings: &[ParseWarning]) {
        self.html = string_to_cstr(output.html);
        self.style = string_to_cstr(output.style);

        let c_meta = output.meta.into_iter().map(ftml_html_meta::from).collect();
        let (meta_ptr, meta_len) = vec_to_cptr(c_meta);
        self.meta_list = meta_ptr;
        self.meta_len = meta_len;

        let c_warnings = warnings.iter().map(ftml_warning::from).collect();
        let (warning_ptr, warning_len) = vec_to_cptr(c_warnings);
        self.warning_list = warning_ptr;
        self.warning_len = warning_len;
    }
}

/// Destructs the given ftml_html_output structure, freeing all resources.
/// The structure must not be used after this point.
#[no_mangle]
pub unsafe extern "C" fn ftml_destroy_html_output(ptr: *mut ftml_html_output) {
    let this = &mut *ptr;

    drop_cstr(this.html);
    drop_cstr(this.style);
    drop_cptr(this.meta_list, this.meta_len, |item| {
        drop_cstr(item.name);
        drop_cstr(item.value);
    });
}
