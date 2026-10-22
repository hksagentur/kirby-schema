<?php

namespace Hks\Schema\Migration;

use Closure;
use Kirby\Cms\App;
use Kirby\Cms\Languages;
use Kirby\Cms\ModelWithContent;

/**
 * A content migration; running it a second time must not change anything.
 */
abstract class Migration
{
    public function appliesTo(ModelWithContent $model): bool
    {
        return true;
    }

    abstract public function migrate(Changeset $changeset): Changeset;

    /**
     * @param Closure(string $message, bool $error): void $log
     */
    public function run(Closure $log, bool $dryRun = false): int
    {
        App::instance()->impersonate('kirby');

        $updated = 0;

        foreach (Languages::ensure() as $language) {
            $lang = $language->code();

            foreach (App::instance()->models() as $model) {
                $version = $model->version('latest');

                if (! $version->exists($lang) || ! $this->appliesTo($model)) {
                    continue;
                }

                $changeset = $this->migrate(Changeset::of($model->content($lang)));

                if ($changeset->isEmpty()) {
                    continue;
                }

                $id = $model->id() ?? $model::CLASS_ALIAS;

                if ($model->version('changes')->exists($lang)) {
                    $log("{$id} ({$lang}) has unsaved changes, save or discard them first", error: true);
                    continue;
                }

                $log("{$id} ({$lang}): " . implode(', ', $changeset->dirty()), error: false);

                if (! $dryRun) {
                    $version->save(
                        fields: $changeset->applyTo($version->read($lang)),
                        language: $lang,
                        overwrite: true
                    );
                }

                $updated++;
            }
        }

        return $updated;
    }
}
