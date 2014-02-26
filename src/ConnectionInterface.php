<?php
namespace Tackk\Pushit;

interface ConnectionInterface {
	public function sendRequest($method, $uri, $queryParams = [], $bodyData = null);
}
