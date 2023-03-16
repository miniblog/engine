<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Schema\Thing;
use Miniblog\Engine\Tests\Schema\ThingTest\ThingGrandchild;

use const false;
use const true;

class ThingTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $thing = new Thing();

        $this->assertNull($thing->getIdentifier());
        $this->assertNull($thing->getName());
        $this->assertNull($thing->getDescription());
        $this->assertNull($thing->getUrl());
    }

    public function testHasAccessorsForAllProperties(): void
    {
        $thing = new Thing();

        $foo = $thing->setIdentifier('lorem-ipsum');
        $this->assertSame('lorem-ipsum', $thing->getIdentifier());
        $this->assertSame($thing, $foo);

        $bar = $thing->setName('Lorem');
        $this->assertSame('Lorem', $thing->getName());
        $this->assertSame($thing, $bar);

        $baz = $thing->setDescription('Lorem ipsum dolor.');
        $this->assertSame('Lorem ipsum dolor.', $thing->getDescription());
        $this->assertSame($thing, $baz);

        $qux = $thing->setUrl('https://example.com');
        $this->assertSame('https://example.com', $thing->getUrl());
        $this->assertSame($thing, $qux);
    }

    public function testDoesNotHaveABodyProperty(): void
    {
        $thing = new Thing();

        $this->assertNull($thing->getBody());

        $thing->setBody('Lorem ipsum dolor.');

        $this->assertNull($thing->getBody());
    }

    /** @return array<mixed[]> */
    public function providesThingsCreatedFromArrays(): array
    {
        return [
            [
                // Some:
                (new Thing())
                    ->setIdentifier('123')
                    ->setName('Title'),
                Thing::class,
                [
                    'identifier' => '123',
                    'name' => 'Title',
                ],
            ],
            [
                // All:
                (new Thing())
                    ->setIdentifier('123')
                    ->setName('Title')
                    ->setDescription('Description')
                    ->setUrl('https://example.com'),
                Thing::class,
                [
                    'identifier' => '123',
                    'name' => 'Title',
                    'description' => 'Description',
                    'url' => 'https://example.com',
                ],
            ],
            [  // #2
                // Junk is ignored:
                (new Thing())
                    ->setIdentifier('123')
                    ->setName('Title')
                    ->setDescription('Description'),
                Thing::class,
                [
                    'identifier' => '123',
                    'name' => 'Title',
                    'description' => 'Description',
                    'foo' => 'bar',
                    'baz' => 'qux',
                ],
            ],
            [
                // Properties inherited from ancestor classes are included:
                (new ThingGrandchild())
                    ->setIdentifier('123')
                    ->setName('Title')
                    ->setDescription('Description')
                    ->setFoo('1')
                    ->setBar('2')
                    ->setBaz('3')
                    ->setQux('4'),
                ThingGrandchild::class,
                [
                    'identifier' => '123',
                    'name' => 'Title',
                    'description' => 'Description',
                    'foo' => '1',
                    'bar' => '2',
                    'baz' => '3',
                    'qux' => '4',
                ],
            ],
        ];
    }

    /**
     * @dataProvider providesThingsCreatedFromArrays
     * @param array<string,mixed> $propertyValues
     */
    public function testCreatefromarrayCreatesANewInstance(
        Thing $expectedThing,
        string $thingClassName,
        array $propertyValues
    ): void {
        $this->assertEquals(
            $expectedThing,
            $thingClassName::createFromArray($propertyValues)
        );
    }

    /** @return array<mixed[]> */
    public function providesValidThings(): array
    {
        return [
            [
                false,
                new Thing(),
            ],
            [
                false,
                (new Thing())
                    // Missing identifier.
                    ->setName('Title'),
            ],
            [  // #2
                false,
                (new Thing())
                    ->setIdentifier('foo'),
                    // Missing name.
            ],
            [
                true,
                (new Thing())
                    ->setIdentifier('foo')
                    ->setName('Title'),
            ],
        ];
    }

    /** @dataProvider providesValidThings */
    public function testIsvalidReturnsTrueIfTheThingIsValid(bool $expected, Thing $thing): void
    {
        $this->assertSame($expected, $thing->isValid());
    }
}
