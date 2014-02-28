<?php

use Tackk\Shover\Credentials;

class CredentialsTest extends PHPUnit_Framework_TestCase {
	public function testCanBeInstantiated() {
		$credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');

		$this->assertInstanceOf('Tackk\Shover\Credentials', $credentials);
	}
}
