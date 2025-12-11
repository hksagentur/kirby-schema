<?php

namespace Hks\Schema\Transformer;

use Hks\Schema\Data\Term;

/**
 * @extends Transformer<Term>
 */
class TermSchemaTransformer extends Transformer
{
    public function transform(object $term): array
    {
        return array_filter([
            '@type' => 'DefinedTerm',
            'name' => $term->name()->toString(),
            'termCode' => $term->slug()->toString(),
            'description' => $term->description()?->toString(),
        ]);
    }
}
