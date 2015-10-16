<?php

namespace Spatie\Pjax\Test;

use Illuminate\Http\Request;
use Spatie\Pjax\Middleware\FilterIfPjax;
use Symfony\Component\HttpFoundation\Response;

class FilterIfPjaxTest extends \PHPUnit_Framework_TestCase
{
    protected $response;

    public function setUp()
    {
        $this->middleware = new FilterIfPjax();

        $this->fullPageHtml = file_get_contents(__DIR__.'/fixtures/page.html');

        $this->response = (new \Illuminate\Http\Response($this->fullPageHtml));

        $this->next = function ($request) { return $this->response; };
    }

    /**
     * @test
     */
    public function it_will_not_modify_a_non_pjax_request()
    {
        $request = new Request();

        $response = $this->middleware->handle($request, $this->next);

        $this->assertFalse($this->isPjaxReponse($response));

        $this->assertEquals($this->fullPageHtml, $response->getContent());
    }

    /**
     * @test
     */
    public function it_will_return_the_title_and_contents_of_the_container_for_pjax_request()
    {
        $request = $this->addPjaxHeaders(new Request());

        $response = $this->middleware->handle($request, $this->next);

        $this->assertTrue($this->isPjaxReponse($response));

        $this->assertEquals('<title>Pjax title</title>Content', $response->getContent());
    }

    /**
     * @test
     */
    public function it_will_set_the_request_uri_for_a_pjax_request()
    {
        $request = $this->addPjaxHeaders(Request::create('/test'));

        $response = $this->middleware->handle($request, $this->next);

        $this->assertEquals('/test', $response->headers->get('X-PJAX-URL'));
    }

    protected function isPjaxReponse(Response $response)
    {
        return $response->headers->has('X-PJAX-URL');
    }

    protected function addPjaxHeaders(Request $request)
    {
        $request->headers->set('X-PJAX', true);
        $request->headers->set('X-PJAX-CONTAINER', '#pjax-container');

        return $request;
    }
}
