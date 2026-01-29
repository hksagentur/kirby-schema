<?php

namespace Hks\Schema\Data\Types;

use Kirby\Cms\Html;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Str;

readonly class FileUrl extends Url
{
    public function name(): string
    {
		return pathinfo($this->path ?? '', PATHINFO_FILENAME);
    }

    public function filename(): string
    {
		return pathinfo($this->path ?? '', PATHINFO_BASENAME);
    }

    public function extension(): string
    {
        return Str::lower(pathinfo($this->path ?? '', PATHINFO_EXTENSION));
    }

    public function type(): ?string
    {
        return F::extensionToType($this->extension());
    }

    public function mime(): ?string
    {
        return F::extensionToMime($this->extension());
    }

    public function toLink(array $attributes = []): string
    {
        return Html::link($this->value(), $this->filename(), [
            'type' => $this->mime(),
            'download' => $this->filename(),
            ...$attributes,
        ]);
    }
}
