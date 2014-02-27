<?php
namespace Tackk\Pushit;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;

class Connection implements ConnectionInterface {
	/**
	 * The Guzzle Client
	 * @var Guzzle\Http\Client
	 */
	protected $guzzle = null;

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
	 * Build the connection.
	 *
	 * @param string|int $appId      The Pusher App ID.
	 * @param string     $authKey    The Pusher Auth Key.
	 * @param string     $authSecret The Pusher Auth Secret.
	 * @param array      $options    The connection options.
	 */
	public function __construct(Credentials $credentials, $options = []) {
		$this->credentials = $credentials;
		$this->options = array_merge($this->options, $options);

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
