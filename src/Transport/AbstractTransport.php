<?php
namespace Tackk\Shover\Transport;

use Tackk\Shover\Credentials;
use Tackk\Shover\Request;

abstract class AbstractTransport
{
    /**
     * The Credentials
     * @var Tackk\Shover\Credentials
     */
    protected $credentials = null;

    /**
     * The base API URL
     * @var string
     */
    protected $apiBase = 'http://api.pusherapp.com';

    /**
     * The Connection options.
     * @var array
     */
    protected $options = [
        'timeout'         => 20,
        'connect_timeout' => 5,
    ];

    /**
     * @param Credentials $credentials The Credentuals.
     * @param array       $options     The transport options.
     */
    public function __construct(Credentials $credentials, $options = [])
    {
        $this->credentials = $credentials;
        $this->options     = array_merge($this->options, $options);
    }

    /**
     * Signs the Request so it can be dispatched.
     *
     * @param  Request $request The Request to Sign
     */
    public function signRequest(Request $request)
    {
        if ($request->isSigned()) {
            return $this;
        }

        $query                   = $request->getQuery();
        $query['auth_key']       = $this->credentials->getAuthKey();
        $query['auth_timestamp'] = time();
        $query['auth_version']   = '1.0';

        if (! is_null($request->getBody())) {
            $query['body_md5'] = md5($request->getBody());
        }

        ksort($query);
        $queryString             = urldecode(http_build_query(array_change_key_case($query, CASE_LOWER)));
        $stringToSign            = $request->getMethod()."\n".$request->getUri()."\n".$queryString;
        $query['auth_signature'] = hash_hmac('sha256', $stringToSign, $this->credentials->getAuthSecret());

        $request->setQuery($query);
        $request->setSigned(true);

        return $this;
    }

    /**
     * Marshals a Request so it can be dispatched.
     *
     * @param  Request $request The Request to be marshaled.
     */
    public function marshal(Request $request)
    {
        $request->seturi($this->buildPath($request->getUri()));
        $this->signRequest($request);
    }

    /**
     * Builds the full Uri for the given path.
     *
     * @param  string $path The URI Path.
     * @return string
     */
    protected function buildPath($path)
    {
        return "/apps/{$this->credentials->getAppId()}/".ltrim($path, '/');
    }

    /**
     * Generates a full API Url.
     *
     * @param  string $path The URI Path
     * @return string
     */
    protected function fullUrl($path)
    {
        return $this->apiBase.$path;
    }

    /**
     * Dispaches a Request.
     *
     * @param  Request $request The Request
     * @return array The Response array
     */
    abstract public function dispatch(Request $request);
}
