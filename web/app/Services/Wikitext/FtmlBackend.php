<?php
declare(strict_types = 1);

namespace Wikijump\Services\Wikitext;

/**
 * Class FtmlInterface, implements a compatible interface for working with FTML.
 * @package Wikijump\Services\Wikitext
 */
class FtmlBackend implements WikitextBackend
{
    private PageInfo $pageInfo;

    public function __construct(ParseRenderMode $mode, ?PageInfo $pageInfo)
    {
        // TODO mode
        $this->pageInfo = $pageInfo ?? self::defaultPageInfo();
    }

    // Interface methods
    public function renderHtml(string $wikitext): HtmlOutput
    {
        return FtmlFfi::renderHtml($wikitext, $this->pageInfo);
    }

    public function renderText(string $wikitext): TextOutput
    {
        return FtmlFfi::renderText($wikitext, $this->pageInfo);
    }

    public function version(): string
    {
        return FtmlFfi::version();
    }

    private static function defaultPageInfo(): PageInfo
    {
        return new PageInfo('_anonymous', null, 'www', '_anonymous', null, [], 'C');
    }
}
