<?php

namespace Hks\Schema\Image;

use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Cms\File;
use Kirby\Filesystem\Asset;
use Kirby\Filesystem\Mime;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;
use Stringable;

class ResponsiveImage implements Stringable
{
    protected File|Asset $image;

    protected ?string $preset;

    protected array $formats;
    protected array $widths;
    protected array $attributes;

    protected int $quality;

    public function __construct(File|Asset $image)
    {
        if ($image->type() !== 'image') {
            throw new InvalidArgumentException('Unexpected file type');
        }

        $this->image = $image;

        $this->formats = $this->getPluginOptions('image.formats');
        $this->widths = $this->getPluginOptions('image.widths');
        $this->attributes = $this->getPluginOptions('image.attributes');

        $this->quality = $this->getPluginOptions('image.quality');
    }

    public static function for(File|Asset $image): static
    {
        return new static($image);
    }

    public static function from(array $options): static
    {
        return static::for($options['image'])->options($options);
    }

    public function usePreset(): bool
    {
        return $this->getPreset() !== null;
    }

    public function options(array $options): static
    {
        if (isset($options['preset'])) {
            $this->preset($options['preset']);
        }

        if (isset($options['quality'])) {
            $this->quality($options['quality']);
        }

        if (isset($options['formats'])) {
            $this->formats($options['formats']);
        }

        if (isset($options['widths'])) {
            $this->widths($options['widths']);
        }

        return $this;
    }

    public function preset(string $preset): static
    {
        $this->preset = $preset;

        return $this;
    }

