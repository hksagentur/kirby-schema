<?php

use Kirby\Cms\Html;

return [

    /**
     * Convert a link structure to HTML markup.
     */
    'toStructuredLink' => function (string|array|null $text = null, array $attr = []): string {
        if (is_array($text)) {
            $attr = $text;
            $text = null;
        }

        $text ??= $attr['title'] ?? $this->title()->kirbytags();
        $href ??= $attr['href'] ?? $this->url()->toUrl();

        if (! $href) {
            return $text;
        }

        $icon = $this->icon()->toIcon();

        $attr += [
            'rel' => $this->rel()->value() ?: null,
            'target' => $this->target()->toBool() ? '_blank' : null,
        ];

        $page = $this->kirby()->site()->page();

        if ($href === $page?->url()) {
            $attr += [
                'aria-current' => 'page',
            ];
        }

        return Html::a($href, array_filter([$text, $icon]), $attr);
    },

];
