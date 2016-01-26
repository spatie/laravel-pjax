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
     * Whether the required filters have been applied.
     *
     * @var bool
     */
    private $filtered = false;

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

        $this->filter($response, $request, function($response, $request) {
            $this->requiredFilter($response, $request);
        });

        if (! $this->isFiltered()) {
            $this->requiredFilter($response, $request);
        }

        return $response;
    }
    
    /**
     * Easily add extra filters for PJAX requests.
     *
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request  $request
     */
    protected function filter(Response $response, Request $request, Closure $filter)
    {
        $filter($response, $request);
    }

    /**
     * Run the required filters.
     *
     * @return $this
     */
    private function requiredFilter(Response $response, Request $request)
    {
        $this->filterResponse($response, $request->header('X-PJAX-Container'))
            ->setUriHeader($response, $request)
            ->setVersionHeader($response, $request);

        $this->filtered = true;
    }
    
    /**
     * Checks if the default PJAX filters have been applied.
     *
     * @return bool
     */
    private function isFiltered()
    {
        return $this->filtered;
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param string                    $container
     *
     * @return $this
     */
    private function filterResponse(Response $response, $container)
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
    private function makeTitle(Crawler $crawler)
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
    private function fetchContainer(Crawler $crawler, $container)
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
     */
    private function setUriHeader(Response $response, Request $request)
    {
        $response->header('X-PJAX-URL', $request->getRequestUri());

        return $this;
    }

    /**
     * @param \Illuminate\Http\Response $response
     * @param \Illuminate\Http\Request  $request
     */
    private function setVersionHeader(Response $response, Request $request)
    {
        $crawler = $this->getCrawler($response);
        $node = $crawler->filter('head > meta[http-equiv]');

        if ($node->count()) {
            $response->header('X-PJAX-Version', $node->attr('content'));
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
}
