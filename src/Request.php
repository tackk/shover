<?php
namespace Tackk\Pushit;

class Request {
	/**
	 * The HTTP Method
	 * @var string
	 */
	protected $method = 'GET';

	/**
	 * The URI
	 * @var string
	 */
	protected $uri = null;

	/**
	 * The Query Parameters
	 * @var array
	 */
	protected $query = [];

	/**
	 * The Request Body
	 * @var string
	 */
	protected $body = null;

	/**
	 * THe Credentials Object
	 * @var Credentials
	 */
	protected $credentials = null;

	/**
	 * @param string $method The HTTP Method
	 * @param string $uri    The URI Path
	 * @param array  $query  The Query to send.
	 * @param string $body   The Request Body
	 */
	public function __construct($method, $uri, $query = [], $body = null) {
		$this->setMethod($method);
		$this->setUri($uri);
		$this->setQuery($query);
		$this->setBody($body);
	}

	/**
	 * Set's the Credentials.
	 * 
	 * @param Credentials $credentials The Credentials
	 */
	public function setCredentials($credentials) {
		$this->credentials = $credentials;
		return $this;
	}

	/**
	 * Gets the HTTP method.
	 *
	 * @return string The HTTP Method
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Set's the HTTP method.
	 * 
	 * @param string $method The HTTP Method
	 */
	public function setMethod($method) {
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Gets the URI.
	 *
	 * @return string The URI
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * Set's the Uri.
	 * 
	 * @param string $uri The URI
	 */
	public function setUri($uri) {
		$this->uri = $uri;
		return $this;
	}

	/**
	 * Get the Query
	 *
	 * @return array
	 */
	public function getQuery() {
		return $this->query;
	}
	
	/**
	 * Set the Query
	 * 
	 * @param array $query The Query
	 */
	public function setQuery($query) {
		$this->query = $query;
		return $this;
	}

	/**
	 * Get the Request Body
	 *
	 * @return string
	 */
	public function getBody() {
	    return $this->body;
	}
	
	/**
	 * Set the Request Body
	 * 
	 * @param string $body The Request Body
	 */
	public function setBody($body) {
	    $this->body = $body;
	    return $this;
	}

	/**
	 * Prepares the Request so it can be dispatched.
	 *
	 * @return Request
	 */
	public function prepare() {
		if ( ! $this->credentials instanceof Credentials) {
			throw new RuntimeException('You must set the Credentials for the Request.');
		}
		$this->prepareUri();

		$this->query['auth_key'] = $this->credentials->getAuthKey();
		$this->query['auth_timestamp'] = time();
		$this->query['auth_version'] = '1.0';

		if ( ! is_null($this->body)) {
			$this->query['body_md5'] = md5($this->body);
		}

		$this->sign();

		return $this;
	}

	/**
	 * Signs the Request for Pusher.
	 *
	 * @see    http://pusher.com/docs/rest_api#authentication
	 * @return Request
	 */
	protected function sign() {
		ksort($this->query);
		$queryString = urldecode(http_build_query(array_change_key_case($this->query, CASE_LOWER)));
		$stringToSign = $this->method."\n".$this->uri."\n".$queryString;
		$this->query['auth_signature'] = hash_hmac('sha256', $stringToSign, $this->credentials->getAuthSecret());
		return $this;
	}

	/**
	 * Perpare the URI.
	 *
	 * @return Request
	 */
	protected function prepareUri() {
		$this->uri = '/apps/'.$this->credentials->getAppId().'/'.ltrim($this->uri, '/');
		return $this;
	}
}
