<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\Schema\Thing\Person;
use Miniblog\Engine\Schema\Thing;

use const false;
use const null;
use const true;

class PersonTest extends AbstractTestCase
{
    public function testIsAThing(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(Thing::class));
    }

    public function testIsInstantiable(): void
    {
        $person = new Person();

        $this->assertNull($person->getIdentifier());
        $this->assertNull($person->getName());
        $this->assertNull($person->getDescription());
        $this->assertNull($person->getGivenName());
        $this->assertNull($person->getFamilyName());
        $this->assertNull($person->getEmail());
    }

    public function testHasAccessorsForAllProperties(): void
    {
        $person = new Person();

        $foo = $person->setGivenName('Daniel');
        $this->assertSame('Daniel', $person->getGivenName());
        $this->assertSame($person, $foo);

        $bar = $person->setFamilyName('Bettles');
        $this->assertSame('Bettles', $person->getFamilyName());
        $this->assertSame($person, $bar);

        $baz = $person->setEmail('daniel@justathought.dev');
        $this->assertSame('daniel@justathought.dev', $person->getEmail());
        $this->assertSame($person, $baz);
    }

    /** @return array<mixed[]> */
    public function providesPersonsWithNames(): array
    {
        return [
            [
                null,
                new Person(),
            ],
            [
                'Full Name',
                (new Person())
                    ->setName('Full Name'),
            ],
            [
                'Full',
                (new Person())
                    ->setGivenName('Full'),
            ],
            [
                'Name',
                (new Person())
                    ->setFamilyName('Name'),
            ],
            [
                'Full Name',
                (new Person())
                    ->setGivenName('Full')
                    ->setFamilyName('Name'),
            ],
            [
                'Full Name',
                (new Person())
                    ->setGivenName('Full')
                    ->setFamilyName('Name')
                    ->setName('Something Else'),
            ],
        ];
    }

    /** @dataProvider providesPersonsWithNames */
    public function testGetfullnameReturnsTheFullNameOfThePerson(
        ?string $expectedFullName,
        Person $person
    ): void {
        $this->assertSame($expectedFullName, $person->getFullName());
    }

    /** @return array<mixed[]> */
    public function providesValidThings(): array
    {
        return [
            [
                false,
                (new Person()),
            ],
            [
                false,
                (new Person())
                    // Missing identifier.
                    ->setName('Jane Doe'),
            ],
            [  // #2
                false,
                (new Person())
                    // Missing identifier.
                    ->setGivenName('Jane'),
            ],
            [
                false,
                (new Person())
                    // Missing identifier.
                    ->setFamilyName('Doe'),
            ],
            [
                false,
                (new Person())
                    // Missing identifier.
                    ->setGivenName('Jane')
                    ->setFamilyName('Doe'),
            ],
            [  // #5
                false,
                (new Person())
                    ->setIdentifier('jane-doe'),
                    // Missing name.
            ],
            [
                true,
                (new Person())
                    ->setIdentifier('jane-doe')
                    ->setName('Jane Doe'),
            ],
            [
                true,
                (new Person())
                    ->setIdentifier('jane-doe')
                    ->setGivenName('Jane'),
            ],
            [  // #8
                true,
                (new Person())
                    ->setIdentifier('jane-doe')
                    ->setFamilyName('Doe'),
            ],
            [
                true,
                (new Person())
                    ->setIdentifier('jane-doe')
                    ->setGivenName('Jane')
                    ->setFamilyName('Doe'),
            ],
        ];
    }

    /** @dataProvider providesValidThings */
    public function testIsvalidReturnsTrueIfTheThingIsValid(bool $expected, Person $thing): void
    {
        $this->assertSame($expected, $thing->isValid());
    }
}
