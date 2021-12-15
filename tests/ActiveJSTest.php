<?php

namespace MotaWord\Active\Tests;

use MotaWord\Active\ActiveJS;

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
        $this->assertEquals('<script src="https://serve.motaword.com/js/1-1.js" crossorigin async></script>', $result);
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
        $this->assertEquals('<script src="https://serve.staging.motaword.com/js/1-1.js" crossorigin async></script>', $result);
    }


}
