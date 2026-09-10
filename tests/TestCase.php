<?php

namespace Hks\Schema;

use Kirby\Cms\App;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected App $app;

    protected function setUp(): void
    {
        $this->app = new App([
            'roots' => [
                'index' => '/dev/null',
            ],
        ]);

        $this->forwardCallToTraits('setUp');
    }

    protected function tearDown(): void
    {
        $this->forwardCallToTraits('tearDown');
    }

    protected function forwardCallToTraits(string $hook): void
    {
        foreach (class_uses($this) as $trait) {
            $name = $hook . substr(strrchr($trait, '\\'), 1);

            if (method_exists($this, $name)) {
                $this->$name();
            }
        }
    }
}
