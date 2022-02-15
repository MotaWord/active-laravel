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

        if (!$config || !$config['project_id'] || !$config['widget_id'] || !$config['token']
            || ($config['serve_enable'] && !$config['serve_url'])
            || (!$config['serve_enable'] && !$config['active_js_url'])) {
            throw new Exception('Token, project ID and widget ID are required for MotaWord Active.');
        }

        if ($config['serve_enable']) {
            return "<script src=\"${config['serve_url']}/js/${config['project_id']}-${config['widget_id']}.js\" ".($config['token'] ? 'data-token="'.$config['token'].'"' : '')." crossorigin async></script>";
        } else {
            return "<script src=\"${config['active_js_url']}\" ".($config['token'] ? 'data-token="'.$config['token'].'"' : '')." ".($config['project_id'] ? 'data-project-id="'.$config['project_id'].'"' : '')." ".($config['widget_id'] ? 'data-widget-id="'.$config['widget_id'].'"' : '')." crossorigin async></script>";
        }
    }
}
