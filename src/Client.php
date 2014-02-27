<?php
namespace Tackk\Pushit;

class Client {
	/**
	 * Holds the Pushit ConnectionInterface object.
	 * @var ConnectionInterface
	 */
	protected $connection = null;

	/**
	 * @param ConnectionInterface $connection The Pusher Connection.
	 */
	public function __construct(ConnectionInterface $connection) {
		$this->connection = $connection;
	}

	/**
	 * Triggers an Event on the given Channel(s).
	 *
	 * @param  string|array $channels The Channel(s) to send on.
	 * @param  string       $event    The even name.
	 * @param  string|array $data     The data to send.
	 * @param  int          $socketId Exclude this socket id from receiving the message.
	 * @return bool
	 * @throws GeneralException
	 */
	public function trigger($channels, $event, $data, $socketId = null) {
		$channels = is_array($channels) ? $channels : [$channels];

		if (count($channels) > 100) {
			throw new GeneralException('You can only send to a maximum of 100 channels at a time.');
		}

		if ( ! is_string($data)) {
			$data = json_encode($data);
		}

		$body = [
			'name' => $event,
			'data' => $data,
			'channels' => $channels,
		];
		if ( ! is_null($socketId)) {
			$body['socket_id'] = $socketId;
		}

		$body = json_encode($body);

		$this->connection->dispatch(new Request('POST', '/events', [], $body));

		return true;
	}

	/**
	 * Gets information on a sepcific Channel.
	 *
	 * @param  string $channel The channel to get information about.
	 * @param  string $info    Comma separated list of attributes which should be returned for the channel
	 * @return array
	 */
	public function channel($channel, $info = null) {
		$params = [];

		if ( ! empty($info)) {
			$params['info'] = $info;
		}

		return $this->connection->dispatch(new Request('GET', "/channel/{$channel}", $params));
	}

	/**
	 * Gets a list of occupied channels.
	 *
	 * @param  string $prefixFilter Filter the returned channels by a specific prefix
	 * @param  string $info         A comma separated list of attributes which should be returned for each channel.
	 * @return array
	 */
	public function channels($prefixFilter = null, $info = null) {
		$params = [];
		if ( ! empty($prefixFilter)) {
			$params['filter_by_prefix'] = $prefixFilter;
		}
		if ( ! empty($info)) {
			$params['info'] = $info;
		}

		return $this->connection->dispatch(new Request('GET', '/channels', $params));
	}

	/**
	 * Gets the user ids currently subscribed to a given presence channel.
	 *
	 * @param  string $channel The channel to get information about.
	 * @return array
	 */
	public function users($channel) {
		if (strpos($channel, 'presence-') !== 0) {
			throw new GeneralException('You can only get the users of a Presence channel.');
		}
		return $this->connection->dispatch(new Request('GET', "/channel/{$channel}/users"));
	}

}
