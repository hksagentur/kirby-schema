<?php

use Kirby\Cms\Page;
use Kirby\Toolkit\A;

return [
    'page.changeStatus:after' => function (Page $newPage, Page $oldPage) {
        $isPublished = $newPage->isPublished() && $oldPage->isDraft();

        if (! $isPublished) {
            return;
        }

        $update = A::get(
            array: $newPage->blueprint()->field('published') ?? [],
            key: 'update',
            default: false,
        );

        if (! $update) {
            return;
        }

        $newPage->update([
            'published' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    },
    'page.create:after' => function (Page $page) {
        $isPublished = $page->isPublished();

        if (! $isPublished) {
            return;
        }

        $update = A::get(
            array: $page->blueprint()->field('published') ?? [],
            key: 'update',
            default: false,
        );

        if (! $update) {
            return;
        }

        $page->update([
            'published' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    },
    'page.update:after' => function (Page $newPage, Page $oldPage) {
        $update = A::get(
            array: $newPage->blueprint()->field('updated') ?? [],
            key: 'update',
            default: false,
        );

        if (! $update) {
            return;
        }

        $newPage->update([
            'updated' => (new DateTime())->format('Y-m-d H:i:s'),
        ]);
    },
];
