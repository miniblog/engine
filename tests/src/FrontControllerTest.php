<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use DanBettles\Marigold\HttpResponse;
use Error;
use Miniblog\Engine\Article;
use Miniblog\Engine\ArticleManager;
use Miniblog\Engine\ArticleRepository;
use Miniblog\Engine\FrontController;
use Miniblog\Engine\MarkdownParser;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use RuntimeException;
use Throwable;

class FrontControllerTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $config = [];

        $articleManager = new ArticleManager(
            new MarkdownParser(),
            $this->createFixturePathname(__FUNCTION__)
        );

        $controller = new FrontController($config, $articleManager);

        $this->assertSame($config, $controller->getConfig());
        $this->assertSame($articleManager, $controller->getArticleManager());
    }

    public function testHasProtectedActionMethods(): void
    {
        $this->assertTrue((new ReflectionMethod(FrontController::class, 'postAction'))->isProtected());
        $this->assertTrue((new ReflectionMethod(FrontController::class, 'homepageAction'))->isProtected());
    }

    // Factory method.
    private function createFrontController(string $projectDir): FrontController
    {
        $articleManager = new ArticleManager(new MarkdownParser(), "{$projectDir}/content");

        return new FrontController([
            'site' => [
                'description' => '',
            ],
            'projectDir' => $projectDir,
            'engineDir' => $projectDir,
        ], $articleManager);
    }

    public function testHandleWillRespondWithASingleArticleIfASpecificBlogPostIsRequested(): void
    {
        $publishedAtStr = '2022-08-29';
        $blogPostId = $publishedAtStr;

        $article = (new Article())
            ->setTitle('Title')
            ->setDescription('Description')
            ->setBody('Body')
            ->setPublishedAt($publishedAtStr)
        ;

        /** @var MockObject */
        $blogPostRepoMock = $this
            ->getMockBuilder(ArticleRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['find'])
            ->getMock()
        ;

        $blogPostRepoMock
            ->expects($this->once())
            ->method('find')
            ->with($blogPostId)
            ->willReturn($article)
        ;

        /** @var MockObject */
        $articleManagerMock = $this
            ->getMockBuilder(ArticleManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRepository'])
            ->getMock()
        ;

        $articleManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with('BlogPost')
            ->willReturn($blogPostRepoMock)
        ;

        $projectDir = $this->createFixturePathname(__FUNCTION__);

        /** @var ArticleManager $articleManagerMock */
        $frontController = new FrontController([
            'projectDir' => $projectDir,
            'engineDir' => $projectDir,
        ], $articleManagerMock);

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_URI' => "/blog/{$blogPostId}?foo=bar",
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Title
        Description
        Body
        2022-08-29
        After content
        END, 200);

        $this->assertEquals($expected, $response);
    }

    public function testHandleReturnsA404IfTheBlogPostIdIsInvalid(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_URI' => "/blog/Invalid_Id?foo=bar",
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Not Found
        After content
        END, 404);

        $this->assertEquals($expected, $response);
    }

    public function testHandleReturnsA404IfTheBlogPostDoesNotExist(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_URI' => "/blog/non-existent?foo=bar",
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Not Found
        After content
        END, 404);

        $this->assertEquals($expected, $response);
    }

    /** @return array<int, array<int, mixed>> */
    public function providesErrors(): array
    {
        return [
            [
                new RuntimeException('Exception'),
            ],
            [
                new Error('PHP error'),
            ],
        ];
    }

    /** @dataProvider providesErrors */
    public function testHandleWillRespondWithA500IfAnErrorOccursInPostaction(Throwable $throwable): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        /** @var MockObject */
        $frontControllerMock = $this
            ->getMockBuilder(FrontController::class)
            ->setConstructorArgs([
                [
                    'projectDir' => $projectDir,
                    'engineDir' => $projectDir,
                ],
                $this->createStub(ArticleManager::class),
            ])
            ->onlyMethods(['postAction'])
            ->getMock()
        ;

        $frontControllerMock
            ->method('postAction')
            ->willThrowException($throwable)
        ;

        /** @var FrontController $frontControllerMock */
        $response = $frontControllerMock->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_URI' => "/blog/2022-09-03?foo=bar",
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Internal Server Error
        After content
        END, 500);

        $this->assertEquals($expected, $response);
    }

    /** @return array<int, array<int, mixed>> */
    public function providesRequestsForBlogPostListingPage(): array
    {
        return [
            [
                [
                    'SERVER_PROTOCOL' => 'HTTP/1.1',
                    'REQUEST_URI' => '/?foo=bar',
                ],
            ],
        ];
    }

    /**
     * Here it's easier to just do a full, functional test.
     *
     * @param array<string, string> $serverVars
     * @dataProvider providesRequestsForBlogPostListingPage
     */
    public function testHandleWillRespondWithAListOfArticlesIfASpecificBlogPostIsNotRequested(array $serverVars): void
    {
        $response = $this
            ->createFrontController($this->createFixturePathname(__FUNCTION__))
            ->handle($serverVars)
        ;

        $expected = new HttpResponse(<<<END
        Before content
        Maximum Article
        Maximum Article description
        <p>Maximum Article body.</p>
        2022-09-14
        Minimum Article

        <p>Minimum Article body.</p>
        2022-09-03

        After content
        END, 200);

        $this->assertEquals($expected, $response);
    }

    /** @dataProvider providesErrors */
    public function testHandleWillRespondWithA500IfAnErrorOccursInHomepageaction(Throwable $throwable): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        /** @var MockObject */
        $frontControllerMock = $this
            ->getMockBuilder(FrontController::class)
            ->setConstructorArgs([
                [
                    'projectDir' => $projectDir,
                    'engineDir' => $projectDir,
                ],
                $this->createStub(ArticleManager::class),
            ])
            ->onlyMethods(['homepageAction'])
            ->getMock()
        ;

        $frontControllerMock
            ->method('homepageAction')
            ->willThrowException($throwable)
        ;

        /** @var FrontController $frontControllerMock */
        $response = $frontControllerMock->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Internal Server Error
        After content
        END, 500);

        $this->assertEquals($expected, $response);
    }

    public function testHandleWillRespondWithA404IfTheRouteIsNotMatched(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_URI' => "/non-existent-resource",
        ]);

        $expected = new HttpResponse(<<<END
        Before content
        Not Found
        After content
        END, 404);

        $this->assertEquals($expected, $response);
    }
}
