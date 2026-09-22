<?php

namespace MotaWord\Active\Tests;

class ActiveMiddlewareTest extends TestCase
{
    /** @test */
    public function it_should_prerender_page_on_get_request()
    {
        $this->allowSymfonyUserAgent();

        $this->get('/test-middleware')
            ->assertHeader('X-Renderer', 'MotaWord Active Serve')
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_not_prerender_page_when_user_agent_does_not_in_list()
    {
        $this->get('/test-middleware')
            ->assertSuccessful()
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_prerender_when_user_agent_is_part_of_crawler_user_agents()
    {
        $this->get('/test-middleware', ['User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',])
            ->assertHeader('X-Renderer', 'MotaWord Active Serve')
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_prerender_page_with_url_in_whitelist()
    {
        $this->allowSymfonyUserAgent();
        config()->set('motaword.active.whitelist', ['/test-middleware*']);

        $this->get('/test-middleware')
            ->assertHeader('X-Renderer', 'MotaWord Active Serve')
            ->assertSuccessful();
    }

    /** @test */
    public function is_should_not_prerender_page_in_blacklist()
    {
        $this->allowSymfonyUserAgent();
        config()->set('motaword.active.blacklist', ['/test-middleware*']);

        $this->get('/test-middleware')
            ->assertSuccessful()
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_prerender_page_when_query_param_value_ends_with_blacklisted_extension()
    {
        $this->allowSymfonyUserAgent();
        config()->set('motaword.active.blacklist', ['*.ai']);

        $this->get('/test-middleware?ref=examples.tely.ai')
            ->assertHeader('X-Renderer', 'MotaWord Active Serve')
            ->assertSuccessful();
    }

    /** @test */
    public function it_should_not_prerender_asset_path_with_blacklisted_extension()
    {
        $this->allowSymfonyUserAgent();
        config()->set('motaword.active.blacklist', ['*.ai']);

        $this->get('/downloads/logo.ai')
            ->assertHeaderMissing('X-Renderer');
    }

    /** @test */
    public function it_should_still_blacklist_by_query_string_with_non_extension_patterns()
    {
        $this->allowSymfonyUserAgent();
        config()->set('motaword.active.blacklist', ['*preview=true*']);

        $this->get('/test-middleware?preview=true')
            ->assertSuccessful()
            ->assertHeaderMissing('X-Renderer')
            ->assertSee('GET - Success');
    }

    /** @test */
    public function it_should_not_prerender_page_on_non_get_request()
    {
        $this->allowSymfonyUserAgent();

        $this->post('/test-middleware')
            ->assertSuccessful()
            ->assertSee('Success');
    }

    /** @test */
    public function it_should_not_prerender_page_when_missing_user_agent()
    {
        $this->get('/test-middleware', ['User-Agent' => null])
            ->assertHeaderMissing('prerender.io-mock')
            ->assertSee('GET - Success');
    }

    private function allowSymfonyUserAgent()
    {
        config()->set('motaword.active.crawler_user_agents', ['symfony']);
    }
}
