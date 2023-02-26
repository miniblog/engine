<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing;

use DanBettles\Marigold\AbstractTestCase;
use DateTime;
use Miniblog\Engine\Schema\Thing\CreativeWork;
use Miniblog\Engine\Schema\Thing;

use const false;
use const true;

class CreativeWorkTest extends AbstractTestCase
{
    public function testIsAThing(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(Thing::class));
    }

    public function testIsInstantiable(): void
    {
        $creativeWork = new CreativeWork();

        $this->assertNull($creativeWork->getIdentifier());
        $this->assertNull($creativeWork->getName());
        $this->assertNull($creativeWork->getDescription());
        $this->assertNull($creativeWork->getText());
        $this->assertNull($creativeWork->getDatePublished());
        $this->assertNull($creativeWork->getDateModified());
        $this->assertNull($creativeWork->getInLanguage());
        $this->assertNull($creativeWork->getHeadline());
    }

    public function testHasAccessorsForAllProperties(): void
    {
        $creativeWork = new CreativeWork();

        $qux = $creativeWork->setText('Lorem ipsum dolor sit amet.');
        $this->assertSame('Lorem ipsum dolor sit amet.', $creativeWork->getText());
        $this->assertSame($creativeWork, $qux);

        // From `DateTime`.
        $quux = $creativeWork->setDatePublished(new DateTime('1969-07-16'));
        $this->assertEquals('1969-07-16T00:00:00+00:00', $creativeWork->getDatePublished());
        $this->assertSame($creativeWork, $quux);
        // From date/time string.
        $creativeWork->setDatePublished('1987-10-15');
        $this->assertEquals('1987-10-15T00:00:00+00:00', $creativeWork->getDatePublished());
        // To `DateTime`.
        $this->assertEquals(new DateTime('1987-10-15'), $creativeWork->getDatePublished(true));

        // From `DateTime`.
        $corge = $creativeWork->setDateModified(new DateTime('1969-07-16'));
        $this->assertEquals('1969-07-16T00:00:00+00:00', $creativeWork->getDateModified());
        $this->assertSame($creativeWork, $corge);
        // From date/time string.
        $creativeWork->setDateModified('1987-10-15');
        $this->assertEquals('1987-10-15T00:00:00+00:00', $creativeWork->getDateModified());
        // To `DateTime`.
        $this->assertEquals(new DateTime('1987-10-15'), $creativeWork->getDateModified(true));

        $grault = $creativeWork->setInLanguage('en-gb');
        $this->assertSame('en-gb', $creativeWork->getInLanguage());
        $this->assertSame($creativeWork, $grault);

        $corge = $creativeWork->setHeadline('Title of the Work');
        $this->assertSame('Title of the Work', $creativeWork->getHeadline());
        $this->assertSame($creativeWork, $corge);
    }

    public function testHasABodyProperty(): void
    {
        $creativeWork = new CreativeWork();
        $something = $creativeWork->setBody('Lorem ipsum dolor.');

        $this->assertSame('Lorem ipsum dolor.', $creativeWork->getBody());
        $this->assertSame($creativeWork->getBody(), $creativeWork->getText());

        $this->assertSame($creativeWork, $something);
    }

    public function testHeadlineSupersedesName(): void
    {
        $fromHeadline = (new CreativeWork())
            ->setHeadline('Title of the Work')
        ;

        $this->assertSame('Title of the Work', $fromHeadline->getHeadline());
        $this->assertSame($fromHeadline->getHeadline(), $fromHeadline->getName());

        $fromName = (new CreativeWork())
            ->setName('Title of the Work')
        ;

        $this->assertSame('Title of the Work', $fromName->getName());
        $this->assertSame($fromName->getName(), $fromName->getHeadline());
    }

    /** @return array<mixed[]> */
    public function providesValidThings(): array
    {
        return [
            [
                false,
                new CreativeWork(),
            ],
            [
                false,
                // Useless: not even a name.
                (new CreativeWork())
                    ->setIdentifier('foo')
                    ->setDescription('Description.'),
            ],
            [  // #2
                false,
                // Useless: every Thing must have an identifier.
                (new CreativeWork())
                    ->setName('Title')  // `name` => `headline`
                    ->setDescription('Description.'),
            ],
            [
                true,
                (new CreativeWork())
                    ->setIdentifier('foo')
                    ->setHeadline('Title'),
            ],
            [
                true,
                (new CreativeWork())
                    ->setIdentifier('foo')
                    ->setName('Title'),  // `name` => `headline`
            ],
        ];
    }

    /** @dataProvider providesValidThings */
    public function testIsvalidReturnsTrueIfTheThingIsValid(bool $expected, CreativeWork $thing): void
    {
        $this->assertSame($expected, $thing->isValid());
    }
}
