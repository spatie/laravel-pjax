<?php

namespace Spatie\Pjax\Test;

use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Spatie\Pjax\Middleware\FilterIfPjax;
use Symfony\Component\HttpFoundation\Response;

class FilterIfPjaxTest extends TestCase
{
    public function setUp(): void
    {
        $this->middleware = new FilterIfPjax();
    }

    /** @test */
    public function it_will_not_modify_a_non_pjax_request()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext());

        $this->assertFalse($this->isPjaxResponse($response));

        $this->assertEquals($this->getHtml(), $response->getContent());
    }

    /** @test */
    public function it_will_return_the_title_and_contents_of_the_container_for_pjax_request()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->getNext());

        $this->assertTrue($this->isPjaxResponse($response));

        $this->assertEquals('<title>Pjax title</title>Content', $response->getContent());
    }

    /** @test */
    public function it_will_not_return_the_title_if_it_is_not_set()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->getNext('pageWithoutTitle'));

        $this->assertTrue($this->isPjaxResponse($response));

        $this->assertEquals('Content', $response->getContent());
    }

    /** @test */
    public function it_will_set_the_request_uri_for_a_pjax_request()
    {
        $request = $this->addPjaxHeaders(Request::create('/test'));

        $response = $this->middleware->handle($request, $this->getNext());

        $this->assertEquals('/test', $response->headers->get('X-PJAX-URL'));
    }

    /** @test */
    public function it_will_set_the_request_version_header_for_a_pjax_request()
    {
        $request = $this->addPjaxHeaders(Request::create('/test'));

        $response = $this->middleware->handle($request, $this->getNext());

        $this->assertEquals('1.0.0', $response->headers->get('X-PJAX-Version'));
    }

    /** @test */
    public function it_wil_not_set_a_version_header_when_it_is_not_requested()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->getNext('pageWithoutVersionMetaTag'));

        $this->assertEquals(null, $response->headers->get('X-PJAX-Version'));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return bool
     */
    protected function isPjaxResponse(Response $response)
    {
        return $response->headers->has('X-PJAX-URL');
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Request
     */
    protected function addPjaxHeaders(Request $request)
    {
        $request->headers->set('X-PJAX', true);
        $request->headers->set('X-PJAX-Container', '#pjax-container');

        return $request;
    }

    /**
     * @param string $pageName
     *
     * @return \Closure
     */
    protected function getNext($pageName = 'pageWithTitle')
    {
        $html = $this->getHtml($pageName);

        $response = (new \Illuminate\Http\Response($html));

        return function ($request) use ($response) {
            return $response;
        };
    }

    /**
     * @param string $pageName
     *
     * @return string
     */
    protected function getHtml($pageName = 'pageWithTitle')
    {
        return file_get_contents(__DIR__."/fixtures/{$pageName}.html");
    }
}
