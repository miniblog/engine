<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\TemplateEngine;
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

    private function createInternalServerErrorResponse(): HttpResponse
    {
        $content = $this->render('http_500.html.php', [
            'metaTitle' => 'Internal Server Error',
        ]);

        return new HttpResponse($content, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     */
    public function handle(array $server, array $query): HttpResponse
    {
        try {
            if (array_key_exists('post', $query)) {
                return $this->postAction($server, $query, $query['post']);
            }

            return $this->postsAction($server, $query);
        } catch (Throwable $t) {
            return $this->createInternalServerErrorResponse();
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
        $templateEngine = new TemplateEngine($templatesDir);

        $variables['config'] = $this->getConfig();
        $variables['helper'] = new OutputHelper();
        $variables['contentForLayout'] = $templateEngine->render($contentTemplateBasename, $variables);

        return $templateEngine->render('layout.html.php', $variables);
    }

    private function createNotFoundResponse(): HttpResponse
    {
        $content = $this->render('http_404.html.php', [
            'metaTitle' => 'Page Not Found',
        ]);

        return new HttpResponse($content, HttpResponse::HTTP_NOT_FOUND);
    }

    private function createOkResponse(string $content): HttpResponse
    {
        return new HttpResponse($content);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     * @param string $postId
     */
    protected function postAction(array $server, array $query, string $postId): HttpResponse
    {
        $article = $this->getPostRepo()->find($postId);

        if (null === $article) {
            return $this->createNotFoundResponse();
        }

        $content = $this->render('post_action.html.php', [
            'metaTitle' => $article->getTitle(),
            'metaDescription' => ($article->getDescription() ?: ''),
            'article' => $article,
        ]);

        return $this->createOkResponse($content);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     */
    protected function postsAction(array $server, array $query): HttpResponse
    {
        $content = $this->render('posts_action.html.php', [
            'metaTitle' => 'All Posts',
            'articles' => $this->getPostRepo()->findAll(),
        ]);

        return $this->createOkResponse($content);
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
