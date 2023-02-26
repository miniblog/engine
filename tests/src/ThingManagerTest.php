<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Miniblog\Engine\ThingManager;
use Miniblog\Engine\DocumentParser;
use Miniblog\Engine\Schema\Thing;
use Miniblog\Engine\Schema\Thing\CreativeWork;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article;
use Miniblog\Engine\Schema\Thing\CreativeWork\WebSite;
use Miniblog\Engine\Schema\Thing\Person;
use Parsedown;
use RangeException;
use ReflectionClass;
use RuntimeException;

use const null;

class ThingManagerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        ThingManager::$thisWebSite = null;
        ThingManager::$owner = null;
    }

    // Factory method.
    private function createThingManager(string $dataDir): ThingManager
    {
        $baseThingClass = new ReflectionClass(Thing::class);

        return new ThingManager(
            $baseThingClass->getNamespaceName(),
            $dataDir,
            new DocumentParser(new Parsedown())
        );
    }

    public function testIsInstantiable(): void
    {
        $namespace = 'Foo';
        $dataDir = $this->createFixturePathname(__FUNCTION__);
        $documentParser = $this->createStub(DocumentParser::class);

        $manager = new ThingManager(
            $namespace,
            $dataDir,
            $documentParser
        );

        $this->assertSame($namespace, $manager->getNamespace());
        $this->assertSame($dataDir, $manager->getDataDir());
        $this->assertSame($documentParser, $manager->getDocumentParser());
    }

    public function testConstructorThrowsAnExceptionIfTheDataDirectoryDoesNotExist(): void
    {
        $dataDir = $this->createFixturePathname('non_existent_dir');

        $this->expectException(RangeException::class);
        $this->expectExceptionMessage("The directory `{$dataDir}` does not exist.");

        new ThingManager(
            'Foo',
            $dataDir,
            $this->createStub(DocumentParser::class)
        );
    }

    /** @return array<mixed[]> */
    public function providesThingsLoadedUsingFind(): array
    {
        return [
            [
                (new Thing())
                    ->setIdentifier('minimum-thing')
                    ->setName('Minimum Thing')
                    ->setDescription(null),
                Thing::class,
                'minimum-thing',
            ],
            [
                (new Thing())
                    ->setIdentifier('maximum-thing')
                    ->setName('Maximum Thing')
                    ->setDescription('Description of Maximum Thing.'),
                Thing::class,
                'maximum-thing',
            ],
            [  // #2
                (new Article())
                    ->setIdentifier('minimum-article')
                    ->setHeadline('Minimum Article')
                    ->setDescription('Lorem.')
                    ->setArticleBody('<p>Lorem ipsum dolor.</p>')
                    ->setDatePublished('2022-09-03')
                    ->setDateModified(null),
                Article::class,
                'minimum-article',
            ],
            [
                (new Article())
                    ->setIdentifier('maximum-article')
                    ->setHeadline('Maximum Article')
                    ->setDescription('Description')
                    ->setArticleBody('<p>Lorem ipsum dolor.</p>')
                    ->setDatePublished('2022-09-14')
                    ->setDateModified('2023-02-12'),
                Article::class,
                'maximum-article',
            ],
            [
                null,
                Article::class,
                'missing-title',
            ],
            [  // #5
                null,
                Article::class,
                'missing-published-date',
            ],
            [
                null,
                Article::class,
                'empty-article',
            ],
            [
                null,
                Article::class,
                'Invalid_Id',
            ],
        ];
    }

    /**
     * @dataProvider providesThingsLoadedUsingFind
     * @phpstan-param class-string $type
     */
    public function testFindLoadsASingleThingById(
        ?Thing $expectedThing,
        string $type,
        string $id
    ): void {
        $thing = $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->find($type, $id)
        ;

        $this->assertEquals($expectedThing, $thing);
    }

    public function testFindReturnsNullIfTheDocumentDoesNotExist(): void
    {
        $thing = $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->find(CreativeWork::class, 'non_existent')
        ;

        $this->assertNull($thing);
    }

    public function testFindReturnsNullIfTheIdIsInvalid(): void
    {
        $dataDir = $this->createFixturePathname(__FUNCTION__);
        $invalidId = 'Invalid_Id';

        // The file *does* exist...
        $this->assertFileExists("{$dataDir}/Thing/{$invalidId}.md");

        $thing = $this
            ->createThingManager($dataDir)
            ->find(Thing::class, $invalidId)
        ;

        // ...But it won't be returned because its ID is invalid.
        $this->assertNull($thing);
    }

    public function testFindallReturnsAnArrayOfAllValidThingsOfTheSpecifiedType(): void
    {
        $things = $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->findAll(Article::class)
        ;

        $this->assertEquals([
            (new Article())
                ->setIdentifier('minimum-article')
                ->setHeadline('Minimum Article')
                ->setDescription('Lorem.')
                ->setArticleBody('<p>Lorem ipsum dolor.</p>')
                ->setDatePublished('2022-09-03'),
            (new Article())
                ->setIdentifier('maximum-article')
                ->setHeadline('Maximum Article')
                ->setDescription('Description')
                ->setArticleBody('<p>Lorem ipsum dolor.</p>')
                ->setDatePublished('2022-09-14')
                ->setDateModified('2023-02-12'),
        ], $things);
    }

    public function testGetthiswebsiteReturnsAThingThatDescribesTheCurrentWebsite(): void
    {
        $thisWebsite = $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->getThisWebsite()
        ;

        $expectedThing = (new WebSite())
            ->setIdentifier('this')
            ->setHeadline('Name of the Website')
            ->setDescription("A concise description of the website's content")
            ->setDatePublished('2023-02-26')
            ->setInLanguage('en-gb')
        ;

        $this->assertEquals($expectedThing, $thisWebsite);
    }

    public function testGetthiswebsiteThrowsAnExceptionIfTheDocumentDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There is something wrong with the Document that describes this website.');

        $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->getThisWebsite()
        ;
    }

    public function testGetthiswebsiteAlwaysReturnsTheSameInstance(): void
    {
        $thingManager = $this->createThingManager($this->createFixturePathname(__FUNCTION__));
        $thisWebsite = $thingManager->getThisWebsite();

        $this->assertSame($thisWebsite, $thingManager->getThisWebsite());
    }

    public function testGetownerReturnsAThingThatDescribesTheOwnerOfTheCurrentWebsite(): void
    {
        $owner = $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->getOwner()
        ;

        $expectedThing = (new Person())
            ->setIdentifier('owner')
            ->setGivenName('John')
            ->setFamilyName('Dory')
            ->setEmail('johndory@example.com')
        ;

        $this->assertEquals($expectedThing, $owner);
    }

    public function testGetownerThrowsAnExceptionIfTheDocumentDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There is something wrong with the Document that describes the owner of this website.');

        $this
            ->createThingManager($this->createFixturePathname(__FUNCTION__))
            ->getOwner()
        ;
    }

    public function testGetownerAlwaysReturnsTheSameInstance(): void
    {
        $thingManager = $this->createThingManager($this->createFixturePathname(__FUNCTION__));
        $owner = $thingManager->getOwner();

        $this->assertSame($owner, $thingManager->getOwner());
    }
}
