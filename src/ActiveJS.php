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

        $injection = '';
        $scriptUrl = '';
        $pageOptimizedAttribute = '';

        if ($config['serve_page_optimized']) {
            $pageOptimizedAttribute = ' referrerpolicy="unsafe-url" ';
        }

        if ($config['serve_enable']) {
            $scriptUrl = "${config['serve_url']}/js/${config['project_id']}-${config['widget_id']}.js";
            $injection .= "<script src=\"$scriptUrl\" ".($config['token'] ? 'data-token="'.$config['token'].'"' : '').' crossorigin async'.$pageOptimizedAttribute.'></script>';
        } else {
            $scriptUrl = $config['active_js_url'];
            $injection .= "<script src=\"$scriptUrl\" ".($config['token'] ? 'data-token="'.$config['token'].'"' : '').' '.($config['project_id'] ? 'data-project-id="'.$config['project_id'].'"' : '').' '.($config['widget_id'] ? 'data-widget-id="'.$config['widget_id'].'"' : '').' crossorigin async'.$pageOptimizedAttribute.'></script>';
        }

        if ($config['optimize_for_browsers']) {
            $preload = '';
            if ($config['serve_enable']) {
                $preload = '<link rel="preconnect" href="https://serve.motaword.com"/>'.$preload;
            } else {
                $preload = '<link rel="preconnect" href="https://active-js.motaword.com"/>'.$preload;
            }
            $preload = $preload."<link rel=\"preload\" href=\"$scriptUrl\" as=\"script\" importance=\"high\" crossorigin".$pageOptimizedAttribute."/><link rel=\"preconnect\" href=\"https://api.motaword.com\"/>";
            $injection = $preload.$injection;
        }

        return $injection;
    }
}
