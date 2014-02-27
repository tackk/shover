# Pushit

A simple PHP interface for the [Pusher API](http://pusher.com/docs/rest_api).

## Example

``` php
<?php
use Tackk\Pushit\Credentials;
use Tackk\Pushit\Connection;
use Tackk\Pushit\Client;

require 'vendor/autoload.php';

$credentials = new Credentials('3', '278d425bdf160c739803', '7ad3773142a6692b25b8');
$connection = new Connection($credentials);
$client = new Client($connection);

$client->trigger('test_channel', 'send', ['message' => 'test']);
```
