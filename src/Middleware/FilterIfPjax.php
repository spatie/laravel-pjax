<?php

namespace Spatie\Pjax\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\DomCrawler\Crawler;

class FilterIfPjax
{
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

        if (! $request->pjax() || $response->isRedirection()) {
            return $response;
        }

        $this->filterResponse($response, $request->header('X-PJAX-CONTAINER'))
            ->setUriHeader($response, $request);

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
        $crawler = new Crawler($response->getContent());

        $response->setContent(
            $this->makeTitle($crawler).
            $this->fetchContainer($crawler, $container)
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     *
     * @return string
     */
    protected function makeTitle(Crawler $crawler)
    {
        $pageTitle = $crawler->filter('head > title')->html();

        return "<title>{$pageTitle}</title>";
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

        if (! $content->count()) {
            abort(422);
        }

        return $content->html();
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request  $request
     */
    protected function setUriHeader(Response $response, Request $request)
    {
        $response->header('X-PJAX-URL', $request->getRequestUri());
    }
}
