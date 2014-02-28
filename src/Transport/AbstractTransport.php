<?php
namespace Tackk\Shover\Transport;

use Tackk\Shover\Credentials;
use Tackk\Shover\Request;

abstract class AbstractTransport {
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
	public function __construct(Credentials $credentials, $options = []) {
		$this->credentials = $credentials;
		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Generates a full API Url.
	 * 
	 * @param  string $path The URI Path
	 * @return string
	 */
	protected function apiUrl($path) {
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
