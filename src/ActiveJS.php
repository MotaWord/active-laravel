<?php

namespace MotaWord\Active;

use Exception;

class ActiveJS
{
    /**
     * @throws Exception
     */
    public static function generate(): string
    {
        $config = config('motaword.active');

        if (!$config || !$config['token'] || !$config['project_id'] || !$config['widget_id'] || !$config['serve_url']) {
            throw new Exception('Token, project ID and widget ID are required for MotaWord Active.');
        }

        return "<script src=\"${config['serve_url']}/js/${config['project_id']}-${config['widget_id']}.js\" crossorigin async></script>";
    }
}
