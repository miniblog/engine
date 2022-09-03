<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\TemplateProcessor;
use Throwable;

use function array_key_exists;

use const DIRECTORY_SEPARATOR;
use const null;

class FrontController
{
    /** @var array<string, mixed> */
    private array $config;

    private MarkdownParser $markdownParser;

    private ArticleRepository $postRepo;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        array $config,
        MarkdownParser $markdownParser
    ) {
        $this
            ->setConfig($config)
            ->setMarkdownParser($markdownParser)
        ;
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     * @return array{headers: string[], content: string}
     */
    public function handle(array $server, array $query): array
    {
        try {
            if (array_key_exists('post', $query)) {
                return $this->postAction($server, $query, $query['post']);
            }

            return $this->postsAction($server, $query);
        } catch (Throwable $t) {
            return $this->createInternalServerErrorResponse($server);
        }
    }

    /**
     * @param string $contentTemplateBasename
     * @param array<string, mixed> $variables
     */
    private function render(
        string $contentTemplateBasename,
        array $variables = []
    ): string {
        /** @var string */
        $templatesDir = $this->getConfig()['templatesDir'];
        $templateProcessor = new TemplateProcessor($templatesDir);

        $variables['config'] = $this->getConfig();
        $variables['helper'] = new OutputHelper();
        $variables['contentForLayout'] = $templateProcessor->render($contentTemplateBasename, $variables);

        return $templateProcessor->render('layout.html.php', $variables);
    }

    /**
     * @param array<string, string> $server
     * @return array{headers: string[], content: string}
     */
    private function createNotFoundResponse(array $server): array
    {
        return [
            'headers' => [
                "{$server['SERVER_PROTOCOL']} 404 Not Found",
            ],
            'content' => $this->render('http_404.html.php', [
                'metaTitle' => 'Page Not Found',
            ]),
        ];
    }

    /**
     * @param array<string, string> $server
     * @return array{headers: string[], content: string}
     */
    private function createInternalServerErrorResponse(array $server): array
    {
        return [
            'headers' => [
                "{$server['SERVER_PROTOCOL']} 500 Internal Server Error",
            ],
            'content' => $this->render('http_500.html.php', [
                'metaTitle' => 'Internal Server Error',
            ]),
        ];
    }

    /**
     * @param array<string, string> $server
     * @param string $content
     * @return array{headers: string[], content: string}
     */
    private function createOkResponse(array $server, string $content): array
    {
        return [
            'headers' => [
                "{$server['SERVER_PROTOCOL']} 200 OK",
            ],
            'content' => $content,
        ];
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     * @param string $postId
     * @return array{headers: string[], content: string}
     */
    protected function postAction(array $server, array $query, string $postId): array
    {
        $article = $this->getPostRepo()->find($postId);

        if (null === $article) {
            return $this->createNotFoundResponse($server);
        }

        $content = $this->render('post_action.html.php', [
            'metaTitle' => $article->getTitle(),
            'metaDescription' => ($article->getDescription() ?: ''),
            'article' => $article,
        ]);

        return $this->createOkResponse($server, $content);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     * @return array{headers: string[], content: string}
     */
    protected function postsAction(array $server, array $query): array
    {
        $content = $this->render('posts_action.html.php', [
            'metaTitle' => 'All Posts',
            'articles' => $this->getPostRepo()->findAll(),
        ]);

        return $this->createOkResponse($server, $content);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    private function setMarkdownParser(MarkdownParser $parser): self
    {
        $this->markdownParser = $parser;
        return $this;
    }

    public function getMarkdownParser(): MarkdownParser
    {
        return $this->markdownParser;
    }

    private function getPostRepo(): ArticleRepository
    {
        if (!isset($this->postRepo)) {
            $this->postRepo = new ArticleRepository(
                ($this->getConfig()['contentDir'] . DIRECTORY_SEPARATOR . 'posts'),
                $this->getMarkdownParser()
            );
        }

        return $this->postRepo;
    }
}
