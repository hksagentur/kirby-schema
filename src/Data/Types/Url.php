<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use InvalidArgumentException;
use Kirby\Http\Idn;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\V;
use SensitiveParameter;

readonly class Url extends StringType implements Arrayable
{
    public string $scheme;
    public string $host;
    public ?int $port;
    public ?string $path;
    public ?string $query;
    public ?string $fragment;

    #[SensitiveParameter]
    public ?string $user;
    #[SensitiveParameter]
    public ?string $password;

    public function __construct(
        public string $value,
    ) {
        if (! V::url($value)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid URL.',
                $value,
            ));
        }

        $parts = parse_url($value);

        $this->scheme = $parts['scheme'];
        $this->host = $parts['host'];
        $this->port = $parts['port'] ?? null;
        $this->path = $parts['path'] ?? null;
        $this->query = $parts['query'] ?? null;
        $this->fragment = $parts['fragment'] ?? null;
        $this->user = $parts['user'] ?? null;
        $this->password = $parts['pass'] ?? null;
    }

    public function hasScheme(): bool
    {
        return $this->scheme !== null;
    }

    public function hasHost(): bool
    {
        return $this->host !== null;
    }

    public function hasPort(): bool
    {
        return $this->port !== null;
    }

    public function hasPath(): bool
    {
        return $this->path !== null;
    }

    public function hasQuery(): bool
    {
        return $this->query !== null;
    }

    public function hasFragment(): bool
    {
        return $this->fragment !== null;
    }

    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    public function hasPassword(): bool
    {
        return $this->password !== null;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function scheme(): string
    {
        return $this->scheme;
    }

    public function host(): string
    {
        return $this->host;
    }

    public function port(): ?int
    {
        return $this->port;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(): ?string
    {
        return $this->query;
    }

    public function fragment(): ?string
    {
        return $this->fragment;
    }

    public function user(): ?string
    {
        return $this->user;
    }

    public function password(): ?string
    {
        return $this->password;
    }

    public function with(array $parts): static
    {
        return new static($this->buildUrl([$this->toArray(), ...$parts]));
    }

    public function withPath(string $path): static
    {
        return $this->with(['path' => $path]);
    }

    public function withFragment(string $fragment): static
    {
        return $this->with(['fragment' => $fragment]);
    }

    public function withQuery(string|array $data): static
    {
        return $this->with(['query' => $data]);
    }

    public function without(string|array $keys): static
    {
        return new static($this->buildUrl(A::without($this->toArray(), $keys)));
    }

    public function withoutPath(): static
    {
        return $this->without('path');
    }

    public function withoutQuery(): static
    {
        return $this->without('query');
    }

    public function withoutFragment(): static
    {
        return $this->without('fragment');
    }

    public function toPunycode(): static
    {
        return $this->with(['host' => Idn::encode($this->host)]);
    }

    public function toUnicode(): static
    {
        return $this->with(['host' => Idn::decode($this->host)]);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->path,
            'query' => $this->query,
            'fragment' => $this->fragment,
            'user' => $this->user,
            'pass' => $this->password,
        ];
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->toLink($attributes);
    }

    public function toLink(array $attributes = []): string
    {
        return Html::link($this->value(), attr: $attributes);
    }

    protected function buildUrl(array $parts): string
    {
        return implode('', array_filter([
            isset($parts['scheme']) ? "{$parts['scheme']}://" : null,
            $parts['user'] ?? null,
            isset($parts['pass']) ? ":{$parts['pass']}" : null,
            isset($parts['user']) ? '@' : null,
            $parts['host'] ?? null,
            isset($parts['port']) ? ":{$parts['port']}" : null,
            $parts['path'] ?? null,
            isset($parts['query']) ? "?{$this->buildQuery($parts['query'])}" : null,
            isset($parts['fragment']) ? "#{$parts['fragment']}" : null,
        ]));
    }

    protected function buildQuery(string|array|object $data): string
    {
        if (is_string($data)) {
            return Str::ltrim($data, '?');
        }

        return http_build_query($data, '', '&', PHP_QUERY_RFC3986);
    }
}
