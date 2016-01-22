<?php

namespace Spatie\Pjax\Test;

use Illuminate\Http\Request;
use Spatie\Pjax\Middleware\FilterIfPjax;
use Symfony\Component\HttpFoundation\Response;

class FilterIfPjaxTest extends \PHPUnit_Framework_TestCase
{
    protected $next = [];
    
    public function setUp()
    {
        $this->middleware = new FilterIfPjax();

        $this->fullPageHtml = file_get_contents(__DIR__.'/fixtures/page.html');

        $response = (new \Illuminate\Http\Response($this->fullPageHtml));

        $this->next['page'] = function ($request) use ($response) { return $response; };

        $this->noTitleHtml = file_get_contents(__DIR__.'/fixtures/noTitle.html');

        $response = (new \Illuminate\Http\Response($this->noTitleHtml));

        $this->next['noTitle'] = function ($request) use ($response) { return $response; };
    }

    /**
     * @test
     */
    public function it_will_not_modify_a_non_pjax_request()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->getNext('page'));

        $this->assertFalse($this->isPjaxReponse($response));

        $this->assertEquals($this->fullPageHtml, $response->getContent());
    }

    /**
     * @test
     */
    public function it_will_return_the_title_and_contents_of_the_container_for_pjax_request()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->getNext('page'));

        $this->assertTrue($this->isPjaxReponse($response));

        $this->assertEquals('<title>Pjax title</title>Content', $response->getContent());
    }

    /**
     * @test
     */
    public function it_will_not_return_the_title_if_it_is_not_set()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->getNext('noTitle'));

        $this->assertTrue($this->isPjaxReponse($response));

        $this->assertEquals('Content', $response->getContent());
    }

    /**
     * @test
     */
    public function it_will_set_the_request_uri_for_a_pjax_request()
    {
        $request = $this->addPjaxHeaders(Request::create('/test'));

        $response = $this->middleware->handle($request, $this->getNext('page'));

        $this->assertEquals('/test', $response->headers->get('X-PJAX-URL'));
    }

    /**
     * @test
     */
    public function it_will_set_the_request_version_header_for_a_pjax_request()
    {
        $request = $this->addPjaxHeaders(Request::create('/test'));

        $response = $this->middleware->handle($request, $this->getNext('page'));

        $this->assertEquals('1.0.0', $response->headers->get('X-PJAX-Version'));
    }

    protected function isPjaxReponse(Response $response)
    {
        return $response->headers->has('X-PJAX-URL');
    }

    protected function addPjaxHeaders(Request $request)
    {
        $request->headers->set('X-PJAX', true);
        $request->headers->set('X-PJAX-Container', '#pjax-container');

        return $request;
    }
    
    protected function getNext($fixture)
    {
        return $this->next[$fixture];
    }
}
