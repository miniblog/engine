<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing\CreativeWork;

use DanBettles\Marigold\AbstractTestCase;
use DateTime;
use Miniblog\Engine\Schema\Thing\CreativeWork;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;

use const false;
use const true;

class WebSiteTest extends AbstractTestCase
{
    public function testIsACreativework(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(CreativeWork::class));
    }

    /** @return array<mixed[]> */
    public function providesValidThings(): array
    {
        return [
            [
                false,
                new WebSite(),
            ],
            [
                false,
                (new WebSite())
                    // Missing identifier.
                    ->setHeadline('Title of the Website')
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
            [  // #2
                false,
                (new WebSite())
                    ->setIdentifier('this')
                    // Missing headline.
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
            [
                false,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setHeadline('Title of the Website')
                    // Missing description.
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
            [
                false,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setHeadline('Title of the Website')
                    ->setDescription('Meta description')
                    // Missing date published.
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
            [  // #5
                false,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setHeadline('Title of the Website')
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    // Missing language.
                    ->setUrl('https://example.com'),
            ],
            [
                false,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setHeadline('Title of the Website')
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb'),
                    // Missing URL.
            ],
            [
                true,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setHeadline('Title of the Website')
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
            [
                true,
                (new WebSite())
                    ->setIdentifier('this')
                    ->setName('Title of the Website')  // `name` => `headline`
                    ->setDescription('Meta description')
                    ->setDatePublished(new DateTime())
                    ->setInLanguage('en-gb')
                    ->setUrl('https://example.com'),
            ],
        ];
    }

    /** @dataProvider providesValidThings */
    public function testIsvalidReturnsTrueIfTheThingIsValid(bool $expected, WebSite $thing): void
    {
        $this->assertSame($expected, $thing->isValid());
    }
}
