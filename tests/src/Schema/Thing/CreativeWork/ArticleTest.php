<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests\Schema\Thing\CreativeWork;

use DanBettles\Marigold\AbstractTestCase;
use DateTime;
use Miniblog\Engine\Schema\Thing\CreativeWork\Article;
use Miniblog\Engine\Schema\Thing\CreativeWork;

use const false;
use const true;

class ArticleTest extends AbstractTestCase
{
    public function testIsACreativework(): void
    {
        $this->assertTrue($this->getTestedClass()->isSubclassOf(CreativeWork::class));
    }

    public function testHasAccessorsForAllProperties(): void
    {
        $article = new Article();

        $foo = $article->setArticleBody('Lorem ipsum dolor.');
        $this->assertSame($article, $foo);
        $this->assertSame('Lorem ipsum dolor.', $foo->getArticleBody());
    }

    public function testArticleBodySupersedesText(): void
    {
        $fromText = (new Article())
            ->setText('Foo')
        ;

        $this->assertSame('Foo', $fromText->getText());
        $this->assertSame($fromText->getText(), $fromText->getArticleBody());

        $fromArticleBody = (new Article())
            ->setArticleBody('Bar')
        ;

        $this->assertSame('Bar', $fromArticleBody->getArticleBody());
        $this->assertSame($fromArticleBody->getArticleBody(), $fromArticleBody->getText());
    }

    public function testHasABodyProperty(): void
    {
        $fromBody = new Article();

        $something = $fromBody->setBody('Lorem ipsum dolor.');

        $this->assertSame('Lorem ipsum dolor.', $fromBody->getBody());
        $this->assertSame($fromBody->getBody(), $fromBody->getText());
        $this->assertSame($fromBody->getText(), $fromBody->getArticleBody());
        $this->assertSame($fromBody, $something);

        $fromArticleBody = (new Article())
            ->setArticleBody('Foo')
        ;

        $this->assertSame('Foo', $fromArticleBody->getBody());

        $fromText = (new Article())
            ->setText('Bar')
        ;

        $this->assertSame('Bar', $fromText->getBody());
    }

    /** @return array<mixed[]> */
    public function providesValidThings(): array
    {
        return [
            [
                false,
                new Article(),
            ],
            [
                false,
                (new Article())
                    // Missing identifier.
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setArticleBody('Body of the Article.'),
            ],
            [  // #2
                false,
                (new Article())
                    ->setIdentifier('foo')
                    // Missing headline.
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setArticleBody('Body of the Article.'),
            ],
            [
                false,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    // Missing description.
                    ->setDatePublished(new DateTime())
                    ->setArticleBody('Body of the Article.'),
            ],
            [
                false,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    // Missing published date.
                    ->setArticleBody('Body of the Article.'),
            ],
            [  // #5
                false,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime()),
                    // Missing article body.
            ],
            [
                true,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setArticleBody('Body of the Article.'),
            ],
            [
                true,
                (new Article())
                    ->setIdentifier('foo')
                    ->setName('Title of the Article')  // `name` => `headline`
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setArticleBody('Body of the Article.'),
            ],
            [  // #8
                true,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setText('Body of the Article.'),  // `text` => `articleBody`
            ],
            [
                true,
                (new Article())
                    ->setIdentifier('foo')
                    ->setHeadline('Title of the Article')
                    ->setDescription('A concise description of the Article.')
                    ->setDatePublished(new DateTime())
                    ->setBody('Body of the Article.'),  // 'body' => `articleBody`
            ],
        ];
    }

    /** @dataProvider providesValidThings */
    public function testIsvalidReturnsTrueIfTheThingIsValid(bool $expected, Article $thing): void
    {
        $this->assertSame($expected, $thing->isValid());
    }
}
