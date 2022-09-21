<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine;
use DanBettles\Marigold\TemplateFileLoader;
use Throwable;

use function array_merge;
use function call_user_func_array;

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
     * @param array<string, string> $serverVars
     */
    public function handle(array $serverVars): HttpResponse
    {
        try {
            $matchedRoute = (new Router([
                [
                    'path' => '/',
                    'action' => 'homepageAction',
                ],
                [
                    'path' => '/blog/{blogPostId}',
                    'action' => 'postAction',
                ],
            ]))->match($serverVars);

            if (null === $matchedRoute) {
                return $this->createNotFoundResponse();
            }

            /** @var callable */
            $action = [$this, $matchedRoute['action']];

            /** @var HttpResponse */
            return call_user_func_array($action, array_merge([
                $serverVars,
            ], $matchedRoute['parameters']));
        } catch (Throwable $t) {
            return $this->createInternalServerErrorResponse();
        }
    }

    /**
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
     * @param array<string, string> $serverVars
     */
    protected function homepageAction(array $serverVars): HttpResponse
    {
        /** @var array<string, string> */
        $site = $this->getConfig()['site'];

        return $this->render('homepage_action.html.php', [
            'serverVars' => $serverVars,
            'metaDescription' => $site['description'],
            'articles' => $this->getBlogPostRepo()->findAll(),
        ]);
    }

    /**
     * @param array<string, string> $serverVars
     */
    protected function postAction(
        array $serverVars,
        string $blogPostId
    ): HttpResponse {
        $article = $this->getBlogPostRepo()->find($blogPostId);

        if (null === $article) {
            return $this->createNotFoundResponse();
        }

        return $this->render('post_action.html.php', [
            'serverVars' => $serverVars,
            'metaTitle' => $article->getTitle(),
            'metaDescription' => ($article->getDescription() ?: ''),
            'article' => $article,
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

    private function getBlogPostRepo(): ArticleRepository
    {
        return $this->getArticleManager()->getRepository('BlogPost');
    }
}
