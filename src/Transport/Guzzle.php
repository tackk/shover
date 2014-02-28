<?php
namespace Tackk\Shover\Transport;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;
use Tackk\Shover\Credentials;
use Tackk\Shover\Request;
use Tackk\Shover\AuthenticationException;
use Tackk\Shover\ForbiddenException;
use Tackk\Shover\GeneralException;

class Guzzle extends AbstractTransport {
	/**
	 * The Guzzle Client
	 * @var Guzzle\Http\Client
	 */
	protected $guzzle = null;

	/**
	 * @param Credentials $credentials The Credentuals.
	 * @param array       $options     The transport options.
	 */
	public function __construct(Credentials $credentials, $options = []) {
		parent::__construct($credentials, $options);

		$this->guzzle = new GuzzleClient($this->apiBase, [
			'request.options' => [
				'timeout' => $this->options['timeout'],
				'connect_timeout' => $this->options['connect_timeout'],
			]
		]);
	}

	/**
	 * Dispaches a Request.
	 *
	 * @param  Request $request The Request
	 * @return array The Response array
	 */
	public function dispatch(Request $request) {
		$request->setCredentials($this->credentials)
		        ->prepare();

		try {
			$method = $request->getMethod();
			$guzzleRequest = $this->guzzle->{$method}($request->getUri());
			$guzzleRequest->getQuery()->merge($request->getQuery());

			if ($method != 'GET') {
				$guzzleRequest->setBody($request->getBody(), 'application/json');
			}

			$response = $guzzleRequest->send();
			return $response->json();
		} catch (BadResponseException $e) {
			$response = $e->getResponse();
			switch ($response->getStatusCode()) {
				case 401:
					throw new AuthenticationException((string) $response->getBody());
					break;
				case 403:
					throw new ForbiddenException('Forbidden: app disabled or over message quota');
					break;
				default:
					throw new GeneralException((string) $response->getBody());
			}
		}
	}
}
