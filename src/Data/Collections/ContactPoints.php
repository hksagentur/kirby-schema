<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\Data\Concerns\InteractsWithValues;
use Hks\Schema\Data\ContactPoint;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PhoneNumber;
use Hks\Schema\Toolkit\Assert;

/**
 * @extends DataCollection<int, ContactPoint>
 */
readonly class ContactPoints extends DataCollection implements Schemable
{
    use InteractsWithValues;

    /** @var array<int, ContactPoint> */
    protected array $contactPoints;

    /** @param array<int, ContactPoint> $contactPoints */
    public function __construct(array $contactPoints = [])
    {
        Assert::containsOnly($contactPoints, ContactPoint::class);

        $this->contactPoints = $this->ensureIndexed($contactPoints);
    }

    public static function fromArray(array $value): static
    {
        return new static(array_map(
            ContactPoint::fromArray(...),
            $value
        ));
    }

    public function findByType(string $type): ?ContactPoint
    {
        return $this->findBy(fn (ContactPoint $contactPoint): bool => $contactPoint->hasType($type));
    }

    public function telephone(?string $type = null): ?PhoneNumber
    {
        if ($type !== null) {
            return $this->findByType($type)?->telephone();
        }

        return $this->findBy(fn (ContactPoint $contactPoint): bool => $contactPoint->telephone() !== null)?->telephone();
    }

    public function email(?string $type = null): ?EmailAddress
    {
        if ($type !== null) {
            return $this->findByType($type)?->email();
        }

        return $this->findBy(fn (ContactPoint $contactPoint): bool => $contactPoint->email() !== null)?->email();
    }

    public function all(): array
    {
        return $this->contactPoints;
    }

    public function toArray(): array
    {
        return $this->map(fn (ContactPoint $contactPoint): array => $contactPoint->toArray());
    }

    public function toStructuredData(): array
    {
        return $this->map(fn (ContactPoint $contactPoint): array => $contactPoint->toStructuredData());
    }
}
