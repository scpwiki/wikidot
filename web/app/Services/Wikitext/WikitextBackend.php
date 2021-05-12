<?php
declare(strict_types = 1);

namespace Wikijump\Services\Wikitext;

use Wikidot\Utils\GlobalProperties;

interface WikitextBackend
{
    public function renderHtml(string $wikitext): HtmlOutput;
    public function renderText(string $wikitext): TextOutput;
    public function version(): string;
}

/**
 * Gets the WikitextBackend interface to allow for parsing, rendering, and related
 * wikitext transformation.
 *
 * @throws GlobalPropertiesException if the feature flag value is invalid
 */
function getWikitextBackend(ParseRenderMode $mode, ?PageInfo $pageInfo): WikitextBackend
{
    switch (GlobalProperties::$FEATURE_WIKITEXT_BACKEND) {
        case 'text_wiki':
            return new TextWikiBackend($mode, $pageInfo);
        case 'ftml':
            return new FtmlBackend($mode, $pageInfo);
        case 'null':
            return new NullBackend();
        default:
            throw new Exception('Wikitext backend feature flag invalid: ' . GlobalProperties::$FEATURE_WIKITEXT_BACKEND);
    }
}
