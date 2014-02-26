<?php
namespace Tackk\Pushit;

use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\BadResponseException;

class Connection implements ConnectionInterface {
	protected $guzzle = null;
	protected $apiBase = 'http://api.pusherapp.com';
	protected $appId;
	protected $authKey;
	protected $authSecret;
	protected $options = [
		'timeout'         => 20,
		'connect_timeout' => 1.5,
		'debug'           => false,
	];

	public function __construct($appId, $authKey, $authSecret, $options = []) {
		$this->appId = $appId;
		$this->authKey = $authKey;
		$this->authSecret = $authSecret;
		$this->options = array_merge($this->options, $options);

		$this->guzzle = new GuzzleClient($this->apiBase);
	}


	public function sendRequest($method, $uri, $queryParams = [], $bodyData = null) {
		$uri = $this->generateUri($uri);
		$queryParams = $this->generateQueryParams($method, $uri, $queryParams, $bodyData);
		try {
			$request = $this->guzzle->{$method}($uri);
			$request->getQuery()->merge($queryParams);
			$request->setBody($bodyData, 'application/json');

			$response = $request->send();
			return $response->json();
		} catch (BadResponseException $e) {
			$response = $e->getResponse();
			switch ($response->getStatus()) {
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

	protected function generateQueryParams($method, $uri, $queryParams, $bodyData = null) {
		$params = [
			'auth_key' => $this->authKey,
			'auth_timestamp' => time(),
			'auth_version' => '1.0',
		];

		if ( ! is_null($bodyData)) {
			$params['body_md5'] = md5($bodyData);
		}

		$params = array_merge($params, $queryParams);
		ksort($params);

		$signatureString = implode("\n", [
			strtoupper($method),
			$uri,
			urldecode(http_build_query(array_change_key_case($params, CASE_LOWER)))
		]);
		$params['auth_signature'] = hash_hmac('sha256', $signatureString, $this->authSecret);
		ksort($params);

		return $params;
	}

	protected function generateUri($path) {
		$path = ltrim($path, '/');
		return "/apps/{$this->appId}/{$path}";
	}
}
