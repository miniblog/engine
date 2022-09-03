<?php

declare(strict_types=1);

namespace Miniblog\Engine\Tests;

use DanBettles\Marigold\AbstractTestCase;
use Error;
use Miniblog\Engine\FrontController;
use Miniblog\Engine\MarkdownParser;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionMethod;
use RuntimeException;
use Throwable;

use function file_get_contents;

use const DIRECTORY_SEPARATOR;
use const true;

class FrontControllerTest extends AbstractTestCase
{
    public function testIsInstantiable(): void
    {
        $config = [];
        $markdownParser = new MarkdownParser();
        $controller = new FrontController($config, $markdownParser);

        $this->assertSame($config, $controller->getConfig());
        $this->assertSame($markdownParser, $controller->getMarkdownParser());
    }

    public function testHasProtectedActionMethods(): void
    {
        $this->assertTrue((new ReflectionMethod(FrontController::class, 'postAction'))->isProtected());
        $this->assertTrue((new ReflectionMethod(FrontController::class, 'postsAction'))->isProtected());
    }

    // Factory method.
    private function createFrontController(string $projectDir): FrontController
    {
        return new FrontController([
            'contentDir' => "{$projectDir}/content",
            'templatesDir' => "{$projectDir}/templates",
        ], new MarkdownParser());
    }

    public function testHandleCallsPostactionIfAPostHasBeenRequested(): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);
        $contentDir = "{$projectDir}/content";

        $publishedAtStr = '2022-08-29';
        $postId = $publishedAtStr;

        /** @var MockObject */
        $markdownParserMock = $this
            ->getMockBuilder(MarkdownParser::class)
            ->onlyMethods(['parse'])
            ->getMock()
        ;

        $postFilePathname = $contentDir . DIRECTORY_SEPARATOR . 'posts' . DIRECTORY_SEPARATOR . "{$postId}.md";
        $articleFileContents = file_get_contents($postFilePathname);

        $parsedMarkdown = [
            'title' => 'Title',
            'description' => 'Description',
            'body' => 'Body',
            'publishedAt' => $publishedAtStr,
            'frontMatterIncluded' => true,
        ];

        $markdownParserMock
            ->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($articleFileContents))
            ->willReturn($parsedMarkdown)
        ;

        /** @var MarkdownParser $markdownParserMock */
        $frontController = new FrontController([
            'contentDir' => $contentDir,
            'templatesDir' => "{$projectDir}/templates",
        ], $markdownParserMock);

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], [
            'post' => $postId,
        ]);

        $this->assertSame(<<<END
        Before content
        Title
        Description
        Body
        2022-08-29
        After content
        END, $response['content']);
    }

    public function testHandleReturnsA404IfThePostIdIsInvalid(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], [
            'post' => 'Invalid_Id',
        ]);

        $this->assertIsArray($response);

        $this->assertArrayHasKey('headers', $response);

        $this->assertEquals([
            'HTTP/1.1 404 Not Found',
        ], $response['headers']);

        $this->assertArrayHasKey('content', $response);

        $this->assertSame(<<<END
        Before content
        Not Found
        After content
        END, $response['content']);
    }

    public function testHandleReturnsA404IfThePostDoesNotExist(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], [
            'post' => 'non-existent',
        ]);

        $this->assertIsArray($response);

        $this->assertArrayHasKey('headers', $response);

        $this->assertEquals([
            'HTTP/1.1 404 Not Found',
        ], $response['headers']);

        $this->assertArrayHasKey('content', $response);

        $this->assertSame(<<<END
        Before content
        Not Found
        After content
        END, $response['content']);
    }

    /** @return array<int, array{0: Throwable}> */
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
    public function testHandleReturnsA500IfAnErrorOccursInPostaction(Throwable $throwable): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        /** @var MockObject */
        $frontControllerMock = $this
            ->getMockBuilder(FrontController::class)
            ->onlyMethods(['postAction'])
            ->setConstructorArgs([
                [
                    'contentDir' => "{$projectDir}/content",
                    'templatesDir' => "{$projectDir}/templates",
                ],
                new MarkdownParser(),
            ])
            ->getMock()
        ;

        $frontControllerMock
            ->method('postAction')
            ->willThrowException($throwable)
        ;

        /** @var FrontController $frontControllerMock */
        $response = $frontControllerMock->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], [
            'post' => '2022-09-03',
        ]);

        $this->assertIsArray($response);

        $this->assertArrayHasKey('headers', $response);

        $this->assertEquals([
            'HTTP/1.1 500 Internal Server Error',
        ], $response['headers']);

        $this->assertArrayHasKey('content', $response);

        $this->assertSame(<<<END
        Before content
        Internal Server Error
        After content
        END, $response['content']);
    }

    // Here it's easier to just do a full, *functional* test.
    public function testHandleCallsPostsactionIfAPostHasNotBeenRequested(): void
    {
        $frontController = $this->createFrontController($this->createFixturePathname(__FUNCTION__));

        $response = $frontController->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], []);

        $this->assertSame(<<<END
        Before content
        Article 3 Title
        Article 3 description
        <p>Article 3 body</p>
        2022-09-02
        Article 2 Title
        Article 2 description
        <p>Article 2 body</p>
        2022-09-01
        Article 1 Title
        Article 1 description
        <p>Article 1 body</p>
        2022-08-31

        After content
        END, $response['content']);
    }

    /** @dataProvider providesErrors */
    public function testHandleReturnsA500IfAnErrorOccursInPostsaction(Throwable $throwable): void
    {
        $projectDir = $this->createFixturePathname(__FUNCTION__);

        /** @var MockObject */
        $frontControllerMock = $this
            ->getMockBuilder(FrontController::class)
            ->onlyMethods(['postsAction'])
            ->setConstructorArgs([
                [
                    'contentDir' => "{$projectDir}/content",
                    'templatesDir' => "{$projectDir}/templates",
                ],
                new MarkdownParser(),
            ])
            ->getMock()
        ;

        $frontControllerMock
            ->method('postsAction')
            ->willThrowException($throwable)
        ;

        /** @var FrontController $frontControllerMock */
        $response = $frontControllerMock->handle([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
        ], []);

        $this->assertIsArray($response);

        $this->assertArrayHasKey('headers', $response);

        $this->assertEquals([
            'HTTP/1.1 500 Internal Server Error',
        ], $response['headers']);

        $this->assertArrayHasKey('content', $response);

        $this->assertSame(<<<END
        Before content
        Internal Server Error
        After content
        END, $response['content']);
    }
}