    public function quality(int $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    /** @param string[] $formats */
    public function formats(array $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    /** @param (int|string)[] $widths */
    public function widths(array $widths): static
    {
        $this->widths = array_map(function (int|string $width) {
            return $width === 'auto' ? $this->image->width() : (int) $width;
        }, array_values($widths));

        asort($this->widths, SORT_NUMERIC);

        return $this;
    }

    public function attributes(array $attributes): static
    {
        if (isset($attributes['width'])) {
            $this->width($attributes['width']);
        }

        if (isset($attributes['height'])) {
            $this->height($attributes['height']);
        }

        if (isset($attributes['alt'])) {
            $this->alt($attributes['alt']);
        }

        if (isset($attributes['id'])) {
            $this->id($attributes['id']);
        }

        if (isset($attributes['classList'])) {
            $this->classList($attributes['classList']);
        }

        if (isset($attributes['dataList'])) {
            $this->dataList($attributes['dataList']);
        }

        if (isset($attributes['sizes'])) {
            $this->sizes($attributes['sizes']);
        }

        if (isset($attributes['loading'])) {
            $this->loading($attributes['loading']);
        }

        if (isset($attributes['decoding'])) {
            $this->decoding($attributes['decoding']);
        }

        if (isset($attributes['fetchpriority'])) {
            $this->fetchPriority($attributes['fetchpriority']);
        }

        return $this;
    }

    public function width(int $width): static
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    public function height(int $height): static
    {
        $this->attributes['height'] = $height;

        return $this;
    }

    public function alt(string $text): static
    {
        $this->attributes['alt'] = $text;

        return $this;
    }

    /** @param string $id */
    public function id(string $id): static
    {
        $this->attributes['id'] = $id;

        return $this;
    }

    /** @param string|string[] $classList */
    public function classList(string|array $classList): static
    {
        $this->attributes['class'] = is_array($classList) ? A::join($classList, ' ') : $classList;

        return $this;
    }

    /** @param Array<string, string> $dataList */
    public function dataList(array $dataList): static
    {
        foreach ($dataList as $key => $value) {
            $this->attributes['data-' . Str::kebab($key)] = $value;
        }

        return $this;
    }

    /** @param string|string[] $sizes */
    public function sizes(string|array $sizes): static
    {
        $this->attributes['sizes'] = is_array($sizes) ? A::join($sizes) : $sizes;

        return $this;
    }

    /** @param {'lazy'|'eager'} $strategy */
    public function loading(string $strategy): static
    {
        $this->attributes['loading'] = $strategy;

        return $this;
    }

    /** @param {'auto'|'sync'|'async'} $strategy */
    public function decoding(string $strategy): static
    {
        $this->attributes['decoding'] = $strategy;

        return $this;
    }

    /** @param {'auto'|'high'|'low'} $priority */
    public function fetchPriority(string $priority): static
    {
        $this->attributes['fetchpriority'] = $priority;

        return $this;
    }

    public function toString(): string
    {
        return $this->toHtml();
    }

    public function toHtml(array $attributes = []): string
    {
        $attributes = [
            ...$this->attributes,
            ...$attributes,
        ];

        $widths = $this->getWidths();
        $formats = $this->getFormats();

        $sourceFormats = A::slice($formats, 0, -1);

        $thumbnailWidth = A::first($widths);
        $thumbnailFormat = A::last($formats);
        $thumbnailQuality = $this->getQuality($thumbnailFormat);

        $thumbnail = match (true) {
            $this->usePreset() => $this->image->thumb($this->getPresetOptions($this->getPreset(), [
                'width' => $thumbnailWidth,
                'format' => $thumbnailFormat,
                'quality' => $thumbnailQuality,
            ])),
            default => $this->image->thumb([
                'width' => $thumbnailWidth,
                'format' => $thumbnailFormat,
                'quality' => $thumbnailQuality,
            ]),
        };

        $sizes = match (true) {
            $this->usePreset() => $this->getSrcsetOptions($this->getPreset(), [
                "{$thumbnailWidth}w" => [
                    'width' => $thumbnailWidth,
                ],
            ]),
            default => A::reduce($widths, fn (array $sizes, int $width) => $sizes + [
                "{$width}w" => [
                    'width' => $width,
                ],
            ], []),
        };

        $image = Html::img($thumbnail->url(), [
            'srcset' => $this->image->srcset(A::map($sizes, fn (array $options) => [
                'quality' => $thumbnailQuality,
                ...$options,
                'format' => $thumbnailFormat,
            ])),
            'width' => $thumbnail->width(),
            'height' => $thumbnail->height(),
            'alt' => $this->image->alt(),
            ...$attributes,
        ]);

        $sources = A::map($sourceFormats, fn (string $format) => Html::tag('source', attr: [
            'srcset' => $this->image->srcset(A::map($sizes, fn (array $options) => [
                'quality' => $this->getQuality($format),
                ...$options,
                'format' => $format,
            ])),
            'type' => Mime::fromExtension($format),
            'sizes' => $attributes['sizes'] ?? null,
        ]));

        return empty($sources) ? $image : Html::tag('picture', [...$sources, $image]);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function getPreset(): ?string
    {
        return $this->preset;
    }

    protected function getFormats(): array
    {
        return A::map($this->formats, function (string $format) {
            return $format === 'auto' ? Str::after($this->image->mime(), '/') : $format;
        });
    }

    protected function getWidths(): array
    {
        $widths = A::map($this->widths, function (int|string $width) {
            return $width === 'auto' ? $this->image->width() : (int) $width;
        });

        asort($widths, SORT_NUMERIC);

        return $widths;
    }

    protected function getQuality(?string $format = null): int
    {
        return match ($format) {
            'avif' => 0.6 * $this->quality,
            'webp' => 0.75 * $this->quality,
            default => $this->quality,
        };
    }

    protected function getPluginOptions(?string $key = null, mixed $default = null): mixed
    {
        $options = App::instance()->option('hksagentur.schema', []);

        if (is_null($key)) {
            return $options;
        }

        return A::get($options, $key, $default);
    }

    protected function getPresetOptions(?string $preset = null, ?array $default = null): ?array
    {
        $options = App::instance()->option('thumbs.presets.' . ($preset ?? $this->getPreset()));

        if (is_array($options)) {
            return $options;
        }

        $options = App::instance()->option('thumbs.presets.default');

        if (is_array($options)) {
            return $options;
        }

        return $default;
    }

    protected function getSrcsetOptions(?string $preset = null, ?array $default = null): ?array
    {
        $options = App::instance()->option('thumbs.srcsets.' . ($preset ?? $this->getPreset()));

        if (is_array($options)) {
            return $options;
        }

        $options = App::instance()->option('thumbs.srcsets.default');

        if (is_array($options)) {
            return $options;
        }

        return $default;
    }
}
