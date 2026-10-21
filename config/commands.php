<?php

use Kirby\CLI\CLI;

return [
    'schema:migrate' => [
        'description' => 'Runs the content migrations registered in `hksagentur.schema.migrations`',
        'args' => [
            'dry-run' => [
                'description' => 'List the changes without updating content',
                'noValue' => true,
            ],
        ],
        'command' => static function (CLI $cli): void {
            $dryRun = $cli->arg('dry-run');
            $updated = 0;

            $log = static fn (string $message, bool $error) => $error ? $cli->error($message) : $cli->out($message);

            foreach ($cli->kirby()->option('hksagentur.schema.migrations', []) as $migration) {
                $updated += (new $migration())->run($log, $dryRun);
            }

            $cli->success(($dryRun ? 'Would update' : 'Updated') . " {$updated} content file(s)");
        },
    ],
];
