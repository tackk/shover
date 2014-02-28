<?php

use Tackk\Shover\Credentials;

class CredentialsTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
	}

	public function testCanBeInstantiated() {
		$this->assertInstanceOf('Tackk\Shover\Credentials', $this->credentials);
	}

	public function testGetAppId() {
		$this->assertEquals('3', $this->credentials->getAppId());
	}

	public function testGetAuthKey() {
		$this->assertEquals('278d425bdf160c739803', $this->credentials->getAuthKey());
	}

	public function testGetAuthSecret() {
		$this->assertEquals('7ad3773142a6692b25b8', $this->credentials->getAuthSecret());
	}
}
