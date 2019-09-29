<?php

namespace Spatie\Pjax\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\DomCrawler\Crawler;

class FilterIfPjax
{
    /** @var \Symfony\Component\DomCrawler\Crawler */
    protected $crawler;

    public function handle(Request $request, Closure $next): BaseResponse
    {
        $response = $next($request);

        if (!$request->pjax() || $response->isRedirection()) {
            return $response;
        }

        $this->filterResponse($response, $request->header('X-PJAX-Container'))
            ->setUriHeader($response, $request)
            ->setVersionHeader($response, $request);

        return $response;
    }

    protected function filterResponse(Response $response, $container): self
    {
        $crawler = $this->getCrawler($response);

        $response->setContent(
            $this->makeTitle($crawler).
            $this->fetchContainer($crawler, $container)
        );

        return $this;
    }

    protected function makeTitle(Crawler $crawler): ?string
    {
        $pageTitle = $crawler->filter('head > title');

        if (!$pageTitle->count()) {
            return null;
        }

        return "<title>{$pageTitle->html()}</title>";
    }

    protected function fetchContainer(Crawler $crawler, $container): string
    {
        $content = $crawler->filter($container);

        if (!$content->count()) {
            abort(422);
        }

        return $content->html();
    }

    protected function setUriHeader(Response $response, Request $request): self
    {
        $response->header('X-PJAX-URL', $request->getRequestUri());

        return $this;
    }

    protected function setVersionHeader(Response $response, Request $request): self
    {
        $crawler = $this->getCrawler($this->createResponseWithLowerCaseContent($response));
        $node = $crawler->filter('head > meta[http-equiv="x-pjax-version"]');

        if ($node->count()) {
            $response->header('x-pjax-version', $node->attr('content'));
        }

        return $this;
    }

    protected function getCrawler(Response $response): Crawler
    {
        if ($this->crawler) {
            return $this->crawler;
        }

        return $this->crawler = new Crawler($response->getContent());
    }

    protected function createResponseWithLowerCaseContent(Response $response): Response
    {
        $lowercaseContent = strtolower($response->getContent());

        return Response::create($lowercaseContent);
    }
}
