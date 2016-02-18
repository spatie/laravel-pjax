<?php

namespace Spatie\Pjax\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class FilterIfPjax
{
    /**
     * The DomCrawler instance.
     *
     * @var \Symfony\Component\DomCrawler\Crawler
     */
    protected $crawler;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
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

    /**
     * @param \Illuminate\Http\Response $response
     * @param string                    $container
     *
     * @return $this
     */
    protected function filterResponse(Response $response, $container)
    {
        $crawler = $this->getCrawler($response);

        $response->setContent(
            $this->makeTitle($crawler).
            $this->fetchContainer($crawler, $container)
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     *
     * @return null|string
     */
    protected function makeTitle(Crawler $crawler)
    {
        $pageTitle = $crawler->filter('head > title');

        if (!$pageTitle->count()) {
            return;
        }

        return "<title>{$pageTitle->html()}</title>";
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     * @param string                                $container
     *
     * @return string
     */
    protected function fetchContainer(Crawler $crawler, $container)
    {
        $content = $crawler->filter($container);

        if (!$content->count()) {
            abort(422);
        }

        return $content->html();
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    protected function setUriHeader(Response $response, Request $request)
    {
        $response->header('X-PJAX-URL', $request->getRequestUri());

        return $this;
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    protected function setVersionHeader(Response $response, Request $request)
    {
        $crawler = $this->getCrawler($this->createReponseWithLowerCaseContent($response));
        $node = $crawler->filter('head > meta[http-equiv="x-pjax-version"]');

        if ($node->count()) {
            $response->header('x-pjax-version', $node->attr('content'));
        }

        return $this;
    }

    /**
     * Get the DomCrawler instance.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Symfony\Component\DomCrawler\Crawler
     */
    protected function getCrawler(Response $response)
    {
        if ($this->crawler) {
            return $this->crawler;
        }

        return $this->crawler = new Crawler($response->getContent());
    }

    /**
     * Make the content of the given response lowercase.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Http\Response
     */
    protected function createReponseWithLowerCaseContent(Response $response)
    {
        $lowercaseContent = strtolower($response->getContent());

        return Response::create($lowercaseContent);
    }
}
