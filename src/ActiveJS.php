<?php

namespace MotaWord\Active;

use Exception;
use Illuminate\Http\Request;

class ActiveJS
{
    /**
     * @throws Exception
     */
    public static function generate(?Request $request = null): string
    {
        if ($request) {
            if (!static::isAllowed($request)) {
                return '';
            }
        }
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
            $scriptUrl = $config['serve_url'].'/js/'.$config['project_id'].'-'.$config['widget_id'].'.js';
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
            $preload = $preload."<link rel=\"preload\" href=\"$scriptUrl\" as=\"script\" onload=\"document.dispatchEvent(new Event('ACTIVE_LOADED'))\" importance=\"high\" crossorigin".$pageOptimizedAttribute.'/><link rel="preconnect" href="https://api.motaword.com"/>';
            $injection = $preload.$injection;
        }

        return $injection;
    }

    public static function isServeAllowed(Request $request): bool
    {
        return static::isAllowed($request, true);
    }


    public static function isAllowed(Request $request, $forServe = false): bool
    {
        // temporarily override blacklist/whitelist config with whitelist_activejs_only config.
        if (!$forServe) {
            $appConfig = app('config');
            $mwConfig = config('motaword.active');
            $previousWhitelist = $mwConfig['whitelist'];
            $previousBlacklist = $mwConfig['blacklist'];
            // this is a hack for active-laravel package to enable ActiveJS for pages that are in blacklist for Active Serve.
            if (!empty($mwConfig['whitelist_activejs_only'])) {
                $appConfig->set('motaword.active.whitelist', $mwConfig['whitelist_activejs_only']);
                $appConfig->set('motaword.active.blacklist', array_diff($mwConfig['blacklist'], $mwConfig['whitelist_activejs_only']));
            }
        }

        $middleware = new ActiveServeMiddleware(null);
        $isAllowed = $middleware->isUrlAllowed($request);

        if (!$forServe) {
            $appConfig->set('motaword.active.whitelist', $previousWhitelist);
            $appConfig->set('motaword.active.blacklist', $previousBlacklist);
        }

        return $isAllowed;
    }
}
