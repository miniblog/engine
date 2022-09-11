<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\TemplateEngine;
use DanBettles\Marigold\TemplateFileLoader;
use Throwable;

use function array_key_exists;

use const null;

class FrontController
{
    /**
     * @var array<string, mixed>
     */
    private array $config;

    private ArticleManager $articleManager;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        array $config,
        ArticleManager $articleManager
    ) {
        $this
            ->setConfig($config)
            ->setArticleManager($articleManager)
        ;
    }

    private function createInternalServerErrorResponse(): HttpResponse
    {
        $content = $this->renderView('http_500.html.php', [
            'metaTitle' => 'Internal Server Error',
        ]);

        return new HttpResponse($content, HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
    }

    private function createNotFoundResponse(): HttpResponse
    {
        $content = $this->renderView('http_404.html.php', [
            'metaTitle' => 'Page Not Found',
        ]);

        return new HttpResponse($content, HttpResponse::HTTP_NOT_FOUND);
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
    private function renderView(
        string $contentTemplateBasename,
        array $variables = []
    ): string {
        $templateFileLoader = new TemplateFileLoader([
            'Overrides' => $this->getConfig()['projectDir'] . '/templates',
            'Default templates' => $this->getConfig()['engineDir'] . '/templates',
        ]);

        $templateEngine = new TemplateEngine($templateFileLoader);

        $variables['config'] = $this->getConfig();
        $variables['helper'] = new OutputHelper();

        return $templateEngine->render($contentTemplateBasename, $variables);
    }

    /**
     * @param string $contentTemplateBasename
     * @param array<string, mixed> $variables
     */
    private function render(
        string $contentTemplateBasename,
        array $variables = []
    ): HttpResponse {
        $content = $this->renderView($contentTemplateBasename, $variables);

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

        return $this->render('post_action.html.php', [
            'metaTitle' => $article->getTitle(),
            'metaDescription' => ($article->getDescription() ?: ''),
            'article' => $article,
        ]);
    }

    /**
     * @param array<string, string> $server
     * @param array<string, string> $query
     */
    protected function postsAction(array $server, array $query): HttpResponse
    {
        return $this->render('posts_action.html.php', [
            'metaTitle' => 'All Posts',
            'articles' => $this->getPostRepo()->findAll(),
        ]);
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

    private function setArticleManager(ArticleManager $manager): self
    {
        $this->articleManager = $manager;
        return $this;
    }

    public function getArticleManager(): ArticleManager
    {
        return $this->articleManager;
    }

    private function getPostRepo(): ArticleRepository
    {
        return $this->getArticleManager()->getRepository('post');
    }
}
