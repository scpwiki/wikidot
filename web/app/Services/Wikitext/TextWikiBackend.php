<?php
declare(strict_types = 1);

namespace Wikijump\Services\Wikitext;

use \Wikidot\Utils\WikiTransformation;

class TextWikiBackend implements WikitextBackend
{
    private WikiTransformation $wt;

    public function __construct(string $mode, ?PageInfo $pageInfo) {
        $this->wt = new WikiTransformation();
        // TODO
    }

    public function version(): string {
        return 'Text_Wiki 0.0.1';
    }
}
