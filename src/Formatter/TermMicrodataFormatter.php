<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\Term;

/**
 * @extends MicrodataFormatter<Term>
 */
class TermMicrodataFormatter extends MicrodataFormatter
{
    public function getSchemaType(): string
    {
        return 'https://schema.org/DefinedTerm';
    }

    public function getAttributes(mixed $term): array
    {
        return [
            'name' => fn () => $term->name()->toHtml(['itemprop' => 'name']),
            'slug' => fn () => $term->slug()->toHtml(['itemprop' => 'termCode']),
            'description' => fn () => $term->description()?->toHtml(['itemprop' => 'description']),
        ];
    }
}
