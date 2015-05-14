<?php
namespace Tackk\Shover;

use Tackk\Shover\Transport\AbstractTransport;

class Client
{
    /**
     * Holds the Shover AbstractTransport object.
     * @var TransportInterface
     */
    protected $transport = null;

    /**
     * @param AbstractTransport $transport The Pusher Transport.
     */
    public function __construct(AbstractTransport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Triggers an Event on the given Channel(s).
     *
     * @param  string|array $channels The Channel(s) to send on.
     * @param  string       $event    The even name.
     * @param  string|array $data     The data to send.
     * @param  int          $socketId Exclude this socket id from receiving the
     *                                message.
     * @return bool
     * @throws GeneralException
     */
    public function trigger($channels, $event, $data, $socketId = null)
    {
        $channels = is_array($channels) ? $channels : [$channels];

        $this->validateSocketId($socketId);
        $this->validateChannels($channels);

        if (! is_string($data)) {
            $data = json_encode($data);
        }

        $body = [
            'name'     => $event,
            'data'     => $data,
            'channels' => $channels,
        ];
        if (! is_null($socketId)) {
            $body['socket_id'] = $socketId;
        }

        $body = json_encode($body);

        $this->transport->dispatch(new Request('POST', '/events', [], $body));

        return true;
    }

    /**
     * Gets information on a sepcific Channel.
     *
     * @param  string $channel The channel to get information about.
     * @param  string $info    Comma separated list of attributes which should
     *                         be returned for the channel
     * @return array
     */
    public function channel($channel, $info = null)
    {
        $this->validateChannel($channel);

        $params = [];
        if (! empty($info)) {
            $params['info'] = $info;
        }

        return $this->transport->dispatch(new Request('GET', "/channel/{$channel}", $params));
    }

    /**
     * Gets a list of occupied channels.
     *
     * @param  string $prefixFilter Filter the returned channels by a specific
     *                              prefix
     * @param  string $info         A comma separated list of attributes which
     *                              should be returned for each channel.
     * @return array
     */
    public function channels($prefixFilter = null, $info = null)
    {
        $params = [];
        if (! empty($prefixFilter)) {
            $params['filter_by_prefix'] = $prefixFilter;
        }
        if (! empty($info)) {
            $params['info'] = $info;
        }

        return $this->transport->dispatch(new Request('GET', '/channels', $params));
    }

    /**
     * Gets the user ids currently subscribed to a given presence channel.
     *
     * @param  string $channel The channel to get information about.
     * @return array
     * @throws GeneralException
     */
    public function users($channel)
    {
        $this->validateChannel($channel);

        if (strpos($channel, 'presence-') !== 0) {
            throw new GeneralException('You can only get the users of a Presence channel.');
        }

        return $this->transport->dispatch(new Request('GET', "/channel/{$channel}/users"));
    }

    /**
     * Creates a socket signature.
     *
     * @param string $channel
     * @param string $socketId
     * @param array|bool  $customData
     * @return string
     */
    public function socketSignature($channel, $socketId, $customData = false)
    {
        $this->validateSocketId($socketId);
        $this->validateChannel($channel);

        return json_encode($this->transport->getSocketSignature($channel, $socketId, $customData));
    }

    /**
     * Creates a presence socket signature.
     *
     * @param string $channel
     * @param string $socketId
     * @param int    $userId
     * @param array|bool  $userInfo
     * @return string
     */
    public function presenceSignature($channel, $socketId, $userId, $userInfo = false)
    {
        $userData = ['user_id' => $userId];
        if ($userInfo !== false) {
            $userData['user_info'] = $userInfo;
        }

        return $this->socketSignature($channel, $socketId, json_encode($userData));
    }

    /**
     * Validates the given Socket ID.
     * @param $socketId
     * @throws GeneralException
     */
    private function validateSocketId($socketId)
    {
        if ($socketId !== null && ! preg_match('/\A\d+\.\d+\z/', $socketId)) {
            throw new GeneralException('Invalid SocketId: '.$socketId);
        }
    }

    /**
     * Validate a channel name.
     * @param $channel
     * @throws GeneralException
     */
    private function validateChannel($channel)
    {
        if ( ! preg_match('/\A[-a-zA-Z0-9_=@,.;]+\z/', $channel)) {
            throw new GeneralException('Invalid Channel Name: '.$channel);
        }
    }

    /**
     * Validates an array of channel names.
     * @param array $channels
     * @throws GeneralException
     */
    private function validateChannels(array $channels)
    {
        if (count($channels) > 100) {
            throw new GeneralException('You can only send to a maximum of 100 channels at a time.');
        }

        array_walk($channels, [$this, 'validateChannel']);
    }
}
