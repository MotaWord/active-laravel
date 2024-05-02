<?php

namespace MotaWord\Active;

use Closure;
use Exception;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response;

class ActiveServeMiddleware
{
    /**
     * The Guzzle Client that sends GET requests to the prerender server.
     *
     * @var Guzzle
     */
    private $client;

    /**
     * This token will be provided via the X-Prerender-Token header.
     *
     * @var string
     */
    private $prerenderToken;

    /**
     * List of crawler user agents that will be.
     *
     * @var array
     */
    private $crawlerUserAgents;

    /**
     * URI whitelist for pre-rendering pages only on this list.
     *
     * @var array
     */
    private $whitelist;

    /**
     * URI blacklist for pre-rendering pages that are not on the list.
     *
     * @var array
     */
    private $blacklist;

    /**
     * Base URI to make the prerender requests.
     *
     * @var string
     */
    private $prerenderUri;

    /**
     * Return soft 3xx and 404 HTTP codes.
     *
     * @var string
     */
    private $returnSoftHttpCodes;

    /**
     * Creates a new PrerenderMiddleware instance.
     */
    public function __construct(?Guzzle $client = null)
    {
        $this->returnSoftHttpCodes = config('motaword.active.soft_http_codes');

        if ($this->returnSoftHttpCodes) {
            $this->client = $client;
        } elseif ($client) {
            // Workaround to avoid following redirects
            $config = $client->getConfig();
            $config['allow_redirects'] = false;
            $this->client = new Guzzle($config);
        }

        $config = config('motaword.active');

        $this->prerenderUri = $config['serve_url'];
        $this->crawlerUserAgents = $config['crawler_user_agents'];
        $this->prerenderToken = $config['token'];
        $this->whitelist = $config['whitelist'];
        $this->blacklist = $config['blacklist'];
    }

    /**
     * Handles a request and prerender if it should, otherwise call the next middleware.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->shouldShowPrerenderedPage($request)) {
            try {
                $serveResponse = $this->getActiveServePageResponse($request);
            } catch (\Exception $e) {
                return $next($request);
            }

            if ($serveResponse) {
                $statusCode = $serveResponse->getStatusCode();

                if (!$this->returnSoftHttpCodes && $statusCode >= 300 && $statusCode < 400) {
                    $headers = $serveResponse->getHeaders();

                    return Redirect::to(array_change_key_case($headers, CASE_LOWER)['location'][0], $statusCode);
                }

                return $this->buildSymfonyResponseFromGuzzleResponse($serveResponse);
            }
        }

        return $next($request);
    }

    /**
     * Returns whether the request must be pre-rendered.
     */
    public function shouldShowPrerenderedPage(Request $request): bool
    {
        if ($request->get('forceActiveServe')) {
            return true;
        }

        if (!$this->isUrlAllowed($request)) {
            return false;
        }

        $userAgent = strtolower($request->server->get('HTTP_USER_AGENT'));
        if (!$userAgent) {
            return false;
        }

        if (!$request->isMethod('GET')) {
            return false;
        }

        // prerender if a crawler is detected
        foreach ($this->crawlerUserAgents as $crawlerUserAgent) {
            if (Str::contains($userAgent, strtolower($crawlerUserAgent))) {
                return true;
            }
        }

        return false;
    }

    public function isUrlAllowed(Request $request): bool
    {
        $requestUri = $request->getRequestUri();
        $referer = $request->headers->get('Referer');
        // Check if the current URL is allowed to be served via Serve.
        $path = parse_url($request->getPathInfo(), PHP_URL_PATH);
        $matches = $this->doesPathMatchPatterns($path, config('motaword.active.blacklist', []));
        if ($matches) {
            return false;
        }

        $matches = $this->doesPathMatchPatterns($path, config('motaword.active.whitelist', []));
        if ($matches) {
            return true;
        }

        // only check whitelist if it is not empty
        if ($this->whitelist) {
            if ($this->isListed($requestUri, $this->whitelist)) {
                return true;
            } else {
                if (!$this->blacklist) {
                    return false;
                }
            }
        }

        // only check blacklist if it is not empty
        if ($this->blacklist) {
            $uris[] = $requestUri;

            // we also check for a blacklisted referer
            if ($referer) {
                $uris[] = $referer;
            }

            if ($this->isListed($uris, $this->blacklist)) {
                return false;
            }
        }

        return true;
    }

    public function doesPathMatchPatterns($path, $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $locales = config('motaword.active.locale_codes', []);
            $pattern = str_replace('*', '.*', $pattern);
            $patternWithLocale = '#^(^([/]?)(' . implode('|', $locales) . '))?(' . $pattern . '|' . strtoupper($pattern) . ')\z#u';
            preg_match($patternWithLocale, $path, $matches);
            $isMatch = isset($matches[1]) && $matches[1] ? $matches[1] : ($matches[0] ?? null);
            if ($isMatch) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prerender the page and return the Guzzle Response.
     * @throws GuzzleException
     */
    private function getActiveServePageResponse(Request $request): ?ResponseInterface
    {
        $headers = [
            'User-Agent' => $request->server->get('HTTP_USER_AGENT'),
        ];

        if ($this->prerenderToken) {
            $headers['X-MotaWord-Token'] = $this->prerenderToken;
        }

        $protocol = $request->isSecure() ? 'https' : 'http';

        try {
            // Return the Guzzle Response
            $host = $request->getHost();
            $port = $request->getPort();
            // no need to specify the port if it is one of the default ports of http and https.
            if (($protocol === 'https' && (int)$port === 443) || ($protocol === 'http' && (int)$port === 80)) {
                $port = null;
            }
            $path = $request->Path();
            // Fix "//" 404 error
            if ($path === '/') {
                $path = '';
            }

            $encodedUrl = urlencode($protocol.'://'.$host.($port ? ':'.$port : '').'/'.$path);

            return $this->client->get($this->prerenderUri . '/' . $encodedUrl, compact('headers'));
        } catch (Exception $exception) {
            if ($exception instanceof RequestException) {
                if (!$this->returnSoftHttpCodes && !empty($exception->getResponse()) && $exception->getResponse()->getStatusCode() === 404) {
                    abort(404);
                }
            }

            // In case of an exception, we only throw the exception if we are in debug mode. Otherwise,
            // we return null and the handle() method will just pass the request to the next middleware
            // and we do not show a pre-rendered page.
            if (config('app.debug')) {
                throw $exception;
            }

            return null;
        }
    }

    /**
     * Convert a Guzzle Response to a Symfony Response.
     */
    private function buildSymfonyResponseFromGuzzleResponse(ResponseInterface $prerenderedResponse): Response
    {
        return (new HttpFoundationFactory)->createResponse($prerenderedResponse);
    }

    /**
     * Check whether one or more needles are in the given list
     */
    private function isListed($needles, array $list): bool
    {
        $needles = Arr::wrap($needles);

        foreach ($list as $pattern) {
            foreach ($needles as $needle) {
                if (Str::is($pattern, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
