<?php

declare(strict_types=1);

namespace Miniblog\Engine;

use DanBettles\Marigold\HttpResponse;
use DanBettles\Marigold\Registry;
use DanBettles\Marigold\Router;
use DanBettles\Marigold\TemplateEngine\Engine;
use DanBettles\Marigold\TemplateEngine\TemplateFileLoader;
use Throwable;

use function array_merge;
use function call_user_func_array;

use const null;

class FrontController
{
    private string $env;

    /**
     * @var array<string, mixed>
     */
    private array $config;

    private ArticleManager $articleManager;

    private Router $router;

    private Engine $templateEngine;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        string $env,
        array $config,
        ArticleManager $articleManager
    ) {
        $this
            ->setEnv($env)
            ->setConfig($config)
            ->setArticleManager($articleManager)
        ;

        $this->setRouter(new Router([
            [
                'path' => '/',
                'action' => 'homepageAction',
            ],
            [
                'path' => '/blog/{blogPostId}',
                'action' => 'postAction',
            ],
        ]));

        $this->setTemplateEngine($this->createTemplateEngine());
    }

    private function createTemplateEngine(): Engine
    {
        $templateFileLoader = new TemplateFileLoader([
            'Overrides' => $this->getConfig()['projectDir'] . '/templates',
            'Default templates' => $this->getConfig()['engineDir'] . '/templates',
        ]);

        $globals = (new Registry())
            ->add('config', $this->getConfig())
            ->add('router', $this->getRouter())
            ->addFactory('helper', function () {
                return new OutputHelper();
            })
        ;

        return Engine::create($templateFileLoader, $globals);
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function render(
        string $contentTemplateBasename,
        array $variables = [],
        int $statusCode = HttpResponse::HTTP_OK
    ): HttpResponse {
        $content = $this
            ->getTemplateEngine()
            ->render($contentTemplateBasename, $variables)
        ;

        return new HttpResponse($content, $statusCode);
    }

    private function createNotFoundResponse(): HttpResponse
    {
        return $this->render('_errors/error_404.html.php', [], HttpResponse::HTTP_NOT_FOUND);
    }

    /**
     * @param array<string, string> $serverVars
     */
    public function handle(array $serverVars): HttpResponse
    {
        try {
            $matchedRoute = $this->getRouter()->match($serverVars);

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
            if ('dev' === $this->getEnv()) {
                throw $t;
            }

            return $this->render('_errors/error.html.php', [], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param array<string, string> $serverVars
     */
    protected function homepageAction(array $serverVars): HttpResponse
    {
        return $this->render('homepage_action.html.php', [
            'serverVars' => $serverVars,  // @todo Add to globals.
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
            'serverVars' => $serverVars,  // @todo Add to globals.
            'article' => $article,
        ]);
    }

    private function setEnv(string $env): self
    {
        $this->env = $env;
        return $this;
    }

    public function getEnv(): string
    {
        return $this->env;
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

    private function setRouter(Router $router): self
    {
        $this->router = $router;
        return $this;
    }

    private function getRouter(): Router
    {
        return $this->router;
    }

    private function setTemplateEngine(Engine $templateEngine): self
    {
        $this->templateEngine = $templateEngine;
        return $this;
    }

    private function getTemplateEngine(): Engine
    {
        return $this->templateEngine;
    }
}
