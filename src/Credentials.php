<?php
namespace Tackk\Pushit;

class Credentials {
	/**
	 * The Pusher App ID
	 * @var string
	 */
	protected $appId;

	/**
	 * The Pusher Auth Key
	 * @var string
	 */
	protected $authKey;

	/**
	 * The Pusher Auth Secret
	 * @var string
	 */
	protected $authSecret;

	/**
	 * @param string $appId      Pusher App ID
	 * @param string $authKey    Pusher Auth key
	 * @param string $authSecret Pusher Auth Secret
	 */
	public function __construct($appId, $authKey, $authSecret) {
		$this->appId =$appId;
		$this->authKey = $authKey;
		$this->authSecret = $authSecret;
	}

	/**
	 * Get the App ID.
	 *
	 * @return string
	 */
	public function getAppId() {
	    return $this->appId;
	}
	
	/**
	 * Get the Auth Key
	 *
	 * @return string
	 */
	public function getAuthKey() {
	    return $this->authKey;
	}

	/**
	 * Get the Auth Secret
	 *
	 * @return string
	 */
	public function getAuthSecret() {
	    return $this->authSecret;
	}
}
