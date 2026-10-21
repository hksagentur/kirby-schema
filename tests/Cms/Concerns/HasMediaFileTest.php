<?php

namespace Hks\Schema\Cms\Concerns;

use Hks\Schema\Concerns\InteractsWithTmp;
use Hks\Schema\TestCase;
use Kirby\Cms\App;
use Kirby\Cms\Block;

class HasMediaFileTest extends TestCase
{
    use InteractsWithTmp;

    public const TMP = __DIR__ . '/tmp/HasMediaFileTest';

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new App([
            'roots' => [
                'index' => static::TMP,
            ],
            'site' => [
                'children' => [
                    [
                        'slug' => 'home',
                        'files' => [
                            ['filename' => 'cover.jpg'],
                            ['filename' => 'intro.mp4'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testHasImageResolvesASingleImage(): void
    {
        $block = $this->block(HasImageBlock::class, ['image' => ['cover.jpg']]);

        $this->assertTrue($block->hasImage());
        $this->assertSame('cover.jpg', $block->image()->filename());
    }

    public function testHasImageIsFalseWithoutAnImage(): void
    {
        $block = $this->block(HasImageBlock::class, []);

        $this->assertFalse($block->hasImage());
        $this->assertNull($block->image());
    }

    public function testHasMediaFileResolvesTheFileOfTheSelectedMediaType(): void
    {
        $content = ['image' => ['cover.jpg'], 'video' => ['intro.mp4']];

        $image = $this->block(HasMediaFileBlock::class, ['mediaType' => 'image', ...$content]);
        $video = $this->block(HasMediaFileBlock::class, ['mediaType' => 'video', ...$content]);
        $none = $this->block(HasMediaFileBlock::class, $content);

        $this->assertSame('cover.jpg', $image->mediaFile()->filename());
        $this->assertSame('intro.mp4', $video->mediaFile()->filename());
        $this->assertNull($none->mediaFile());
    }

    /**
     * @template T of Block
     * @param class-string<T> $class
     * @return T
     */
    protected function block(string $class, array $content): Block
    {
        return new $class([
            'type' => 'test',
            'content' => $content,
            'parent' => $this->app->page('home'),
        ]);
    }
}

class HasImageBlock extends Block
{
    use HasImage;
}

class HasMediaFileBlock extends Block
{
    use HasMediaFile;
}
