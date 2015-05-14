<?php

use Tackk\Shover\Credentials;

class ClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Tackk\Shover\Client
     */
    private $client;

    public function setUp()
    {
        $credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
        $this->client = new \Tackk\Shover\Client(new \Tackk\Shover\Transport\Blackhole($credentials));
    }

    /**
     * @expectedException \Tackk\Shover\GeneralException
     */
    public function testInvalidChannelThrowsException()
    {
        $this->client->trigger('foo:', 'event', []);
    }

    /**
     * @expectedException \Tackk\Shover\GeneralException
     */
    public function testInvalidChannelsThrowsException()
    {
        $this->client->trigger(['foo', 'bar\n:'], 'event', []);
    }

    /**
     * @expectedException \Tackk\Shover\GeneralException
     */
    public function testInvalidSocketIdThrowsException()
    {
        $this->client->trigger('foo', 'event', [], 'hellothere');
    }
}
