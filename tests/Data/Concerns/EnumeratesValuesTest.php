<?php

namespace Hks\Schema\Data\Concerns;

use Hks\Schema\Data\Collections\DataCollection;
use Hks\Schema\TestCase;
use stdClass;

class EnumeratesValuesTest extends TestCase
{
    protected function collection(): DataCollection
    {
        $items = array_map(fn (int $number) => (object) ['number' => $number], [1, 2, 3, 4]);

        return new readonly class ($items) extends DataCollection {
            public function __construct(protected array $items)
            {
            }

            public function all(): array
            {
                return $this->items;
            }

            public function find(callable $callback): mixed
            {
                return $this->findBy($callback);
            }

            public function filter(callable $callback): static
            {
                return $this->filterBy($callback);
            }
        };
    }

    public function testFindByReturnsTheFirstMatch(): void
    {
        $this->assertSame(2, $this->collection()->find(fn (stdClass $item) => $item->number % 2 === 0)?->number);
        $this->assertNull($this->collection()->find(fn (stdClass $item) => $item->number > 4));
    }

    public function testFilterByReturnsACollectionOfTheMatches(): void
    {
        $filtered = $this->collection()->filter(fn (stdClass $item) => $item->number % 2 === 0);

        $this->assertInstanceOf(DataCollection::class, $filtered);
        $this->assertSame([2, 4], array_values($filtered->map(fn (stdClass $item) => $item->number)));
    }
}
