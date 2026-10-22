<?php

namespace Hks\Schema\Data\Collections;

use Hks\Schema\TestCase;
use Hks\Schema\Data\ContactPoint;
use Hks\Schema\Data\Exceptions\TypeMismatchException;

class ContactPointsTest extends TestCase
{
    protected function contactPoints(): ContactPoints
    {
        return ContactPoints::fromArray([
            ['contactType' => 'Customer service', 'telephone' => '+49 30 123456'],
            ['contactType' => 'Sales', 'email' => 'sales@acme.example'],
            ['contactType' => 'Press', 'telephone' => '+49 30 654321', 'email' => 'press@acme.example'],
        ]);
    }

    public function testFindByTypeIgnoresCase(): void
    {
        $this->assertSame('Sales', $this->contactPoints()->findByType('sales')?->type());
        $this->assertNull($this->contactPoints()->findByType('Support'));
    }

    public function testTelephoneOfAContactType(): void
    {
        $this->assertSame('+49 30 654321', $this->contactPoints()->telephone('press')?->value);
        $this->assertNull($this->contactPoints()->telephone('Sales'));
        $this->assertNull($this->contactPoints()->telephone('Support'));
    }

    public function testTelephoneWithoutAContactTypeIsTheFirstOne(): void
    {
        $this->assertSame('+49 30 123456', $this->contactPoints()->telephone()?->value);
    }

    public function testEmailOfAContactType(): void
    {
        $this->assertSame('press@acme.example', $this->contactPoints()->email('Press')?->value);
        $this->assertNull($this->contactPoints()->email('Customer service'));
    }

    public function testEmailWithoutAContactTypeIsTheFirstOne(): void
    {
        $this->assertSame('sales@acme.example', $this->contactPoints()->email()?->value);
        $this->assertNull((new ContactPoints())->email());
    }

    public function testRejectsOtherValues(): void
    {
        $this->expectException(TypeMismatchException::class);

        new ContactPoints(['Sales']);
    }

    public function testHasTypeIgnoresCase(): void
    {
        $contactPoint = new ContactPoint(type: 'Customer service');

        $this->assertTrue($contactPoint->hasType('customer SERVICE'));
        $this->assertFalse($contactPoint->hasType('Sales'));
    }
}
