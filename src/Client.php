<?php
namespace Tackk\Pushit;

class Client {
	protected $connection = null;

	public function __construct(ConnectionInterface $connection) {
		$this->connection = $connection;
	}

	public function trigger($channels, $event, $data, $socketId = null) {
		$channels = is_array($channels) ? $channels : [$channels];



		if ( ! is_string($data)) {
			$data = json_encode($data);
		}

		$postParams = [
			'name' => $event,
			'data' => $data,
			'channels' => $channels
		];
		if ( ! is_null($socketId)) {
			$postParams['socket_id'] = $socketId;
		}

		$postBody = json_encode($postParams);

		$this->connection->sendRequest('POST', '/events', [], $postBody);

		return true;
	}


	public function channel($channel, $info = null) {
		$params = [];

		if ( ! empty($info)) {
			$params['info'] = $info;
		}

		return $this->connection->sendRequest('GET', "/channel/{$channel}", $params);
	}

	public function channels($prefixFilter = null, $info = null) {
		$params = [];
		if ( ! empty($prefixFilter)) {
			$params['filter_by_prefix'] = $prefixFilter;
		}
		if ( ! empty($info)) {
			$params['info'] = $info;
		}

		$response = $this->connection->sendRequest('GET', '/channels', $params);
		if (isset($response['channels'])) {
			return $response['channels'];
		}

		return [];
	}
}
