<?php
declare(strict_types = 1);

namespace Wikijump\Services\Wikitext;

/**
 * Class ParseWarning, representing a particular issue which occurred while parsing some wikitext.
 * @package Wikijump\Services\Wikitext
 */
class ParseWarning
{
    /**
     * @var string The name of the parser token this warning occurred at.
     */
    public string $token;

    /**
     * @var string The name of the parser rule this warning occurred on.
     */
    public string $rule;

    /**
     * @var int The UTF-8 byte index representing the start of the span.
     *
     * Together with $spanEnd, this represents the slice of the
     * original wikitext that this token corresponds to.
     */
    public int $spanStart;

    /**
     * @var int The UTF-8 byte index representing the stop of the span.
     *
     * Together with $spanEnd, this represents the slice of the
     * original wikitext that this token corresponds to.
     */
    public int $spanEnd;

    /**
     * @var string The kind of warning this is.
     */
    public string $kind;
}
