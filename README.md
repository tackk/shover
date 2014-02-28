# Shover

A lightweight PHP interface for the [Pusher API](http://pusher.com/docs/rest_api).

## Transports

All transports must extend the `Tackk\Shover\Transport\AbstractTransport`.

Two (2) transports are included by default:

* `Tackk\Shover\Transport\Curl` - Uses the PHP Curl extension.
* `Tackk\Shover\Transport\Guzzle` - Uses [Guzzle](http://guzzlephp.org).

### Curl

``` php
<?php
use Tackk\Shover\Credentials;
use Tackk\Shover\Transport\Curl;
use Tackk\Shover\Client;

require 'vendor/autoload.php';

$credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
$transport = new Curl($credentials);
$client = new Client($transport);

$client->trigger('test_channel', 'send', ['message' => 'test']);
```

### Guzzle

**Note: You must have Guzzle installed.**

``` php
<?php
use Tackk\Shover\Credentials;
use Tackk\Shover\Transport\Guzzle;
use Tackk\Shover\Client;

require 'vendor/autoload.php';

$credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
$transport = new Guzzle($credentials);
$client = new Client($transport);

$client->trigger('test_channel', 'send', ['message' => 'test']);
```
