<?php

namespace MotaWord\Active\Tests;

use Illuminate\Http\Request;
use MotaWord\Active\ActiveJS;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class ActiveJSTest extends TestCase
{
    /** @test */
    public function it_should_generate_script()
    {
        config()->set('motaword.active.token', 'test');
        config()->set('motaword.active.project_id', 1);
        config()->set('motaword.active.widget_id', 1);
        config()->set('motaword.active.serve_enable', true);
        $result = ActiveJS::generate();
        $this->assertNotEmpty($result);
        $this->assertEquals('<link rel="preconnect" href="https://serve.motaword.com"/><link rel="preload" href="https://serve.motaword.com/js/1-1.js" as="script" importance="high" crossorigin referrerpolicy="unsafe-url" /><link rel="preconnect" href="https://api.motaword.com"/><script src="https://serve.motaword.com/js/1-1.js" data-token="test" crossorigin async referrerpolicy="unsafe-url" ></script>', $result);
    }

    /** @test */
    public function it_should_generate_staging_script()
    {
        config()->set('motaword.active.token', 'test');
        config()->set('motaword.active.project_id', 1);
        config()->set('motaword.active.widget_id', 1);
        config()->set('motaword.active.serve_enable', true);
        config()->set('motaword.active.serve_url', 'https://serve.staging.motaword.com');
        $result = ActiveJS::generate();
        $this->assertNotEmpty($result);
        $this->assertEquals('<link rel="preconnect" href="https://serve.motaword.com"/><link rel="preload" href="https://serve.staging.motaword.com/js/1-1.js" as="script" importance="high" crossorigin referrerpolicy="unsafe-url" /><link rel="preconnect" href="https://api.motaword.com"/><script src="https://serve.staging.motaword.com/js/1-1.js" data-token="test" crossorigin async referrerpolicy="unsafe-url" ></script>', $result);
    }

    /** @test */
    public function it_should_skip_ignored_url()
    {
        config()->set('motaword.active.token', 'test');
        config()->set('motaword.active.project_id', 1);
        config()->set('motaword.active.widget_id', 1);
        config()->set('motaword.active.serve_enable', true);

        config()->set('motaword.active.blacklist', ['/about', '/blog', '/blog/*', '/']);

        // will not render

        $symfonyRequest = SymfonyRequest::create('/');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/about');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/blog');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/blog/hello-world');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/tr/blog/hello-world');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/pt-BR/blog/hello-world');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertFalse(ActiveJS::isAllowed($request));
        $this->assertEmpty(ActiveJS::generate($request));

        // will render

        $symfonyRequest = SymfonyRequest::create('/hello');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertTrue(ActiveJS::isAllowed($request));
        $this->assertNotEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/random/blog/hello-world');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertTrue(ActiveJS::isAllowed($request));
        $this->assertNotEmpty(ActiveJS::generate($request));

        $symfonyRequest = SymfonyRequest::create('/r/blog/hello-world');
        $request = Request::createFromBase($symfonyRequest);
        $this->assertTrue(ActiveJS::isAllowed($request));
        $this->assertNotEmpty(ActiveJS::generate($request));
    }
}
