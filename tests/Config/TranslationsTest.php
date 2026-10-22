<?php

namespace Hks\Schema\Config;

use Hks\Schema\TestCase;
use Kirby\Filesystem\F;

class TranslationsTest extends TestCase
{
    public function testEveryLanguageHasTheSameKeys(): void
    {
        $translations = require dirname(__DIR__, 2) . '/config/translations.php';

        $this->assertSame([], array_keys(array_diff_key($translations['de'], $translations['en'])));
        $this->assertSame([], array_keys(array_diff_key($translations['en'], $translations['de'])));
    }

    public function testBlueprintsReferenceExistingKeys(): void
    {
        $translations = require dirname(__DIR__, 2) . '/config/translations.php';

        foreach (glob(dirname(__DIR__, 2) . '/config/blueprints/*/*.yml') as $file) {
            preg_match_all('#\*: (hksagentur\.schema\.[\w.]+)#', F::read($file), $matches);

            foreach ($matches[1] as $key) {
                $this->assertArrayHasKey($key, $translations['en'], sprintf('%s references %s', F::filename($file), $key));
            }
        }
    }
}
