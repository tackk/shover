<?php
namespace Tackk\Shover\Transport;

use Tackk\Shover\AuthenticationException;
use Tackk\Shover\Credentials;
use Tackk\Shover\ForbiddenException;
use Tackk\Shover\GeneralException;
use Tackk\Shover\Request;

class Curl extends AbstractTransport
{
    /**
     * The Curl Handle
     * @var object
     */
    protected $handle = null;

    /**
     * @param Credentials $credentials The Credentuals.
     * @param array       $options     The transport options.
     */
    public function __construct(Credentials $credentials, $options = [])
    {
        parent::__construct($credentials, $options);
        $this->handle = curl_init();
    }

    /**
     * Dispaches a Request.
     *
     * @param  Request $request The Request
     * @return array The Response array
     */
    public function dispatch(Request $request)
    {
        $this->marshal($request);

        $this->setupCurl($request);
        $response = curl_exec($this->handle);

        switch (curl_getinfo($this->handle, CURLINFO_HTTP_CODE)) {
            case 200:
                return json_decode($response, true);
            case 401:
                throw new AuthenticationException($response);
            case 403:
                throw new ForbiddenException('Forbidden: app disabled or over message quota');
            default:
                throw new GeneralException($response);
        }
    }

    /**
     * Setup the Curl Options.
     *
     * @param  Request $request The Request
     */
    protected function setupCurl(Request $request)
    {
        curl_setopt_array($this->handle, [
            CURLOPT_URL            => $this->fullUrl($request->getUri().'?'.$request->getQuery(true)),
            CURLOPT_TIMEOUT        => $this->options['timeout'],
            CURLOPT_CONNECTTIMEOUT => $this->options['connect_timeout'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json'
            ]
        ]);

        switch ($request->getMethod()) {
            case 'GET':
                curl_setopt($this->handle, CURLOPT_HTTPGET, true);
                curl_setopt($this->handle, CURLOPT_POST, false);
                break;
            case 'POST':
                curl_setopt($this->handle, CURLOPT_HTTPGET, false);
                curl_setopt($this->handle, CURLOPT_POST, true);
                curl_setopt($this->handle, CURLOPT_POSTFIELDS, $request->getBody());
                break;
        }
    }
}
